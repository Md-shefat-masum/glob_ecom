<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DbClearTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('ac_incomes')->truncate();
        DB::table('ac_income_categories')->truncate();
        DB::table('ac_investor_profit_allocations')->truncate();
        DB::table('ac_investor_rules')->truncate();
        DB::table('ac_moneydeposits')->truncate();

        DB::table('ac_moneytransfer')->truncate();
        DB::table('ac_money_withdraws')->truncate();
        DB::table('ac_transactions')->truncate();

        DB::table('cache')->truncate();
        DB::table('cache_locks')->truncate();
        DB::table('carts')->truncate();

        DB::table('db_customer_payments')->truncate();
        DB::table('db_expenses')->truncate();
        DB::table('db_purchasepayments')->truncate();
        DB::table('db_supplier_payments')->truncate();
        DB::table('product_order_products')->truncate();
        DB::table('product_purchase_orders')->truncate();
        DB::table('product_purchase_order_products')->truncate();
        DB::table('product_purchase_order_product_units')->truncate();
        DB::table('product_purchase_returns')->truncate();
        DB::table('product_purchase_return_products')->truncate();
        DB::table('product_stocks')->truncate();
        DB::table('product_stock_logs')->truncate();
        
        DB::table('product_stock_logs')->truncate();


        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
