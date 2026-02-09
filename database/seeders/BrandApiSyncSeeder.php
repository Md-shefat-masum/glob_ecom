<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Brand;
use Illuminate\Support\Facades\Log;

class BrandApiSyncSeeder extends Seeder
{
    private $apiUrl = 'https://app-back-end.wardahlife.com/api/get/all/brands';
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting Brand API Sync...');
        
        try {
            // Fetch data from API
            $response = Http::timeout(60)->get($this->apiUrl);
            
            if (!$response->successful()) {
                $this->command->error('Failed to fetch data from API. Status: ' . $response->status());
                return;
            }
            
            $data = $response->json();
            
            if (!isset($data['success']) || !$data['success'] || !isset($data['data'])) {
                $this->command->error('Invalid API response format');
                return;
            }
            
            $brands = $data['data'];
            $this->command->info('Found ' . count($brands) . ' brands to sync');
            
            $syncedBrands = 0;
            
            foreach ($brands as $brandData) {
                try {
                    // Sync brand
                    $brand = $this->syncBrand($brandData);
                    $syncedBrands++;
                    
                } catch (\Exception $e) {
                    $this->command->error('Error syncing brand ID ' . ($brandData['id'] ?? 'unknown') . ': ' . $e->getMessage());
                    Log::error('Brand sync error: ' . $e->getMessage(), [
                        'brand_data' => $brandData,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
            
            $this->command->info("✓ Sync completed!");
            $this->command->info("  - Brands: {$syncedBrands}");
            
        } catch (\Exception $e) {
            $this->command->error('Fatal error: ' . $e->getMessage());
            Log::error('Brand API Sync Fatal Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Sync brand
     */
    private function syncBrand($brandData)
    {
        // Use DB::table()->updateOrInsert to maintain same ID from API
        $brandId = $brandData['id'];
        
        // Download logo if available
        $logoPath = null;
        if (!empty($brandData['logo'])) {
            $logoPath = $this->downloadLogo($brandData['logo']);
        }
        
        // Download banner if available
        $bannerPath = null;
        if (!empty($brandData['banner'])) {
            $bannerPath = $this->downloadBanner($brandData['banner']);
        }
        
        $updateData = [
            'name' => $brandData['name'],
            'logo' => $logoPath ?? null,
            'banner' => $bannerPath ?? null,
            'categories' => $brandData['categories'] ?? null,
            'subcategories' => $brandData['subcategories'] ?? null,
            'slug' => $brandData['slug'],
            'status' => $brandData['status'] == '1' || $brandData['status'] === 1 ? 1 : 0,
            'featured' => $brandData['featured'] == '1' || $brandData['featured'] === 1 ? 1 : 0,
            'serial' => $brandData['serial'] ?? 1,
            'updated_at' => $brandData['updated_at'] ?? now(),
        ];
        
        // Check if brand exists
        $exists = DB::table('brands')->where('id', $brandId)->exists();
        
        if ($exists) {
            // Update existing brand
            DB::table('brands')->where('id', $brandId)->update($updateData);
        } else {
            // Insert new brand with explicit ID
            $updateData['id'] = $brandId;
            $updateData['created_at'] = $brandData['created_at'] ?? now();
            DB::table('brands')->insert($updateData);
        }
        
        $brand = Brand::find($brandId);
        $this->command->info("  ✓ Brand: {$brand->name} (ID: {$brand->id})");
        
        return $brand;
    }
    
    /**
     * Download brand logo from API and save to uploads/brand_images/
     */
    private function downloadLogo($logoPath)
    {
        try {
            // Create directory if it doesn't exist
            $uploadDir = public_path("uploads/brand_images");
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }
            
            // Construct full URL if relative path is provided
            $logoUrl = $logoPath;
            if (!filter_var($logoPath, FILTER_VALIDATE_URL)) {
                // It's a relative path, construct full URL
                $logoUrl = 'https://app-back-end.wardahlife.com/' . ltrim($logoPath, '/');
            }
            
            // Get filename from path
            $fileName = basename($logoPath);
            
            // Get file extension
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            if (empty($extension)) {
                // Try to get from URL
                $urlPath = parse_url($logoUrl, PHP_URL_PATH);
                $extension = pathinfo($urlPath, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '.' . $extension;
            }
            
            $relativePath = "uploads/brand_images/{$fileName}";
            $filePath = $uploadDir . '/' . $fileName;
            
            // Check if file already exists
            if (File::exists($filePath)) {
                $this->command->info("    Logo already exists: {$fileName}");
                return $relativePath;
            }
            
            // Download logo with response validation
            $response = Http::timeout(30)->get($logoUrl);
            
            if (!$response->successful()) {
                $this->command->warn("Failed to download logo (HTTP {$response->status()}): {$logoUrl}");
                return null;
            }
            
            $imageContent = $response->body();
            
            if (empty($imageContent)) {
                $this->command->warn("Empty response for logo: {$logoUrl}");
                return null;
            }
            
            // Check if response is actually an image (not HTML error page)
            $contentType = $response->header('Content-Type', '');
            $isImage = strpos($contentType, 'image/') === 0;
            
            // Also check first few bytes for image magic numbers
            $imageMagicNumbers = [
                "\xFF\xD8\xFF", // JPEG
                "\x89\x50\x4E\x47", // PNG
                "GIF87a", // GIF87
                "GIF89a", // GIF89
                "BM", // BMP
                "RIFF", // WebP (RIFF....WEBP)
            ];
            
            $isImageByContent = false;
            foreach ($imageMagicNumbers as $magic) {
                if (strpos($imageContent, $magic) === 0) {
                    $isImageByContent = true;
                    break;
                }
            }
            
            // If not an image, skip it
            if (!$isImage && !$isImageByContent) {
                $this->command->warn("URL does not return an image (Content-Type: {$contentType}): {$logoUrl}");
                return null;
            }
            
            // Save file
            File::put($filePath, $imageContent);
            
            $this->command->info("    ✓ Downloaded logo: {$fileName}");
            
            return $relativePath;
            
        } catch (\Exception $e) {
            $this->command->error("Error downloading logo {$logoPath}: " . $e->getMessage());
            Log::error('Brand logo download error: ' . $e->getMessage(), [
                'logo_path' => $logoPath,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
    
    /**
     * Download brand banner from API and save to uploads/brand_images/
     */
    private function downloadBanner($bannerPath)
    {
        try {
            // Create directory if it doesn't exist
            $uploadDir = public_path("uploads/brand_images");
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }
            
            // Construct full URL if relative path is provided
            $bannerUrl = $bannerPath;
            if (!filter_var($bannerPath, FILTER_VALIDATE_URL)) {
                // It's a relative path, construct full URL
                $bannerUrl = 'https://app-back-end.wardahlife.com/' . ltrim($bannerPath, '/');
            }
            
            // Get filename from path
            $fileName = basename($bannerPath);
            
            // Get file extension
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            if (empty($extension)) {
                // Try to get from URL
                $urlPath = parse_url($bannerUrl, PHP_URL_PATH);
                $extension = pathinfo($urlPath, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '.' . $extension;
            }
            
            $relativePath = "uploads/brand_images/{$fileName}";
            $filePath = $uploadDir . '/' . $fileName;
            
            // Check if file already exists
            if (File::exists($filePath)) {
                $this->command->info("    Banner already exists: {$fileName}");
                return $relativePath;
            }
            
            // Download banner with response validation
            $response = Http::timeout(30)->get($bannerUrl);
            
            if (!$response->successful()) {
                $this->command->warn("Failed to download banner (HTTP {$response->status()}): {$bannerUrl}");
                return null;
            }
            
            $imageContent = $response->body();
            
            if (empty($imageContent)) {
                $this->command->warn("Empty response for banner: {$bannerUrl}");
                return null;
            }
            
            // Check if response is actually an image (not HTML error page)
            $contentType = $response->header('Content-Type', '');
            $isImage = strpos($contentType, 'image/') === 0;
            
            // Also check first few bytes for image magic numbers
            $imageMagicNumbers = [
                "\xFF\xD8\xFF", // JPEG
                "\x89\x50\x4E\x47", // PNG
                "GIF87a", // GIF87
                "GIF89a", // GIF89
                "BM", // BMP
                "RIFF", // WebP (RIFF....WEBP)
            ];
            
            $isImageByContent = false;
            foreach ($imageMagicNumbers as $magic) {
                if (strpos($imageContent, $magic) === 0) {
                    $isImageByContent = true;
                    break;
                }
            }
            
            // If not an image, skip it
            if (!$isImage && !$isImageByContent) {
                $this->command->warn("URL does not return an image (Content-Type: {$contentType}): {$bannerUrl}");
                return null;
            }
            
            // Save file
            File::put($filePath, $imageContent);
            
            $this->command->info("    ✓ Downloaded banner: {$fileName}");
            
            return $relativePath;
            
        } catch (\Exception $e) {
            $this->command->error("Error downloading banner {$bannerPath}: " . $e->getMessage());
            Log::error('Brand banner download error: ' . $e->getMessage(), [
                'banner_path' => $bannerPath,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}

