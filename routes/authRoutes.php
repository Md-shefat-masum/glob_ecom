<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\DemoMode;
use App\Http\Middleware\CheckUserType;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CkeditorController;

// auth routes
Route::get('/', function () {
    return redirect('/login');
});

Auth::routes([
    'login' => true,
    'register' => false, // Registration Routes...
    'reset' => false, // Password Reset Routes...
    'verify' => false, // Email Verification Routes...
]);

Route::middleware([CheckUserType::class, DemoMode::class])->group(function () {
    Route::get('/change/password/page', [HomeController::class, 'changePasswordPage'])->name('changePasswordPage');
    Route::post('/change/password', [HomeController::class, 'changePassword'])->name('changePassword');
    Route::get('ckeditor', [CkeditorController::class, 'index']);
    Route::post('ckeditor/upload', [CkeditorController::class, 'upload'])->name('ckeditor.upload');
});