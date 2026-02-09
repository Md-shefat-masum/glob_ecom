<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ChildCategory;
use Illuminate\Support\Facades\Log;

class CategoryApiSyncSeeder extends Seeder
{
    private $apiUrl = 'https://app-back-end.wardahlife.com/api/get/all/categories/with/subcategories/and/childcategories';
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting Category API Sync...');
        
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
            
            $categories = $data['data'];
            $this->command->info('Found ' . count($categories) . ' categories to sync');
            
            $syncedCategories = 0;
            $syncedSubcategories = 0;
            $syncedChildCategories = 0;
            
            foreach ($categories as $categoryData) {
                try {
                    // Sync main category
                    $category = $this->syncCategory($categoryData);
                    $syncedCategories++;
                    
                    // Sync children (subcategories and child categories)
                    if (!empty($categoryData['children']) && is_array($categoryData['children'])) {
                        foreach ($categoryData['children'] as $subcategoryData) {
                            // Check if this is a subcategory (stage 2) or child category (stage 3)
                            $stage = $subcategoryData['stage'] ?? 2;
                            
                            if ($stage == 2) {
                                // This is a subcategory
                                $subcategory = $this->syncSubcategory($subcategoryData, $category->id);
                                $syncedSubcategories++;
                                
                                // Check for child categories within this subcategory
                                if (!empty($subcategoryData['children']) && is_array($subcategoryData['children'])) {
                                    foreach ($subcategoryData['children'] as $childCategoryData) {
                                        $childCategory = $this->syncChildCategory($childCategoryData, $category->id, $subcategory->id);
                                        $syncedChildCategories++;
                                    }
                                }
                            } else {
                                // This might be a direct child category (stage 3)
                                $childCategory = $this->syncChildCategory($subcategoryData, $category->id, null);
                                $syncedChildCategories++;
                            }
                        }
                    }
                    
                } catch (\Exception $e) {
                    $this->command->error('Error syncing category ID ' . ($categoryData['id'] ?? 'unknown') . ': ' . $e->getMessage());
                    Log::error('Category sync error: ' . $e->getMessage(), [
                        'category_data' => $categoryData,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
            
            $this->command->info("✓ Sync completed!");
            $this->command->info("  - Categories: {$syncedCategories}");
            $this->command->info("  - Subcategories: {$syncedSubcategories}");
            $this->command->info("  - Child Categories: {$syncedChildCategories}");
            
        } catch (\Exception $e) {
            $this->command->error('Fatal error: ' . $e->getMessage());
            Log::error('Category API Sync Fatal Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Sync category (stage 1)
     */
    private function syncCategory($categoryData)
    {
        // Use DB::table()->updateOrInsert to maintain same ID from API
        $categoryId = $categoryData['id'];
        
        // Download icon if available
        $iconPath = null;
        if (!empty($categoryData['icon'])) {
            $iconPath = $this->downloadIcon($categoryData['icon']);
        }
        
        $updateData = [
            'name' => $categoryData['name'],
            'icon' => $iconPath ?? null,
            'banner_image' => $categoryData['banner_image'] ?? null,
            'slug' => $categoryData['slug'],
            'status' => $categoryData['status'] == '1' || $categoryData['status'] === 1 ? 1 : 0,
            'featured' => $categoryData['featured'] == '1' || $categoryData['featured'] === 1 ? 1 : 0,
            'serial' => $categoryData['serial'] ?? 1,
            'show_on_navbar' => 1, // Default value
            'updated_at' => $categoryData['updated_at'] ?? now(),
        ];
        
        // Check if category exists
        $exists = DB::table('categories')->where('id', $categoryId)->exists();
        
        if ($exists) {
            // Update existing category
            DB::table('categories')->where('id', $categoryId)->update($updateData);
        } else {
            // Insert new category with explicit ID
            $updateData['id'] = $categoryId;
            $updateData['created_at'] = $categoryData['created_at'] ?? now();
            DB::table('categories')->insert($updateData);
        }
        
        $category = Category::find($categoryId);
        $this->command->info("  ✓ Category: {$category->name} (ID: {$category->id})");
        
        return $category;
    }
    
    /**
     * Sync subcategory (stage 2)
     */
    private function syncSubcategory($subcategoryData, $categoryId)
    {
        // Use DB::table()->updateOrInsert to maintain same ID from API
        $subcategoryId = $subcategoryData['id'];
        
        // Download icon if available
        $iconPath = null;
        if (!empty($subcategoryData['icon'])) {
            $iconPath = $this->downloadIcon($subcategoryData['icon']);
        }
        
        $updateData = [
            'category_id' => $categoryId,
            'name' => $subcategoryData['name'],
            'icon' => $iconPath ?? null,
            'image' => $subcategoryData['image'] ?? $subcategoryData['banner_image'] ?? null,
            'slug' => $subcategoryData['slug'],
            'status' => $subcategoryData['status'] == '1' || $subcategoryData['status'] === 1 ? 1 : 0,
            'featured' => $subcategoryData['featured'] == '1' || $subcategoryData['featured'] === 1 ? 1 : 0,
            'updated_at' => $subcategoryData['updated_at'] ?? now(),
        ];
        
        // Check if subcategory exists
        $exists = DB::table('subcategories')->where('id', $subcategoryId)->exists();
        
        if ($exists) {
            // Update existing subcategory
            DB::table('subcategories')->where('id', $subcategoryId)->update($updateData);
        } else {
            // Insert new subcategory with explicit ID
            $updateData['id'] = $subcategoryId;
            $updateData['created_at'] = $subcategoryData['created_at'] ?? now();
            DB::table('subcategories')->insert($updateData);
        }
        
        $subcategory = Subcategory::find($subcategoryId);
        $this->command->info("    ✓ Subcategory: {$subcategory->name} (ID: {$subcategory->id})");
        
        return $subcategory;
    }
    
    /**
     * Sync child category (stage 3)
     */
    private function syncChildCategory($childCategoryData, $categoryId, $subcategoryId = null)
    {
        // Use DB::table()->updateOrInsert to maintain same ID from API
        $childCategoryId = $childCategoryData['id'];
        $updateData = [
            'category_id' => $categoryId,
            'subcategory_id' => $subcategoryId ?? $childCategoryData['subcategory_id'] ?? null,
            'name' => $childCategoryData['name'],
            'slug' => $childCategoryData['slug'],
            'status' => $childCategoryData['status'] == '1' || $childCategoryData['status'] === 1 ? 1 : 0,
            'updated_at' => $childCategoryData['updated_at'] ?? now(),
        ];
        
        // Check if child category exists
        $exists = DB::table('child_categories')->where('id', $childCategoryId)->exists();
        
        if ($exists) {
            // Update existing child category
            DB::table('child_categories')->where('id', $childCategoryId)->update($updateData);
        } else {
            // Insert new child category with explicit ID
            $updateData['id'] = $childCategoryId;
            $updateData['created_at'] = $childCategoryData['created_at'] ?? now();
            DB::table('child_categories')->insert($updateData);
        }
        
        $childCategory = ChildCategory::find($childCategoryId);
        $this->command->info("      ✓ Child Category: {$childCategory->name} (ID: {$childCategory->id})");
        
        return $childCategory;
    }
    
    /**
     * Download category icon from API and save to uploads/category_images/
     */
    private function downloadIcon($iconPath)
    {
        try {
            // Create directory if it doesn't exist
            $uploadDir = public_path("uploads/category_images");
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }
            
            // Construct full URL if relative path is provided
            $iconUrl = $iconPath;
            if (!filter_var($iconPath, FILTER_VALIDATE_URL)) {
                // It's a relative path, construct full URL
                $iconUrl = 'https://app-back-end.wardahlife.com/' . ltrim($iconPath, '/');
            }
            
            // Get filename from path
            $fileName = basename($iconPath);
            
            // Get file extension
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            if (empty($extension)) {
                // Try to get from URL
                $urlPath = parse_url($iconUrl, PHP_URL_PATH);
                $extension = pathinfo($urlPath, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '.' . $extension;
            }
            
            $relativePath = "uploads/category_images/{$fileName}";
            $filePath = $uploadDir . '/' . $fileName;
            
            // Check if file already exists
            if (File::exists($filePath)) {
                $this->command->info("    Icon already exists: {$fileName}");
                return $relativePath;
            }
            
            // Download icon with response validation
            $response = Http::timeout(30)->get($iconUrl);
            
            if (!$response->successful()) {
                $this->command->warn("Failed to download icon (HTTP {$response->status()}): {$iconUrl}");
                return null;
            }
            
            $imageContent = $response->body();
            
            if (empty($imageContent)) {
                $this->command->warn("Empty response for icon: {$iconUrl}");
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
                $this->command->warn("URL does not return an image (Content-Type: {$contentType}): {$iconUrl}");
                return null;
            }
            
            // Save file
            File::put($filePath, $imageContent);
            
            $this->command->info("    ✓ Downloaded icon: {$fileName}");
            
            return $relativePath;
            
        } catch (\Exception $e) {
            $this->command->error("Error downloading icon {$iconPath}: " . $e->getMessage());
            Log::error('Category icon download error: ' . $e->getMessage(), [
                'icon_path' => $iconPath,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}

