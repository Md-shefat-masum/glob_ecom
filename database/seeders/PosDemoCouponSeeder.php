<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosDemoCouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $today = now()->toDateString();
        $future = now()->addYear()->toDateString();

        // 50% off coupon
        if (!DB::table('promo_codes')->where('code', 'POSHALF50')->exists()) {
            DB::table('promo_codes')->insert([
                'icon' => null,
                'title' => 'POS 50% Off',
                'description' => 'Demo POS coupon for 50% off.',
                'code' => 'POSHALF50',
                'effective_date' => $today,
                'expire_date' => $future,
                'type' => 2, // percentage
                'value' => 50,
                'minimum_order_amount' => 0,
                'slug' => Str::uuid(),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 50 TK off coupon
        if (!DB::table('promo_codes')->where('code', 'POSLESS50')->exists()) {
            DB::table('promo_codes')->insert([
                'icon' => null,
                'title' => 'POS 50 TK Off',
                'description' => 'Demo POS coupon for 50 TK flat discount.',
                'code' => 'POSLESS50',
                'effective_date' => $today,
                'expire_date' => $future,
                'type' => 1, // fixed amount
                'value' => 50,
                'minimum_order_amount' => 0,
                'slug' => Str::uuid(),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}


