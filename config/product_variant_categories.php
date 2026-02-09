<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Product Variant Category Mappings
    |--------------------------------------------------------------------------
    |
    | This configuration maps product categories to suggested variant groups.
    | When a user selects a category, the system will suggest appropriate
    | variant groups based on the category type.
    |
    */

    'electronics' => [
        'variant_groups' => ['color', 'size', 'other'],
        'default_groups' => ['color', 'size'],
        'show_color_size_tab' => true,
        'category_keywords' => ['electronics', 'electronic', 'mobile', 'phone', 'laptop', 'computer', 'tablet', 'tv', 'television'],
    ],

    'grocery' => [
        'variant_groups' => ['other'],
        'default_groups' => [],
        'show_color_size_tab' => false,
        'category_keywords' => ['grocery', 'food', 'beverage', 'snack', 'rice', 'oil', 'spice'],
    ],

    'clothes' => [
        'variant_groups' => ['color', 'size'],
        'default_groups' => ['color', 'size'],
        'show_color_size_tab' => true,
        'category_keywords' => ['clothes', 'clothing', 'apparel', 'garment', 'shirt', 'pant', 'dress', 't-shirt'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    |
    | These methods help identify category types from category names or slugs.
    |
    */

    'getCategoryType' => function ($categoryName, $categorySlug = null) {
        $name = strtolower($categoryName ?? '');
        $slug = strtolower($categorySlug ?? '');
        $combined = $name . ' ' . $slug;

        foreach (config('product_variant_categories') as $type => $config) {
            if (is_array($config) && isset($config['category_keywords'])) {
                foreach ($config['category_keywords'] as $keyword) {
                    if (strpos($combined, strtolower($keyword)) !== false) {
                        return $type;
                    }
                }
            }
        }

        return null;
    },
];

