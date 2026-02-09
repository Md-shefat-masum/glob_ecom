<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsDemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all active products
        $products = DB::table('products')
            ->where('status', 'active')
            ->limit(20)
            ->get();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please add products first.');
            return;
        }

        // Get first user or use ID 1
        $userId = DB::table('users')->first()->id ?? 1;

        $this->command->info('Seeding product views...');
        
        // Add product views (last 30 days)
        foreach ($products as $product) {
            // Random views between 10-100 per product
            $viewCount = rand(10, 100);
            
            for ($i = 0; $i < $viewCount; $i++) {
                // Random date in last 30 days
                $daysAgo = rand(0, 30);
                $viewedAt = Carbon::now()->subDays($daysAgo)->subHours(rand(0, 23));
                
                DB::table('product_views')->insert([
                    'product_id' => $product->id,
                    'user_id' => rand(0, 1) ? $userId : null, // 50% guest users
                    'ip_address' => '192.168.1.' . rand(1, 255),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'viewed_at' => $viewedAt,
                    'created_at' => $viewedAt,
                    'updated_at' => $viewedAt,
                ]);
            }
        }

        $this->command->info('Seeding product reviews...');
        
        // Add product reviews (ratings)
        foreach ($products->take(15) as $product) {
            // Random 2-5 reviews per product
            $reviewCount = rand(2, 5);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                $rating = rand(3, 5); // Mostly positive ratings
                $daysAgo = rand(0, 60);
                
                $reviews = [
                    'Excellent product! Very fresh and organic.',
                    'Great quality, will buy again.',
                    'Good value for money. Recommended!',
                    'Fresh and tasty. Love it!',
                    'Best organic product I have tried.',
                    'Very satisfied with the quality.',
                    'Delivered on time and fresh.',
                    'Authentic organic product.',
                ];
                
                DB::table('product_reviews')->insert([
                    'product_id' => $product->id,
                    'user_id' => $userId,
                    'rating' => $rating,
                    'review' => $reviews[array_rand($reviews)],
                    'reply' => null,
                    'slug' => uniqid() . '-' . time(),
                    'status' => 1, // Approved
                    'created_at' => Carbon::now()->subDays($daysAgo),
                    'updated_at' => Carbon::now()->subDays($daysAgo),
                ]);
            }
        }

        $this->command->info('✅ Analytics demo data seeded successfully!');
        $this->command->info('   - Product views: ' . ($products->count() * 55) . ' (average)');
        $this->command->info('   - Product reviews: ' . ($products->take(15)->count() * 3.5) . ' (average)');
    }
}

