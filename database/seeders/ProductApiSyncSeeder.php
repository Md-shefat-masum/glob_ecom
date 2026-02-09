<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductVariantCombination;
use App\Models\ProductStockVariantGroup;
use App\Models\ProductStockVariantsGroupKey;
use App\Models\MediaFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class ProductApiSyncSeeder extends Seeder
{
    private $apiUrl = 'https://app-back-end.wardahlife.com/api/single-product-full-info';
    private $serialCodeStart = 10001;
    private $currentSerialCode = 10001;
    
    /**
     * Run the database seeds.
     *
     * @return void
     * 
     * product sync steps
. insert info into products
    . code = as serial code start from 10001
    . barcode = code from api
     
. in our data base 
    . product_stock_variant_groups->id = 1 = Color
    . product_stock_variant_groups->id = 2 = Size
    . check color_code exist and size_name in product_stock_variants_group_keys , if exist > get product_stock_variants_group_keys.id is create one
    . product_variant_combinations here product variants data will set
        set only :
            product_id bigint(20) UN 
            combination_key varchar(255) 
            variant_values longtext 
            price double 
            discount_price double 
            additional_price double 
            low_stock_alert int(11)
            image varchar(255)

. product_stocks will track product stock single or variant based stocks . it is tracks always the closing stock of a product.
    . Table: product_stocks
        Columns:
        id bigint(20) UN AI PK 
        product_warehouse_id bigint(20) UN 
        product_warehouse_room_id bigint(20) UN 
        product_warehouse_room_cartoon_id bigint(20) UN 
        product_supplier_id bigint(20) UN 
        product_purchase_order_id bigint(20) UN 
        product_id bigint(20) UN 
        variant_combination_id bigint(20) UN 
        has_variant tinyint(1) 
        variant_combination_key varchar(255) 
        variant_sku varchar(100) 
        variant_barcode varchar(50) => if no in response set a unique one following bar code rules
        variant_data longtext 
        variant_price decimal(10,2) 
        variant_discount_price decimal(10,2) 
        date date 
        qty mediumint(8) UN 
        purchase_price decimal(10,2) 
        creator bigint(20) UN 
        slug varchar(255) 
        status enum('active','inactive') 
        created_at timestamp 
        updated_at timestamp

. track stock events: product_stock_logs this this table track all events on a product. increment or decrement
    Table: product_stock_logs
        Columns:
        id bigint(20) UN AI PK 
        warehouse_id bigint(20) UN 
        product_id bigint(20) UN 
        variant_combination_id bigint(20) UN 
        has_variant tinyint(1) 
        variant_combination_key varchar(255) 
        variant_sku varchar(100) 
        variant_data longtext 
        product_name varchar(255) 
        product_sales_id bigint(20) UN 
        product_purchase_id bigint(20) UN 
        product_return_id bigint(20) UN 
        quantity int(11) 
        type enum('sales','purchase','return','initial','transfer','waste','manual add') 
        description text 
        creator bigint(20) UN 
        slug varchar(50) 
        status tinyint(3) UN 
        created_at timestamp 
        updated_at timestamp


summary :  
    products table: store product info and single product all informations.
    product_variant_combinations: only stores prouct variant inforamtions price and image
    product_stocks: track single and variant products closing stock amount.
    product_stock_logs: track stock events.
     */
    public function run()
    {
        $this->command->info('Starting Product API Sync...');
        
        try {
            // Truncate tables first
            $this->truncateTables();
            
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
            
            $products = $data['data'];
            $this->command->info('Found ' . count($products) . ' products to sync');
            
            // Reset serial code counter
            $this->currentSerialCode = $this->serialCodeStart;
            
            // First, sync variant groups and keys from all products
            $this->syncVariantGroupsAndKeys($products);
            
            foreach ($products as $productData) {
                $this->syncProduct($productData);
            }
            
            $this->command->info('Product sync completed successfully!');
            
        } catch (\Exception $e) {
            $this->command->error('Error syncing products: ' . $e->getMessage());
            Log::error('ProductApiSyncSeeder Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Truncate the required tables
     */
    private function truncateTables()
    {
        $this->command->info('Truncating tables...');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('product_stock_logs')->truncate();
        DB::table('product_stocks')->truncate();
        DB::table('product_variant_combinations')->truncate();
        DB::table('products')->truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('✓ Tables truncated');
    }
    
    /**
     * Sync variant groups and keys from all products
     * Only handles Color (id=1) and Size (id=2)
     */
    private function syncVariantGroupsAndKeys($products)
    {
        $this->command->info('Syncing variant groups and keys...');
        
        // Get Color group (id=1) and Size group (id=2)
        $colorGroup = ProductStockVariantGroup::find(1);
        $sizeGroup = ProductStockVariantGroup::find(2);
        
        if (!$colorGroup || $colorGroup->name !== 'Color') {
            $this->command->error('Color group (id=1) not found!');
            return;
        }
        
        if (!$sizeGroup || $sizeGroup->name !== 'Size') {
            $this->command->error('Size group (id=2) not found!');
            return;
        }
        
        $colorKeys = [];
        $sizeKeys = [];
        
        // Collect all unique variant values from all products
        foreach ($products as $productData) {
            if (!empty($productData['variants']) && is_array($productData['variants'])) {
                foreach ($productData['variants'] as $variant) {
                    // Color with color_code
                    if (!empty($variant['color_name']) && !empty($variant['color_code'])) {
                        $colorKeys[$variant['color_name']] = [
                            'key_name' => $variant['color_name'],
                            'key_value' => $variant['color_code'],
                        ];
                    }
                    
                    // Size
                    if (!empty($variant['size_name'])) {
                        $sizeKeys[$variant['size_name']] = [
                            'key_name' => $variant['size_name'],
                            'key_value' => null,
                        ];
                    }
                }
            }
        }
        
        // Create Color keys (group_id = 1)
        $sortOrder = 0;
        foreach ($colorKeys as $keyData) {
            ProductStockVariantsGroupKey::firstOrCreate(
                [
                    'group_id' => 1,
                    'key_name' => $keyData['key_name'],
                ],
                [
                    'key_value' => $keyData['key_value'],
                    'sort_order' => $sortOrder++,
                    'status' => 1,
                ]
            );
        }
        
        // Create Size keys (group_id = 2)
        $sortOrder = 0;
        foreach ($sizeKeys as $keyData) {
            ProductStockVariantsGroupKey::firstOrCreate(
                [
                    'group_id' => 2,
                    'key_name' => $keyData['key_name'],
                ],
                [
                    'key_value' => $keyData['key_value'],
                    'sort_order' => $sortOrder++,
                    'status' => 1,
                ]
            );
        }
        
        $this->command->info('✓ Variant groups and keys synced');
    }
    
    /**
     * Sync a single product
     */
    private function syncProduct($data)
    {
        DB::beginTransaction();
        
        try {
            $this->command->info("Syncing product: {$data['name']}");
            
            // Generate serial code starting from 10001
            $serialCode = $this->currentSerialCode++;
            
            // Map API data to Product model
            $product = new Product();
            $product->name = $data['name'];
            $product->slug = $data['slug'];
            $product->code = (string)$serialCode; // Serial code starting from 10001
            // Note: barcode field not available in Product model, API code stored in variant_barcode for variants
            $product->category_id = $data['category_id'] ? (int)$data['category_id'] : null;
            $product->subcategory_id = $data['subcategory_id'] ? (int)$data['subcategory_id'] : null;
            $product->childcategory_id = $data['childcategory_id'] ? (int)$data['childcategory_id'] : null;
            $product->brand_id = $data['brand_id'] ? (int)$data['brand_id'] : null;
            $product->model_id = $data['model_id'] ? (int)$data['model_id'] : null;
            $product->unit_id = $data['unit_id'] ? (int)$data['unit_id'] : null;
            $product->flag_id = $data['flag_id'] ? (int)$data['flag_id'] : null;
            
            // Pricing
            $product->price = (float)($data['price'] ?? 0);
            $product->discount_price = (float)($data['discount_price'] ?? 0);
            $product->stock = 0; // Stock will be calculated from product_stocks table
            
            // Store original stock for initial entry
            $originalStock = (int)($data['stock'] ?? 0);
            
            // Content
            $product->short_description = $data['short_description'] ?? null;
            $product->description = $data['description'] ?? null;
            $product->specification = $data['specification'] ?? null;
            $product->warrenty_policy = $data['warrenty_policy'] ?? null;
            $product->tags = $data['tags'] ?? null;
            $product->video_url = $data['video_url'] ?? null;
            
            // Meta
            $product->meta_title = $data['meta_title'] ?? null;
            $product->meta_keywords = $data['meta_keywords'] ?? null;
            $product->meta_description = $data['meta_description'] ?? null;
            
            // Flags
            $product->status = (int)($data['status'] ?? 1);
            $product->has_variant = (int)($data['has_variant'] ?? 0);
            $product->is_demo = (int)($data['is_demo'] ?? 0);
            $product->is_package = (int)($data['is_package'] ?? 0);
            
            // Save product first to get ID
            $product->save();
            
            // Handle main product image
            if (!empty($data['image_full_url'])) {
                $imageFileName = !empty($data['image']) ? basename($data['image']) : basename($data['image_full_url']);
                $imagePath = $this->downloadImage($data['image_full_url'], $product->id, $imageFileName);
                if ($imagePath) {
                    $product->image = $imagePath;
                    $product->save();
                }
            } elseif (!empty($data['image'])) {
                // If only relative path is provided, try to construct full URL
                $imageUrl = 'https://app-back-end.wardahlife.com/' . ltrim($data['image'], '/');
                $imagePath = $this->downloadImage($imageUrl, $product->id, basename($data['image']));
                if ($imagePath) {
                    $product->image = $imagePath;
                    $product->save();
                }
            }
            
            // Handle multiple images
            if (!empty($data['images']) && is_array($data['images'])) {
                $galleryPaths = [];
                foreach ($data['images'] as $imageUrl) {
                    if (!empty($imageUrl)) {
                        $imagePath = $this->downloadImage($imageUrl, $product->id, basename($imageUrl));
                        if ($imagePath) {
                            $galleryPaths[] = $imagePath;
                        }
                    }
                }
                if (!empty($galleryPaths)) {
                    $product->multiple_images = $galleryPaths;
                    $product->save();
                }
            }
            
            // Handle variants
            if ($product->has_variant && !empty($data['variants']) && is_array($data['variants'])) {
                $this->syncVariants($product, $data['variants']);
            } else {
                // For non-variant products, create initial stock entry
                if ($originalStock > 0) {
                    $this->createInitialStockEntry($product, $originalStock);
                }
            }
            
            DB::commit();
            $this->command->info("✓ Product synced: {$product->name} (ID: {$product->id}, Code: {$product->code})");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("✗ Failed to sync product: {$data['name']} - " . $e->getMessage());
            Log::error('Product sync error', [
                'product_name' => $data['name'] ?? 'Unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Download image from URL and save to public/uploads/product/{product_id}/
     * Also registers the image in MediaFile table for proper URL generation
     */
    private function downloadImage($imageUrl, $productId, $originalFileName)
    {
        try {
            // Create directory if it doesn't exist
            $uploadDir = public_path("uploads/products/{$productId}");
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }
            
            // Get file extension from original filename or URL
            $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            if (empty($extension)) {
                // Try to get from URL
                $urlPath = parse_url($imageUrl, PHP_URL_PATH);
                $extension = pathinfo($urlPath, PATHINFO_EXTENSION) ?: 'jpg';
            }
            
            // Keep original filename (without path)
            $fileName = basename($originalFileName);
            
            // If filename doesn't have extension, add it
            if (!pathinfo($fileName, PATHINFO_EXTENSION)) {
                $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '.' . $extension;
            }
            
            $relativePath = "uploads/products/{$productId}/{$fileName}";
            $filePath = $uploadDir . '/' . $fileName;
            
            // Check if file already exists in MediaFile table
            $existingMediaFile = MediaFile::where('file_path', $relativePath)->first();
            if ($existingMediaFile && File::exists($filePath)) {
                // File already registered and exists, return the path
                return $relativePath;
            }
            
            // Download image with response validation
            $response = Http::timeout(30)->get($imageUrl);
            
            if (!$response->successful()) {
                $this->command->warn("Failed to download image (HTTP {$response->status()}): {$imageUrl}");
                return null;
            }
            
            $imageContent = $response->body();
            
            if (empty($imageContent)) {
                $this->command->warn("Empty response for image: {$imageUrl}");
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
                $this->command->warn("URL does not return an image (Content-Type: {$contentType}): {$imageUrl}");
                return null;
            }
            
            // Save file
            File::put($filePath, $imageContent);
            
            // Get file size
            $fileSize = File::size($filePath);
            
            // Get image dimensions using Intervention Image
            $width = null;
            $height = null;
            try {
                // Double check it's an image before processing
                $detectedMimeType = mime_content_type($filePath);
                if (strpos($detectedMimeType, 'image/') === 0) {
                    $image = Image::make($filePath);
                    $width = $image->width();
                    $height = $image->height();
                }
            } catch (\Exception $e) {
                // If image processing fails, continue without dimensions
                // Only warn if it's not a known HTML error
                if (strpos($e->getMessage(), 'text/html') === false) {
                    $this->command->warn("Could not get image dimensions: " . $e->getMessage());
                }
            }
            
            // Get MIME type
            $mimeType = mime_content_type($filePath);
            if (!$mimeType || strpos($mimeType, 'image/') !== 0) {
                $mimeType = $contentType ?: 'image/' . $extension;
            }
            
            // Register in MediaFile table
            MediaFile::create([
                'folder_path' => "uploads/products/{$productId}",
                'file_path' => $relativePath,
                'domain_url' => url('/'),
                'full_url' => asset($relativePath),
                'file_name' => $fileName,
                'original_name' => $originalFileName,
                'size' => $fileSize,
                'mime_type' => $mimeType,
                'extension' => $extension,
                'width' => $width,
                'height' => $height,
                'disk' => 'public',
                'uploader_type' => null,
                'uploader_id' => null,
                'file_type' => 'image',
                'is_temp' => false,
                'temp_token' => null,
                'metadata' => [
                    'source' => 'api_sync',
                    'product_id' => $productId,
                ],
            ]);
            
            // Return relative path from public directory
            return $relativePath;
            
        } catch (\Exception $e) {
            $this->command->warn("Error downloading image {$imageUrl}: " . $e->getMessage());
            Log::error('Image download error', [
                'url' => $imageUrl,
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * Sync product variants
     * Only sets: product_id, combination_key, variant_values, price, discount_price, additional_price, low_stock_alert, image
     */
    private function syncVariants($product, $variants)
    {
        foreach ($variants as $variantData) {
            try {
                // Build variant_values array
                $variantValues = [];
                
                if (!empty($variantData['color_name'])) {
                    $variantValues['color'] = $variantData['color_name'];
                }
                
                if (!empty($variantData['size_name'])) {
                    $variantValues['size'] = $variantData['size_name'];
                }
                
                // Add other variant attributes if present
                if (!empty($variantData['region_name'])) {
                    $variantValues['region'] = $variantData['region_name'];
                }
                
                if (!empty($variantData['sim_type'])) {
                    $variantValues['sim_type'] = $variantData['sim_type'];
                }
                
                if (!empty($variantData['storage_type'])) {
                    $variantValues['storage_type'] = $variantData['storage_type'];
                }
                
                if (!empty($variantData['device_condition'])) {
                    $variantValues['device_condition'] = $variantData['device_condition'];
                }
                
                // Generate combination key
                $combinationKey = ProductVariantCombination::generateCombinationKey($variantValues);
                
                // Store original stock value for initial entry
                $originalStock = (int)($variantData['stock'] ?? 0);
                
                // Prepare variant data - ONLY required fields
                $variantDataToSave = [
                    'product_id' => $product->id,
                    'combination_key' => $combinationKey,
                    'variant_values' => $variantValues,
                    'price' => !empty($variantData['price']) ? (float)$variantData['price'] : null,
                    'discount_price' => !empty($variantData['discounted_price']) ? (float)$variantData['discounted_price'] : null,
                    'additional_price' => null, // Not in API, set to null
                    'low_stock_alert' => null, // Not in API, set to null
                ];
                
                // Handle variant image
                if (!empty($variantData['image_full_url'])) {
                    $imageFileName = !empty($variantData['image']) ? basename($variantData['image']) : basename($variantData['image_full_url']);
                    $imagePath = $this->downloadImage($variantData['image_full_url'], $product->id, $imageFileName);
                    if ($imagePath) {
                        $variantDataToSave['image'] = $imagePath;
                    }
                } elseif (!empty($variantData['image'])) {
                    // If only relative path is provided, try to construct full URL
                    $imageUrl = 'https://app-back-end.wardahlife.com/' . ltrim($variantData['image'], '/');
                    $imagePath = $this->downloadImage($imageUrl, $product->id, basename($variantData['image']));
                    if ($imagePath) {
                        $variantDataToSave['image'] = $imagePath;
                    }
                }
                
                // Create variant combination
                $variant = ProductVariantCombination::create($variantDataToSave);
                
                // Create initial stock entry for this variant
                if ($originalStock > 0) {
                    $this->createInitialStockEntry($product, $originalStock, true, $variant, $variantData);
                }
                
            } catch (\Exception $e) {
                $this->command->warn("Failed to sync variant: " . $e->getMessage());
                Log::error('Variant sync error', [
                    'product_id' => $product->id,
                    'variant_data' => $variantData,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    /**
     * Create initial stock entry in product_stocks and product_stock_logs
     */
    private function createInitialStockEntry($product, $quantity, $hasVariant = false, $variant = null, $variantData = null)
    {
        if ($quantity <= 0) {
            return;
        }
        
        try {
            // Prepare stock data
            $stockData = [
                'product_id' => $product->id,
                'has_variant' => $hasVariant ? 1 : 0,
                'qty' => $quantity,
                'date' => now()->format('Y-m-d'),
                'status' => 'active',
                'creator' => 1, // System user
                'slug' => Str::slug($product->name . '-initial-' . time()) . '-' . uniqid(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Prepare log data
            $logData = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'has_variant' => $hasVariant ? 1 : 0,
                'quantity' => $quantity,
                'type' => 'initial',
                'status' => 1,
                'creator' => 1, // System user
                'slug' => Str::slug($product->name . '-initial-log-' . time()) . '-' . uniqid(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            if ($hasVariant && $variant) {
                // Get variant values from variant model
                $variantValues = $variant->variant_values ?? [];
                
                // Add variant information to stock data
                $stockData['variant_combination_key'] = $variant->combination_key;
                $stockData['variant_sku'] = null; // Not in API
                $stockData['variant_barcode'] = $this->generateVariantBarcode($product, $variant, $variantData);
                $stockData['variant_data'] = is_array($variantValues) ? json_encode($variantValues) : $variantValues;
                $stockData['variant_price'] = $variant->price;
                $stockData['variant_discount_price'] = $variant->discount_price;
                
                // Add variant information to log data
                $logData['variant_combination_key'] = $variant->combination_key;
                $logData['variant_sku'] = null; // Not in API
                $logData['variant_data'] = is_array($variantValues) ? json_encode($variantValues) : $variantValues;
                
                // Check if variant_combination_id column exists
                if (DB::getSchemaBuilder()->hasColumn('product_stocks', 'variant_combination_id')) {
                    $stockData['variant_combination_id'] = $variant->id;
                }
                
                if (DB::getSchemaBuilder()->hasColumn('product_stock_logs', 'variant_combination_id')) {
                    $logData['variant_combination_id'] = $variant->id;
                }
            }
            
            // Insert stock entry
            DB::table('product_stocks')->insert($stockData);
            
            // Insert stock log entry
            DB::table('product_stock_logs')->insert($logData);
            
        } catch (\Exception $e) {
            $this->command->warn("Failed to create initial stock entry: " . $e->getMessage());
            Log::error('Initial stock entry error', [
                'product_id' => $product->id,
                'variant_id' => $variant ? $variant->id : null,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Generate barcode for variant if not provided
     * Following barcode rules: use product code + variant identifier
     */
    private function generateVariantBarcode($product, $variant, $variantData = null)
    {
        // If variant has a code in API, use it
        if ($variantData && !empty($variantData['code'])) {
            return $variantData['code'];
        }
        
        // Generate unique barcode: product code + variant combination key hash
        $hash = substr(md5($variant->combination_key), 0, 6);
        return $product->code . '-' . strtoupper($hash);
    }
    
}

