<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Account\LedgerController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\ExpenseController;
use App\Http\Controllers\Account\PaymenttypeController;
use App\Http\Controllers\Account\TransactionController;
use App\Http\Controllers\Account\ExpenseCategoryController;
use App\Http\Controllers\Account\AccountIncomeCategoryController;
use App\Http\Controllers\Account\AccountIncomeController;
use App\Http\Controllers\Account\AdjustmentController;
use App\Http\Controllers\Account\InvestorController;


Route::group(['middleware' => ['auth', 'CheckUserType', 'DemoMode']], function () {
    // Payment Type
    Route::get('/add/new/payment-type', [PaymenttypeController::class, 'addNewPaymentType'])->name('AddNewPaymentType');
    Route::post('/save/new/payment-type', [PaymenttypeController::class, 'saveNewPaymentType'])->name('SaveNewPaymentType');
    Route::get('/view/all/payment-type', [PaymenttypeController::class, 'viewAllPaymentType'])->name('ViewAllPaymentType');
    Route::get('/delete/payment-type/{slug}', [PaymenttypeController::class, 'deletePaymentType'])->name('DeletePaymentType');
    Route::get('/edit/payment-type/{slug}', [PaymenttypeController::class, 'editPaymentType'])->name('EditPaymentType');
    Route::post('/update/payment-type', [PaymenttypeController::class, 'updatePaymentType'])->name('UpdatePaymentType');


    // Expense Category
    Route::get('/add/new/expense-category', [ExpenseCategoryController::class, 'addNewExpenseCategory'])->name('AddNewExpenseCategory');
    Route::post('/save/new/expense-category', [ExpenseCategoryController::class, 'saveNewExpenseCategory'])->name('SaveNewExpenseCategory');
    Route::get('/view/all/expense-category', [ExpenseCategoryController::class, 'viewAllExpenseCategory'])->name('ViewAllExpenseCategory');
    Route::get('/delete/expense-category/{slug}', [ExpenseCategoryController::class, 'deleteExpenseCategory'])->name('DeleteExpenseCategory');
    Route::get('/edit/expense-category/{slug}', [ExpenseCategoryController::class, 'editExpenseCategory'])->name('EditExpenseCategory');
    Route::post('/update/expense-category', [ExpenseCategoryController::class, 'updateExpenseCategory'])->name('UpdateExpenseCategory');

    // Income Category
    Route::get('/add/new/income-category', [AccountIncomeCategoryController::class, 'addNewIncomeCategory'])->name('AddNewIncomeCategory');
    Route::post('/save/new/income-category', [AccountIncomeCategoryController::class, 'saveNewIncomeCategory'])->name('SaveNewIncomeCategory');
    Route::get('/view/all/income-category', [AccountIncomeCategoryController::class, 'viewAllIncomeCategory'])->name('ViewAllIncomeCategory');
    Route::get('/delete/income-category/{slug}', [AccountIncomeCategoryController::class, 'deleteIncomeCategory'])->name('DeleteIncomeCategory');
    Route::get('/edit/income-category/{slug}', [AccountIncomeCategoryController::class, 'editIncomeCategory'])->name('EditIncomeCategory');
    Route::post('/update/income-category', [AccountIncomeCategoryController::class, 'updateIncomeCategory'])->name('UpdateIncomeCategory');

    // Income
    Route::get('/add/new/income', [AccountIncomeController::class, 'addNewIncome'])->name('AddNewIncome');
    Route::post('/save/new/income', [AccountIncomeController::class, 'saveNewIncome'])->name('SaveNewIncome');
    Route::get('/view/all/income', [AccountIncomeController::class, 'viewAllIncome'])->name('ViewAllIncome');
    Route::get('/view/income/{id}', [AccountIncomeController::class, 'showIncome'])->name('ViewIncomeDetails');
    Route::get('/print/income/{id}', [AccountIncomeController::class, 'printIncome'])->name('PrintIncome');
    Route::get('/get/income-category-details', [AccountIncomeController::class, 'getIncomeCategoryDetails'])->name('GetIncomeCategoryDetails');

    // Account
    Route::get('/add/new/ac-account', [AccountController::class, 'addNewAcAccount'])->name('AddNewAcAccount');
    Route::post('/save/new/ac-account', [AccountController::class, 'saveNewAcAccount'])->name('SaveNewAcAccount');
    Route::get('/view/all/ac-account', [AccountController::class, 'viewAllAcAccount'])->name('ViewAllAcAccount');
    Route::get('/delete/ac-account/{slug}', [AccountController::class, 'deleteAcAccount'])->name('DeleteAcAccount');
    Route::get('/edit/ac-account/{slug}', [AccountController::class, 'editAcAccount'])->name('EditAcAccount');
    Route::post('/update/ac-account', [AccountController::class, 'updateAcAccount'])->name('UpdateAcAccount');
    Route::get('/get/ac-account/json', [AccountController::class, 'getJsonAcAccount'])->name('GetJsonAcAccount');
    Route::get('/get/ac-account-expense/json', [AccountController::class, 'getJsonAcAccountExpense'])->name('GetJsonAcAccountExpense');
    Route::get('/get/ac-account-revenue/json', [AccountController::class, 'getJsonAcAccountRevenue'])->name('GetJsonAcAccountRevenue');
    Route::get('/get/ac-account-from-payment-types/json', [AccountController::class, 'getJsonAcAccountFromPaymentTypes'])->name('GetJsonAcAccountFromPaymentTypes');


    // Expense 
    Route::get('/add/new/expense', [ExpenseController::class, 'addNewExpense'])->name('AddNewExpense');
    Route::post('/save/new/expense', [ExpenseController::class, 'saveNewExpense'])->name('SaveNewExpense');
    Route::get('/view/all/expense', [ExpenseController::class, 'viewAllExpense'])->name('ViewAllExpense');
    Route::get('/view/expense/{id}', [ExpenseController::class, 'showExpense'])->name('ViewExpenseDetails');
    Route::get('/print/expense/{id}', [ExpenseController::class, 'printExpense'])->name('PrintExpense');
    Route::get('/get/expense-category-details', [ExpenseController::class, 'getExpenseCategoryDetails'])->name('GetExpenseCategoryDetails');


    // Deposit 
    Route::get('/add/new/deposit', [TransactionController::class, 'addNewDeposit'])->name('AddNewDeposit');
    Route::post('/save/new/deposit', [TransactionController::class, 'saveNewDeposit'])->name('SaveNewDeposit');
    Route::get('/view/all/deposit', [TransactionController::class, 'viewAllDeposit'])->name('ViewAllDeposit');
    Route::get('/print/deposit/{id}', [TransactionController::class, 'printDeposit'])->name('PrintDeposit');
    Route::get('/delete/deposit/{slug}', [TransactionController::class, 'deleteDeposit'])->name('DeleteDeposit');
    Route::get('/edit/deposit/{slug}', [TransactionController::class, 'editDeposit'])->name('EditDeposit');
    Route::post('/update/deposit', [TransactionController::class, 'updateDeposit'])->name('UpdateDeposit');

    // Withdraw
    Route::get('/add/new/withdraw', [TransactionController::class, 'addNewWithdraw'])->name('AddNewWithdraw');
    Route::post('/store/withdraw', [TransactionController::class, 'saveNewWithdraw'])->name('StoreWithdraw');
    Route::get('/view/all/withdraw', [TransactionController::class, 'viewAllWithdraw'])->name('ViewAllWithdraw');
    Route::get('/print/withdraw/{id}', [TransactionController::class, 'printWithdraw'])->name('PrintWithdraw');
    Route::get('/get/investor-balance', [TransactionController::class, 'getInvestorBalance'])->name('GetInvestorBalance');
    Route::get('/get/payment-type-balance', [TransactionController::class, 'getPaymentTypeBalance'])->name('GetPaymentTypeBalance');

    // Account Adjustment
    Route::get('/view/all/adjustment', [AdjustmentController::class, 'index'])->name('ViewAllAdjustment');
    Route::get('/create/adjustment', [AdjustmentController::class, 'create'])->name('CreateAdjustment');
    Route::post('/store/adjustment', [AdjustmentController::class, 'store'])->name('StoreAdjustment');

    // Investor Management
    Route::get('/view/all/investor', [InvestorController::class, 'index'])->name('ViewAllInvestor');
    Route::get('/create/investor', [InvestorController::class, 'create'])->name('CreateInvestor');
    Route::post('/store/investor', [InvestorController::class, 'store'])->name('StoreInvestor');
    Route::get('/view/investor/{id}', [InvestorController::class, 'show'])->name('ViewInvestorDetails');
    Route::get('/edit/investor/{id}', [InvestorController::class, 'edit'])->name('EditInvestor');
    Route::post('/update/investor/{id}', [InvestorController::class, 'update'])->name('UpdateInvestor');

    // Ledger 
    Route::get('/ledger', [LedgerController::class, 'index'])->name('ledger.index');
    Route::get('/ledger/journal', [LedgerController::class, 'journal'])->name('journal.index');
    Route::get('/ledger/balance-sheet', [LedgerController::class, 'balanceSheet'])->name('ledger.balance_sheet');
    Route::get('/ledger/income-statement', [LedgerController::class, 'incomeStatement'])->name('ledger.income_statement');
});
