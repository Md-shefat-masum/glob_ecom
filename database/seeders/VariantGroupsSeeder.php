<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VariantGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates comprehensive variant groups and keys for:
     * - Electronics (Storage, RAM, Screen Size, Processor, etc.)
     * - Clothing (Material, Pattern, Fit, Sleeve Length, etc.)
     * - Organic Foods (Weight, Pack Size, Type, Origin, Grade, etc.)
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        $this->command->info('🌱 Seeding variant groups...');

        // =================================================================
        // VARIANT GROUPS (Color and Size already in migration)
        // =================================================================
        
        $groups = [
            // ===== CLOTHING VARIANTS =====
            [
                'name' => 'Material',
                'slug' => 'material',
                'description' => 'Fabric/Material type - For clothing products',
                'is_fixed' => 0,
                'sort_order' => 3,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Pattern',
                'slug' => 'pattern',
                'description' => 'Design pattern - For clothing products',
                'is_fixed' => 0,
                'sort_order' => 4,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Fit',
                'slug' => 'fit',
                'description' => 'Fitting type - For clothing products',
                'is_fixed' => 0,
                'sort_order' => 5,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Sleeve Length',
                'slug' => 'sleeve_length',
                'description' => 'Sleeve length options - For clothing products',
                'is_fixed' => 0,
                'sort_order' => 6,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Neck Type',
                'slug' => 'neck_type',
                'description' => 'Neck style - For clothing products',
                'is_fixed' => 0,
                'sort_order' => 7,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            
            // ===== ELECTRONICS VARIANTS =====
            [
                'name' => 'Storage',
                'slug' => 'storage',
                'description' => 'Storage capacity - For electronics',
                'is_fixed' => 0,
                'sort_order' => 8,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'RAM',
                'slug' => 'ram',
                'description' => 'Memory capacity - For electronics',
                'is_fixed' => 0,
                'sort_order' => 9,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Screen Size',
                'slug' => 'screen_size',
                'description' => 'Display size - For electronics',
                'is_fixed' => 0,
                'sort_order' => 10,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Processor',
                'slug' => 'processor',
                'description' => 'CPU type - For electronics',
                'is_fixed' => 0,
                'sort_order' => 11,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Connectivity',
                'slug' => 'connectivity',
                'description' => 'Network connectivity - For electronics',
                'is_fixed' => 0,
                'sort_order' => 12,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Warranty',
                'slug' => 'warranty',
                'description' => 'Warranty period - For electronics',
                'is_fixed' => 0,
                'sort_order' => 13,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            
            // ===== ORGANIC FOOD VARIANTS =====
            [
                'name' => 'Weight',
                'slug' => 'weight',
                'description' => 'Product weight - For organic foods',
                'is_fixed' => 0,
                'sort_order' => 14,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Pack Size',
                'slug' => 'pack_size',
                'description' => 'Package size - For organic foods',
                'is_fixed' => 0,
                'sort_order' => 15,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Type',
                'slug' => 'type',
                'description' => 'Processing type - For organic foods',
                'is_fixed' => 0,
                'sort_order' => 16,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Origin',
                'slug' => 'origin',
                'description' => 'Product origin - For organic foods',
                'is_fixed' => 0,
                'sort_order' => 17,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Grade',
                'slug' => 'grade',
                'description' => 'Quality grade - For organic foods',
                'is_fixed' => 0,
                'sort_order' => 18,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'Freshness',
                'slug' => 'freshness',
                'description' => 'Product condition - For organic foods',
                'is_fixed' => 0,
                'sort_order' => 19,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ];

        // Insert all groups
        foreach ($groups as $group) {
            DB::table('product_stock_variant_groups')->insertOrIgnore($group);
        }

        $this->command->info('✅ Variant groups created!');
        $this->command->info('🔑 Seeding variant keys...');

        // =================================================================
        // GET GROUP IDs
        // =================================================================
        
        $materialId = DB::table('product_stock_variant_groups')->where('slug', 'material')->value('id');
        $patternId = DB::table('product_stock_variant_groups')->where('slug', 'pattern')->value('id');
        $fitId = DB::table('product_stock_variant_groups')->where('slug', 'fit')->value('id');
        $sleeveLengthId = DB::table('product_stock_variant_groups')->where('slug', 'sleeve_length')->value('id');
        $neckTypeId = DB::table('product_stock_variant_groups')->where('slug', 'neck_type')->value('id');
        
        $storageId = DB::table('product_stock_variant_groups')->where('slug', 'storage')->value('id');
        $ramId = DB::table('product_stock_variant_groups')->where('slug', 'ram')->value('id');
        $screenSizeId = DB::table('product_stock_variant_groups')->where('slug', 'screen_size')->value('id');
        $processorId = DB::table('product_stock_variant_groups')->where('slug', 'processor')->value('id');
        $connectivityId = DB::table('product_stock_variant_groups')->where('slug', 'connectivity')->value('id');
        $warrantyId = DB::table('product_stock_variant_groups')->where('slug', 'warranty')->value('id');
        
        $weightId = DB::table('product_stock_variant_groups')->where('slug', 'weight')->value('id');
        $packSizeId = DB::table('product_stock_variant_groups')->where('slug', 'pack_size')->value('id');
        $typeId = DB::table('product_stock_variant_groups')->where('slug', 'type')->value('id');
        $originId = DB::table('product_stock_variant_groups')->where('slug', 'origin')->value('id');
        $gradeId = DB::table('product_stock_variant_groups')->where('slug', 'grade')->value('id');
        $freshnessId = DB::table('product_stock_variant_groups')->where('slug', 'freshness')->value('id');

        // =================================================================
        // VARIANT KEYS - CLOTHING
        // =================================================================
        
        $this->command->info('  👕 Adding clothing variants...');
        
        $materialKeys = [
            ['group_id' => $materialId, 'key_name' => 'Cotton', 'key_value' => 'cotton', 'sort_order' => 1],
            ['group_id' => $materialId, 'key_name' => 'Polyester', 'key_value' => 'polyester', 'sort_order' => 2],
            ['group_id' => $materialId, 'key_name' => 'Linen', 'key_value' => 'linen', 'sort_order' => 3],
            ['group_id' => $materialId, 'key_name' => 'Silk', 'key_value' => 'silk', 'sort_order' => 4],
            ['group_id' => $materialId, 'key_name' => 'Wool', 'key_value' => 'wool', 'sort_order' => 5],
            ['group_id' => $materialId, 'key_name' => 'Denim', 'key_value' => 'denim', 'sort_order' => 6],
            ['group_id' => $materialId, 'key_name' => 'Leather', 'key_value' => 'leather', 'sort_order' => 7],
            ['group_id' => $materialId, 'key_name' => 'Nylon', 'key_value' => 'nylon', 'sort_order' => 8],
            ['group_id' => $materialId, 'key_name' => 'Rayon', 'key_value' => 'rayon', 'sort_order' => 9],
            ['group_id' => $materialId, 'key_name' => 'Spandex', 'key_value' => 'spandex', 'sort_order' => 10],
        ];

        $patternKeys = [
            ['group_id' => $patternId, 'key_name' => 'Solid/Plain', 'key_value' => 'solid', 'sort_order' => 1],
            ['group_id' => $patternId, 'key_name' => 'Striped', 'key_value' => 'striped', 'sort_order' => 2],
            ['group_id' => $patternId, 'key_name' => 'Checked', 'key_value' => 'checked', 'sort_order' => 3],
            ['group_id' => $patternId, 'key_name' => 'Floral', 'key_value' => 'floral', 'sort_order' => 4],
            ['group_id' => $patternId, 'key_name' => 'Printed', 'key_value' => 'printed', 'sort_order' => 5],
            ['group_id' => $patternId, 'key_name' => 'Abstract', 'key_value' => 'abstract', 'sort_order' => 6],
            ['group_id' => $patternId, 'key_name' => 'Geometric', 'key_value' => 'geometric', 'sort_order' => 7],
            ['group_id' => $patternId, 'key_name' => 'Polka Dots', 'key_value' => 'polka_dots', 'sort_order' => 8],
        ];

        $fitKeys = [
            ['group_id' => $fitId, 'key_name' => 'Slim Fit', 'key_value' => 'slim', 'sort_order' => 1],
            ['group_id' => $fitId, 'key_name' => 'Regular Fit', 'key_value' => 'regular', 'sort_order' => 2],
            ['group_id' => $fitId, 'key_name' => 'Relaxed Fit', 'key_value' => 'relaxed', 'sort_order' => 3],
            ['group_id' => $fitId, 'key_name' => 'Oversized', 'key_value' => 'oversized', 'sort_order' => 4],
            ['group_id' => $fitId, 'key_name' => 'Skinny', 'key_value' => 'skinny', 'sort_order' => 5],
            ['group_id' => $fitId, 'key_name' => 'Loose Fit', 'key_value' => 'loose', 'sort_order' => 6],
        ];

        $sleeveLengthKeys = [
            ['group_id' => $sleeveLengthId, 'key_name' => 'Sleeveless', 'key_value' => 'sleeveless', 'sort_order' => 1],
            ['group_id' => $sleeveLengthId, 'key_name' => 'Short Sleeve', 'key_value' => 'short', 'sort_order' => 2],
            ['group_id' => $sleeveLengthId, 'key_name' => '3/4 Sleeve', 'key_value' => 'three_quarter', 'sort_order' => 3],
            ['group_id' => $sleeveLengthId, 'key_name' => 'Full Sleeve', 'key_value' => 'full', 'sort_order' => 4],
        ];

        $neckTypeKeys = [
            ['group_id' => $neckTypeId, 'key_name' => 'Round Neck', 'key_value' => 'round', 'sort_order' => 1],
            ['group_id' => $neckTypeId, 'key_name' => 'V-Neck', 'key_value' => 'v_neck', 'sort_order' => 2],
            ['group_id' => $neckTypeId, 'key_name' => 'Collar', 'key_value' => 'collar', 'sort_order' => 3],
            ['group_id' => $neckTypeId, 'key_name' => 'Hooded', 'key_value' => 'hooded', 'sort_order' => 4],
            ['group_id' => $neckTypeId, 'key_name' => 'Turtle Neck', 'key_value' => 'turtle', 'sort_order' => 5],
            ['group_id' => $neckTypeId, 'key_name' => 'Polo', 'key_value' => 'polo', 'sort_order' => 6],
        ];

        // =================================================================
        // VARIANT KEYS - ELECTRONICS
        // =================================================================
        
        $this->command->info('  📱 Adding electronics variants...');
        
        $storageKeys = [
            ['group_id' => $storageId, 'key_name' => '16GB', 'key_value' => '16', 'sort_order' => 1],
            ['group_id' => $storageId, 'key_name' => '32GB', 'key_value' => '32', 'sort_order' => 2],
            ['group_id' => $storageId, 'key_name' => '64GB', 'key_value' => '64', 'sort_order' => 3],
            ['group_id' => $storageId, 'key_name' => '128GB', 'key_value' => '128', 'sort_order' => 4],
            ['group_id' => $storageId, 'key_name' => '256GB', 'key_value' => '256', 'sort_order' => 5],
            ['group_id' => $storageId, 'key_name' => '512GB', 'key_value' => '512', 'sort_order' => 6],
            ['group_id' => $storageId, 'key_name' => '1TB', 'key_value' => '1024', 'sort_order' => 7],
            ['group_id' => $storageId, 'key_name' => '2TB', 'key_value' => '2048', 'sort_order' => 8],
        ];

        $ramKeys = [
            ['group_id' => $ramId, 'key_name' => '2GB', 'key_value' => '2', 'sort_order' => 1],
            ['group_id' => $ramId, 'key_name' => '4GB', 'key_value' => '4', 'sort_order' => 2],
            ['group_id' => $ramId, 'key_name' => '6GB', 'key_value' => '6', 'sort_order' => 3],
            ['group_id' => $ramId, 'key_name' => '8GB', 'key_value' => '8', 'sort_order' => 4],
            ['group_id' => $ramId, 'key_name' => '12GB', 'key_value' => '12', 'sort_order' => 5],
            ['group_id' => $ramId, 'key_name' => '16GB', 'key_value' => '16', 'sort_order' => 6],
            ['group_id' => $ramId, 'key_name' => '32GB', 'key_value' => '32', 'sort_order' => 7],
        ];

        $screenSizeKeys = [
            ['group_id' => $screenSizeId, 'key_name' => '5.0"', 'key_value' => '5.0', 'sort_order' => 1],
            ['group_id' => $screenSizeId, 'key_name' => '5.5"', 'key_value' => '5.5', 'sort_order' => 2],
            ['group_id' => $screenSizeId, 'key_name' => '6.0"', 'key_value' => '6.0', 'sort_order' => 3],
            ['group_id' => $screenSizeId, 'key_name' => '6.5"', 'key_value' => '6.5', 'sort_order' => 4],
            ['group_id' => $screenSizeId, 'key_name' => '7.0"', 'key_value' => '7.0', 'sort_order' => 5],
            ['group_id' => $screenSizeId, 'key_name' => '10.1"', 'key_value' => '10.1', 'sort_order' => 6],
            ['group_id' => $screenSizeId, 'key_name' => '13.3"', 'key_value' => '13.3', 'sort_order' => 7],
            ['group_id' => $screenSizeId, 'key_name' => '15.6"', 'key_value' => '15.6', 'sort_order' => 8],
            ['group_id' => $screenSizeId, 'key_name' => '17.3"', 'key_value' => '17.3', 'sort_order' => 9],
        ];

        $processorKeys = [
            ['group_id' => $processorId, 'key_name' => 'Intel Core i3', 'key_value' => 'intel_i3', 'sort_order' => 1],
            ['group_id' => $processorId, 'key_name' => 'Intel Core i5', 'key_value' => 'intel_i5', 'sort_order' => 2],
            ['group_id' => $processorId, 'key_name' => 'Intel Core i7', 'key_value' => 'intel_i7', 'sort_order' => 3],
            ['group_id' => $processorId, 'key_name' => 'Intel Core i9', 'key_value' => 'intel_i9', 'sort_order' => 4],
            ['group_id' => $processorId, 'key_name' => 'AMD Ryzen 3', 'key_value' => 'amd_r3', 'sort_order' => 5],
            ['group_id' => $processorId, 'key_name' => 'AMD Ryzen 5', 'key_value' => 'amd_r5', 'sort_order' => 6],
            ['group_id' => $processorId, 'key_name' => 'AMD Ryzen 7', 'key_value' => 'amd_r7', 'sort_order' => 7],
            ['group_id' => $processorId, 'key_name' => 'AMD Ryzen 9', 'key_value' => 'amd_r9', 'sort_order' => 8],
            ['group_id' => $processorId, 'key_name' => 'Apple M1', 'key_value' => 'apple_m1', 'sort_order' => 9],
            ['group_id' => $processorId, 'key_name' => 'Apple M2', 'key_value' => 'apple_m2', 'sort_order' => 10],
        ];

        $connectivityKeys = [
            ['group_id' => $connectivityId, 'key_name' => 'WiFi Only', 'key_value' => 'wifi', 'sort_order' => 1],
            ['group_id' => $connectivityId, 'key_name' => 'WiFi + 4G', 'key_value' => 'wifi_4g', 'sort_order' => 2],
            ['group_id' => $connectivityId, 'key_name' => 'WiFi + 5G', 'key_value' => 'wifi_5g', 'sort_order' => 3],
            ['group_id' => $connectivityId, 'key_name' => 'Bluetooth', 'key_value' => 'bluetooth', 'sort_order' => 4],
        ];

        $warrantyKeys = [
            ['group_id' => $warrantyId, 'key_name' => '6 Months', 'key_value' => '6', 'sort_order' => 1],
            ['group_id' => $warrantyId, 'key_name' => '1 Year', 'key_value' => '12', 'sort_order' => 2],
            ['group_id' => $warrantyId, 'key_name' => '2 Years', 'key_value' => '24', 'sort_order' => 3],
            ['group_id' => $warrantyId, 'key_name' => '3 Years', 'key_value' => '36', 'sort_order' => 4],
            ['group_id' => $warrantyId, 'key_name' => '5 Years', 'key_value' => '60', 'sort_order' => 5],
        ];

        // =================================================================
        // VARIANT KEYS - ORGANIC FOODS
        // =================================================================
        
        $this->command->info('  🌾 Adding organic food variants...');
        
        $weightKeys = [
            ['group_id' => $weightId, 'key_name' => '100gm', 'key_value' => '100', 'sort_order' => 1],
            ['group_id' => $weightId, 'key_name' => '250gm', 'key_value' => '250', 'sort_order' => 2],
            ['group_id' => $weightId, 'key_name' => '500gm', 'key_value' => '500', 'sort_order' => 3],
            ['group_id' => $weightId, 'key_name' => '1kg', 'key_value' => '1000', 'sort_order' => 4],
            ['group_id' => $weightId, 'key_name' => '2kg', 'key_value' => '2000', 'sort_order' => 5],
            ['group_id' => $weightId, 'key_name' => '5kg', 'key_value' => '5000', 'sort_order' => 6],
            ['group_id' => $weightId, 'key_name' => '10kg', 'key_value' => '10000', 'sort_order' => 7],
            ['group_id' => $weightId, 'key_name' => '25kg', 'key_value' => '25000', 'sort_order' => 8],
            ['group_id' => $weightId, 'key_name' => '50kg', 'key_value' => '50000', 'sort_order' => 9],
        ];

        $packSizeKeys = [
            ['group_id' => $packSizeId, 'key_name' => 'Small Pack', 'key_value' => 'small', 'sort_order' => 1],
            ['group_id' => $packSizeId, 'key_name' => 'Medium Pack', 'key_value' => 'medium', 'sort_order' => 2],
            ['group_id' => $packSizeId, 'key_name' => 'Large Pack', 'key_value' => 'large', 'sort_order' => 3],
            ['group_id' => $packSizeId, 'key_name' => 'Family Pack', 'key_value' => 'family', 'sort_order' => 4],
            ['group_id' => $packSizeId, 'key_name' => 'Bulk Pack', 'key_value' => 'bulk', 'sort_order' => 5],
        ];

        $typeKeys = [
            ['group_id' => $typeId, 'key_name' => 'Whole', 'key_value' => 'whole', 'sort_order' => 1],
            ['group_id' => $typeId, 'key_name' => 'Sliced', 'key_value' => 'sliced', 'sort_order' => 2],
            ['group_id' => $typeId, 'key_name' => 'Diced', 'key_value' => 'diced', 'sort_order' => 3],
            ['group_id' => $typeId, 'key_name' => 'Ground/Powder', 'key_value' => 'ground', 'sort_order' => 4],
            ['group_id' => $typeId, 'key_name' => 'Minced', 'key_value' => 'minced', 'sort_order' => 5],
            ['group_id' => $typeId, 'key_name' => 'Chopped', 'key_value' => 'chopped', 'sort_order' => 6],
        ];

        $originKeys = [
            ['group_id' => $originId, 'key_name' => 'Local', 'key_value' => 'local', 'sort_order' => 1],
            ['group_id' => $originId, 'key_name' => 'Imported', 'key_value' => 'imported', 'sort_order' => 2],
            ['group_id' => $originId, 'key_name' => 'Bangladesh', 'key_value' => 'bd', 'sort_order' => 3],
            ['group_id' => $originId, 'key_name' => 'India', 'key_value' => 'in', 'sort_order' => 4],
            ['group_id' => $originId, 'key_name' => 'Thailand', 'key_value' => 'th', 'sort_order' => 5],
            ['group_id' => $originId, 'key_name' => 'Vietnam', 'key_value' => 'vn', 'sort_order' => 6],
        ];

        $gradeKeys = [
            ['group_id' => $gradeId, 'key_name' => 'Premium', 'key_value' => 'premium', 'sort_order' => 1],
            ['group_id' => $gradeId, 'key_name' => 'Grade A', 'key_value' => 'grade_a', 'sort_order' => 2],
            ['group_id' => $gradeId, 'key_name' => 'Grade B', 'key_value' => 'grade_b', 'sort_order' => 3],
            ['group_id' => $gradeId, 'key_name' => 'Standard', 'key_value' => 'standard', 'sort_order' => 4],
            ['group_id' => $gradeId, 'key_name' => 'Economy', 'key_value' => 'economy', 'sort_order' => 5],
        ];

        $freshnessKeys = [
            ['group_id' => $freshnessId, 'key_name' => 'Fresh', 'key_value' => 'fresh', 'sort_order' => 1],
            ['group_id' => $freshnessId, 'key_name' => 'Frozen', 'key_value' => 'frozen', 'sort_order' => 2],
            ['group_id' => $freshnessId, 'key_name' => 'Dried', 'key_value' => 'dried', 'sort_order' => 3],
            ['group_id' => $freshnessId, 'key_name' => 'Canned', 'key_value' => 'canned', 'sort_order' => 4],
            ['group_id' => $freshnessId, 'key_name' => 'Vacuum Packed', 'key_value' => 'vacuum', 'sort_order' => 5],
        ];

        // =================================================================
        // MERGE AND INSERT ALL KEYS
        // =================================================================
        
        $allKeys = array_merge(
            // Clothing
            $materialKeys,
            $patternKeys,
            $fitKeys,
            $sleeveLengthKeys,
            $neckTypeKeys,
            // Electronics
            $storageKeys,
            $ramKeys,
            $screenSizeKeys,
            $processorKeys,
            $connectivityKeys,
            $warrantyKeys,
            // Organic Foods
            $weightKeys,
            $packSizeKeys,
            $typeKeys,
            $originKeys,
            $gradeKeys,
            $freshnessKeys
        );

        // Add timestamps and status to all keys
        foreach ($allKeys as &$key) {
            $key['status'] = 1;
            $key['created_at'] = $now;
            $key['updated_at'] = $now;
        }

        // Insert in chunks to avoid memory issues
        $chunks = array_chunk($allKeys, 100);
        foreach ($chunks as $chunk) {
            DB::table('product_stock_variants_group_keys')->insertOrIgnore($chunk);
        }

        $this->command->info('✅ All variant keys seeded!');
        $this->command->newLine();
        $this->command->info('📊 Summary:');
        $this->command->info('  - Total Groups: ' . count($groups));
        $this->command->info('  - Total Keys: ' . count($allKeys));
        $this->command->info('  - Clothing Groups: 5 (Material, Pattern, Fit, Sleeve Length, Neck Type)');
        $this->command->info('  - Electronics Groups: 6 (Storage, RAM, Screen Size, Processor, Connectivity, Warranty)');
        $this->command->info('  - Organic Food Groups: 6 (Weight, Pack Size, Type, Origin, Grade, Freshness)');
        $this->command->newLine();
        $this->command->info('🎉 Variant system seeded successfully!');
    }
}

