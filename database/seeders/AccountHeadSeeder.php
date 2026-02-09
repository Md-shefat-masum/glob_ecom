<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $storeId = 1;

        // Clear existing data - disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('ac_event_mappings')->truncate();
        DB::table('ac_accounts')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ============================================
        // CHART OF ACCOUNTS
        // ============================================

        // 1. ASSETS (Parent ID = 0)
        $assetId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => 0,
            'account_type' => 'asset',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'is_control_account' => false,
            'sort_code' => '1000',
            'account_code' => 'AC-1000',
            'account_name' => 'Assets',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 1.1 Current Assets
        $currentAssetId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $assetId,
            'account_type' => 'asset',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '1100',
            'account_code' => 'AC-1100',
            'account_name' => 'Current Assets',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 1.1.1 Cash & Bank Accounts
        $cashBankId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $currentAssetId,
            'account_type' => 'asset',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '1110',
            'account_code' => 'AC-1110',
            'account_name' => 'Cash & Bank',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $cashOnHandId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $cashBankId,
            'account_type' => 'asset',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '1111',
            'account_code' => 'AC-1111',
            'account_name' => 'Cash on Hand',
            'account_selection_name' => 'cash_on_hand',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $bankAccountId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $cashBankId,
            'account_type' => 'asset',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '1112',
            'account_code' => 'AC-1112',
            'account_name' => 'Bank Account',
            'account_selection_name' => 'bank_account',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $pettyCashId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $cashBankId,
            'account_type' => 'asset',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '1113',
            'account_code' => 'AC-1113',
            'account_name' => 'Petty Cash',
            'account_selection_name' => 'petty_cash',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 1.1.2 Accounts Receivable (Control Account)
        $accountsReceivableId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $currentAssetId,
            'account_type' => 'asset',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'is_control_account' => true,
            'sort_code' => '1120',
            'account_code' => 'AC-1120',
            'account_name' => 'Accounts Receivable',
            'account_selection_name' => 'accounts_receivable',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 1.1.3 Inventory
        $inventoryId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $currentAssetId,
            'account_type' => 'asset',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '1130',
            'account_code' => 'AC-1130',
            'account_name' => 'Inventory',
            'account_selection_name' => 'inventory',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 1.2 Fixed Assets
        $fixedAssetId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $assetId,
            'account_type' => 'asset',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '1200',
            'account_code' => 'AC-1200',
            'account_name' => 'Fixed Assets',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. LIABILITIES (Parent ID = 0)
        $liabilityId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => 0,
            'account_type' => 'liability',
            'normal_balance' => 'credit',
            'is_system_account' => true,
            'is_control_account' => false,
            'sort_code' => '2000',
            'account_code' => 'AC-2000',
            'account_name' => 'Liabilities',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2.1 Current Liabilities
        $currentLiabilityId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $liabilityId,
            'account_type' => 'liability',
            'normal_balance' => 'credit',
            'is_system_account' => true,
            'sort_code' => '2100',
            'account_code' => 'AC-2100',
            'account_name' => 'Current Liabilities',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2.1.1 Accounts Payable (Control Account)
        $accountsPayableId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $currentLiabilityId,
            'account_type' => 'liability',
            'normal_balance' => 'credit',
            'is_system_account' => true,
            'is_control_account' => true,
            'sort_code' => '2110',
            'account_code' => 'AC-2110',
            'account_name' => 'Accounts Payable',
            'account_selection_name' => 'accounts_payable',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2.2 Long-term Liabilities
        $longTermLiabilityId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $liabilityId,
            'account_type' => 'liability',
            'normal_balance' => 'credit',
            'is_system_account' => true,
            'sort_code' => '2200',
            'account_code' => 'AC-2200',
            'account_name' => 'Long-term Liabilities',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $loansPayableId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $longTermLiabilityId,
            'account_type' => 'liability',
            'normal_balance' => 'credit',
            'is_system_account' => true,
            'sort_code' => '2210',
            'account_code' => 'AC-2210',
            'account_name' => 'Loans Payable',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. OWNER'S EQUITY (Parent ID = 0)
        $equityId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => 0,
            'account_type' => 'equity',
            'normal_balance' => 'credit',
            'is_system_account' => true,
            'sort_code' => '3000',
            'account_code' => 'AC-3000',
            'account_name' => 'Owner\'s Equity',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $capitalId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $equityId,
            'account_type' => 'equity',
            'normal_balance' => 'credit',
            'is_system_account' => true,
            'sort_code' => '3100',
            'account_code' => 'AC-3100',
            'account_name' => 'Capital',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $retainedEarningsId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $equityId,
            'account_type' => 'equity',
            'normal_balance' => 'credit',
            'is_system_account' => true,
            'sort_code' => '3200',
            'account_code' => 'AC-3200',
            'account_name' => 'Retained Earnings',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $drawingsId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $equityId,
            'account_type' => 'equity',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '3300',
            'account_code' => 'AC-3300',
            'account_name' => 'Owner\'s Drawings',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 4. REVENUE (Parent ID = 0)
        $revenueId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => 0,
            'account_type' => 'revenue',
            'normal_balance' => 'credit',
            'is_system_account' => true,
            'sort_code' => '4000',
            'account_code' => 'AC-4000',
            'account_name' => 'Revenue',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $salesRevenueId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $revenueId,
            'account_type' => 'revenue',
            'normal_balance' => 'credit',
            'is_system_account' => true,
            'sort_code' => '4100',
            'account_code' => 'AC-4100',
            'account_name' => 'Sales Revenue',
            'account_selection_name' => 'sales_revenue',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $otherIncomeId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $revenueId,
            'account_type' => 'revenue',
            'normal_balance' => 'credit',
            'is_system_account' => true,
            'sort_code' => '4200',
            'account_code' => 'AC-4200',
            'account_name' => 'Other Income',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 5. EXPENSES (Parent ID = 0)
        $expenseId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => 0,
            'account_type' => 'expense',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '5000',
            'account_code' => 'AC-5000',
            'account_name' => 'Expenses',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 5.1 Cost of Goods Sold
        $cogsId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $expenseId,
            'account_type' => 'expense',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '5100',
            'account_code' => 'AC-5100',
            'account_name' => 'Cost of Goods Sold (COGS)',
            'account_selection_name' => 'cogs',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 5.2 Sales Returns & Allowances (Contra-Revenue)
        $salesReturnsId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $expenseId,
            'account_type' => 'expense',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '5200',
            'account_code' => 'AC-5200',
            'account_name' => 'Sales Returns & Allowances',
            'account_selection_name' => 'sales_returns',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 5.3 Purchase Returns (Contra-Expense - Credit Balance)
        $purchaseReturnsId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $expenseId,
            'account_type' => 'expense',
            'normal_balance' => 'credit',
            'is_system_account' => true,
            'sort_code' => '5300',
            'account_code' => 'AC-5300',
            'account_name' => 'Purchase Returns',
            'account_selection_name' => 'purchase_returns',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 5.4 Operating Expenses
        $operatingExpensesId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $expenseId,
            'account_type' => 'expense',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '5400',
            'account_code' => 'AC-5400',
            'account_name' => 'Operating Expenses',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $payrollExpenseId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $operatingExpensesId,
            'account_type' => 'expense',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '5410',
            'account_code' => 'AC-5410',
            'account_name' => 'Payroll Expense',
            'account_selection_name' => 'payroll_expense',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $regularExpensesId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $operatingExpensesId,
            'account_type' => 'expense',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '5420',
            'account_code' => 'AC-5420',
            'account_name' => 'Regular Expenses',
            'account_selection_name' => 'regular_expenses',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $productWasteId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $operatingExpensesId,
            'account_type' => 'expense',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '5430',
            'account_code' => 'AC-5430',
            'account_name' => 'Product Waste/Loss',
            'account_selection_name' => 'product_waste',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $promotionalExpenseId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $operatingExpensesId,
            'account_type' => 'expense',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '5440',
            'account_code' => 'AC-5440',
            'account_name' => 'Promotional/Gift Expense',
            'account_selection_name' => 'promotional_expense',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $utilitiesExpenseId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $operatingExpensesId,
            'account_type' => 'expense',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '5450',
            'account_code' => 'AC-5450',
            'account_name' => 'Utilities Expense',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $rentExpenseId = DB::table('ac_accounts')->insertGetId([
            'store_id' => $storeId,
            'parent_id' => $operatingExpensesId,
            'account_type' => 'expense',
            'normal_balance' => 'debit',
            'is_system_account' => true,
            'sort_code' => '5460',
            'account_code' => 'AC-5460',
            'account_name' => 'Rent Expense',
            'balance' => 0,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ============================================
        // EVENT MAPPINGS
        // ============================================

        $this->seedEventMappings([
            'cash_on_hand_id' => $cashOnHandId,
            'bank_account_id' => $bankAccountId,
            'petty_cash_id' => $pettyCashId,
            'accounts_receivable_id' => $accountsReceivableId,
            'accounts_payable_id' => $accountsPayableId,
            'inventory_id' => $inventoryId,
            'sales_revenue_id' => $salesRevenueId,
            'cogs_id' => $cogsId,
            'sales_returns_id' => $salesReturnsId,
            'purchase_returns_id' => $purchaseReturnsId,
            'payroll_expense_id' => $payrollExpenseId,
            'regular_expenses_id' => $regularExpensesId,
            'product_waste_id' => $productWasteId,
            'promotional_expense_id' => $promotionalExpenseId,
        ]);

        $this->command->info('Account heads and event mappings seeded successfully!');
    }

    /**
     * Seed event to account mappings
     */
    private function seedEventMappings($accounts)
    {
        $now = Carbon::now();

        $mappings = [
            // Purchase - Debit: Inventory, Credit: Accounts Payable
            [
                'event_name' => 'purchase',
                'event_description' => 'Purchase of inventory on credit',
                'debit_account_id' => $accounts['inventory_id'],
                'credit_account_id' => $accounts['accounts_payable_id'],
                'secondary_debit_account_id' => null,
                'secondary_credit_account_id' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Purchase Cash - Debit: Inventory, Credit: Cash/Bank
            [
                'event_name' => 'purchase_cash',
                'event_description' => 'Purchase of inventory with cash',
                'debit_account_id' => $accounts['inventory_id'],
                'credit_account_id' => $accounts['cash_on_hand_id'],
                'secondary_debit_account_id' => null,
                'secondary_credit_account_id' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Purchase Return - Debit: Accounts Payable, Credit: Inventory
            [
                'event_name' => 'purchase_return',
                'event_description' => 'Return of purchased inventory',
                'debit_account_id' => $accounts['accounts_payable_id'],
                'credit_account_id' => $accounts['inventory_id'],
                'secondary_debit_account_id' => null,
                'secondary_credit_account_id' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Supplier Payment - Debit: Accounts Payable, Credit: Cash/Bank
            [
                'event_name' => 'supplier_payment',
                'event_description' => 'Payment to supplier',
                'debit_account_id' => $accounts['accounts_payable_id'],
                'credit_account_id' => $accounts['cash_on_hand_id'],
                'secondary_debit_account_id' => null,
                'secondary_credit_account_id' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Supplier Payment Return - Debit: Cash/Bank, Credit: Accounts Payable
            [
                'event_name' => 'supplier_payment_return',
                'event_description' => 'Return of payment from supplier',
                'debit_account_id' => $accounts['cash_on_hand_id'],
                'credit_account_id' => $accounts['accounts_payable_id'],
                'secondary_debit_account_id' => null,
                'secondary_credit_account_id' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sales Cash - Primary: Debit: Cash, Credit: Sales Revenue
            //           - Secondary: Debit: COGS, Credit: Inventory
            [
                'event_name' => 'sales',
                'event_description' => 'Sale of products for cash',
                'debit_account_id' => $accounts['cash_on_hand_id'],
                'credit_account_id' => $accounts['sales_revenue_id'],
                'secondary_debit_account_id' => $accounts['cogs_id'],
                'secondary_credit_account_id' => $accounts['inventory_id'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sales Credit - Primary: Debit: AR, Credit: Sales Revenue
            //              - Secondary: Debit: COGS, Credit: Inventory
            [
                'event_name' => 'sales_credit',
                'event_description' => 'Sale of products on credit',
                'debit_account_id' => $accounts['accounts_receivable_id'],
                'credit_account_id' => $accounts['sales_revenue_id'],
                'secondary_debit_account_id' => $accounts['cogs_id'],
                'secondary_credit_account_id' => $accounts['inventory_id'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Sales Return - Primary: Debit: Sales Returns, Credit: Cash/AR
            //              - Secondary: Debit: Inventory, Credit: COGS
            [
                'event_name' => 'sales_return',
                'event_description' => 'Return of sold products',
                'debit_account_id' => $accounts['sales_returns_id'],
                'credit_account_id' => $accounts['cash_on_hand_id'],
                'secondary_debit_account_id' => $accounts['inventory_id'],
                'secondary_credit_account_id' => $accounts['cogs_id'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Customer Payment - Debit: Cash/Bank, Credit: Accounts Receivable
            [
                'event_name' => 'customer_payment',
                'event_description' => 'Payment received from customer',
                'debit_account_id' => $accounts['cash_on_hand_id'],
                'credit_account_id' => $accounts['accounts_receivable_id'],
                'secondary_debit_account_id' => null,
                'secondary_credit_account_id' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Customer Due Order - Debit: Accounts Receivable, Credit: Sales Revenue
            [
                'event_name' => 'customer_due_order',
                'event_description' => 'Customer order with due payment',
                'debit_account_id' => $accounts['accounts_receivable_id'],
                'credit_account_id' => $accounts['sales_revenue_id'],
                'secondary_debit_account_id' => $accounts['cogs_id'],
                'secondary_credit_account_id' => $accounts['inventory_id'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Product Waste - Debit: Product Waste Expense, Credit: Inventory
            [
                'event_name' => 'product_waste',
                'event_description' => 'Product damaged or wasted',
                'debit_account_id' => $accounts['product_waste_id'],
                'credit_account_id' => $accounts['inventory_id'],
                'secondary_debit_account_id' => null,
                'secondary_credit_account_id' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Gift Product - Debit: Promotional Expense, Credit: Inventory
            [
                'event_name' => 'gift_product',
                'event_description' => 'Product given as gift (100% discount)',
                'debit_account_id' => $accounts['promotional_expense_id'],
                'credit_account_id' => $accounts['inventory_id'],
                'secondary_debit_account_id' => null,
                'secondary_credit_account_id' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Regular Expense - Debit: Regular Expenses, Credit: Cash/Bank
            [
                'event_name' => 'regular_expense',
                'event_description' => 'Regular operating expense',
                'debit_account_id' => $accounts['regular_expenses_id'],
                'credit_account_id' => $accounts['cash_on_hand_id'],
                'secondary_debit_account_id' => null,
                'secondary_credit_account_id' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Payroll - Debit: Payroll Expense, Credit: Cash/Bank
            [
                'event_name' => 'payroll',
                'event_description' => 'Employee payroll payment',
                'debit_account_id' => $accounts['payroll_expense_id'],
                'credit_account_id' => $accounts['cash_on_hand_id'],
                'secondary_debit_account_id' => null,
                'secondary_credit_account_id' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Petty Cash Expense - Debit: Regular Expenses, Credit: Petty Cash
            [
                'event_name' => 'petty_cash',
                'event_description' => 'Expense paid from petty cash',
                'debit_account_id' => $accounts['regular_expenses_id'],
                'credit_account_id' => $accounts['petty_cash_id'],
                'secondary_debit_account_id' => null,
                'secondary_credit_account_id' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Cash to Bank Transfer - Debit: Bank Account, Credit: Cash on Hand
            [
                'event_name' => 'cash_to_bank_transfer',
                'event_description' => 'Transfer cash to bank account',
                'debit_account_id' => $accounts['bank_account_id'],
                'credit_account_id' => $accounts['cash_on_hand_id'],
                'secondary_debit_account_id' => null,
                'secondary_credit_account_id' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],

            // Bank to Cash Transfer - Debit: Cash on Hand, Credit: Bank Account
            [
                'event_name' => 'bank_to_cash_transfer',
                'event_description' => 'Withdraw cash from bank account',
                'debit_account_id' => $accounts['cash_on_hand_id'],
                'credit_account_id' => $accounts['bank_account_id'],
                'secondary_debit_account_id' => null,
                'secondary_credit_account_id' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('ac_event_mappings')->insert($mappings);
    }
}
