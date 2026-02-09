<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrganicVariantsSeeder0 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        // Wipe existing organic variant data safely
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('product_stock_variants_group_keys')->truncate();
        DB::table('product_stock_variant_groups')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $groups = [
            'color' => [
                'name' => 'Color',
                'slug' => 'color',
                'description' => 'Organic product color palette',
                'is_fixed' => 1,
                'is_stock_related' => 1,
                'sort_order' => 1,
            ],
            'size' => [
                'name' => 'Size',
                'slug' => 'size',
                'description' => 'Common organic package sizes',
                'is_fixed' => 1,
                'is_stock_related' => 1,
                'sort_order' => 2,
            ],
            'fruits' => [
                'name' => 'Fruits',
                'slug' => 'fruits',
                'description' => 'Organic fruit weight & packaging',
                'is_fixed' => 1,
                'is_stock_related' => 1,
                'sort_order' => 10,
            ],
            'dates' => [
                'name' => 'Dates',
                'slug' => 'dates',
                'description' => 'Dates (khejur) premium packaging',
                'is_fixed' => 1,
                'is_stock_related' => 1,
                'sort_order' => 11,
            ],
            'jaggery' => [
                'name' => 'Jaggery',
                'slug' => 'jaggery',
                'description' => 'Organic jaggery pack sizes',
                'is_fixed' => 1,
                'is_stock_related' => 1,
                'sort_order' => 12,
            ],
            'dried_fish' => [
                'name' => 'Dried Fish',
                'slug' => 'dried_fish',
                'description' => 'Sun-dried fish pack sizes',
                'is_fixed' => 1,
                'is_stock_related' => 1,
                'sort_order' => 13,
            ],
            'grains' => [
                'name' => 'Grains',
                'slug' => 'grains',
                'description' => 'Organic grains sacks & packs',
                'is_fixed' => 1,
                'is_stock_related' => 1,
                'sort_order' => 14,
            ],
            'oil' => [
                'name' => 'Oil',
                'slug' => 'oil',
                'description' => 'Cold-pressed oil litre options',
                'is_fixed' => 1,
                'is_stock_related' => 1,
                'sort_order' => 15,
            ],
            'spices' => [
                'name' => 'Spices',
                'slug' => 'spices',
                'description' => 'Whole and ground spice packs',
                'is_fixed' => 1,
                'is_stock_related' => 1,
                'sort_order' => 16,
            ],
            'dairy' => [
                'name' => 'Dairy',
                'slug' => 'dairy',
                'description' => 'Fresh dairy pack and litre options',
                'is_fixed' => 1,
                'is_stock_related' => 1,
                'sort_order' => 17,
            ],
            'honey' => [
                'name' => 'Honey',
                'slug' => 'honey',
                'description' => 'Organic honey jar and container sizes',
                'is_fixed' => 1,
                'is_stock_related' => 1,
                'sort_order' => 18,
            ],
            'herbal' => [
                'name' => 'Herbal',
                'slug' => 'herbal',
                'description' => 'Herbal and ayurvedic pack sizes',
                'is_fixed' => 1,
                'is_stock_related' => 1,
                'sort_order' => 19,
            ],
        ];

        $groupPayload = collect($groups)->map(function (array $group) use ($now) {
            return array_merge($group, [
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        })->values()->all();

        DB::table('product_stock_variant_groups')->upsert(
            $groupPayload,
            ['slug'],
            ['name', 'description', 'is_fixed', 'is_stock_related', 'sort_order', 'status', 'updated_at']
        );

        $groupIds = DB::table('product_stock_variant_groups')
            ->whereIn('slug', array_keys($groups))
            ->pluck('id', 'slug');

        $variantDefinitions = [
            'color' => ['natural', 'golden', 'brown', 'dark brown', 'light yellow', 'mixed'],
            'size' => ['250gm', '500gm', '1kg', '5kg', '10kg', '1 litre', '5 litre gallon', 'gift box'],
            'fruits' => ['1kg', '5kg', '10kg', '1 basket', 'gift box'],
            'dates' => ['500gm box', '1kg box', '5kg box', '10kg box', 'premium gift box'],
            'jaggery' => ['250gm', '500gm', '1kg', '5kg packet'],
            'dried_fish' => ['250gm', '500gm', '1kg', '2kg family pack'],
            'grains' => ['1kg', '2kg', '5kg', '10kg sack'],
            'oil' => ['250ml', '500ml', '1 litre', '5 litre gallon'],
            'spices' => ['100gm', '250gm', '500gm', '1kg'],
            'dairy' => ['250gm', '500gm', '1/2 litre', '1 litre', '5 litre gallon'],
            'honey' => ['250gm jar', '500gm jar', '1kg jar', '5kg container'],
            'herbal' => ['100gm', '250gm', '500gm', '1kg'],
        ];

        $variantKeys = [];

        foreach ($variantDefinitions as $slug => $values) {
            if (!$groupIds->has($slug)) {
                continue;
            }

            foreach ($values as $index => $value) {
                $variantKeys[] = [
                    'group_id' => $groupIds[$slug],
                    'key_name' => $value,
                    'key_value' => $this->normalizeKeyValue($value),
                    'sort_order' => $index + 1,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($variantKeys)) {
            DB::table('product_stock_variants_group_keys')->upsert(
                $variantKeys,
                ['group_id', 'key_value'],
                ['key_name', 'sort_order', 'status', 'updated_at']
            );
        }
    }

    private function normalizeKeyValue(string $value): string
    {
        return (string) Str::of($value)
            ->lower()
            ->replace(['(', ')'], '')
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->trim('_');
    }
}


