<?php

use App\Http\Controllers\SailDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('sail')->name('sail.')->group(function () {

    // Sail Environment Dashboard
    Route::get('/dashboard', [SailDashboardController::class, 'dashboard'])
        ->name('dashboard');

    // Application Health Check
    Route::get('/health', [SailDashboardController::class, 'health'])
        ->name('health');

    // System Information Dashboard
    Route::get('/system', [SailDashboardController::class, 'system'])
        ->name('system');
});