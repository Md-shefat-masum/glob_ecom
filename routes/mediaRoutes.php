<?php

use App\Http\Controllers\MediaUploaderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Media Upload Routes
|--------------------------------------------------------------------------
|
| Routes for handling file uploads with FilePond lazy upload functionality
|
*/

Route::middleware(['auth'])->prefix('media')->name('media.')->group(function () {
    
    // FilePond upload routes
    Route::post('/upload', [MediaUploaderController::class, 'upload'])->name('upload');
    Route::delete('/revert', [MediaUploaderController::class, 'revert'])->name('revert');
    Route::get('/load/{id}', [MediaUploaderController::class, 'load'])->name('load');
    Route::get('/fetch', [MediaUploaderController::class, 'fetch'])->name('fetch');
    
    // Media management routes - specific routes must come before parameterized routes
    Route::get('/find-by-path', [MediaUploaderController::class, 'findByPath'])->name('find-by-path');
    Route::post('/delete-by-path', [MediaUploaderController::class, 'deleteByPath'])->name('delete-by-path');
    Route::post('/mark-permanent', [MediaUploaderController::class, 'markAsPermanent'])->name('mark-permanent');
    
    // Parameterized routes (must come after specific routes)
    Route::get('/{id}', [MediaUploaderController::class, 'show'])->name('show');
    Route::delete('/{id}', [MediaUploaderController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/replace', [MediaUploaderController::class, 'replace'])->name('replace');
});

