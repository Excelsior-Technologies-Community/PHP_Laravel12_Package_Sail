<?php

use App\Http\Controllers\SailDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('sail')->name('sail.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Existing Sail Features
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [
        SailDashboardController::class,
        'dashboard'
    ])->name('dashboard');

    Route::get('/health', [
        SailDashboardController::class,
        'health'
    ])->name('health');

    Route::get('/system', [
        SailDashboardController::class,
        'system'
    ])->name('system');


    /*
    |--------------------------------------------------------------------------
    | New Sail Features
    |--------------------------------------------------------------------------
    */

    // 4. Database Statistics
    Route::get('/database', [
        SailDashboardController::class,
        'database'
    ])->name('database');


    // 5. Cache Tester
    Route::match(['get', 'post'], '/cache', [
        SailDashboardController::class,
        'cache'
    ])->name('cache');


    // 6. Storage Diagnostics
    Route::get('/storage', [
        SailDashboardController::class,
        'storage'
    ])->name('storage');


    // 7. PHP Extensions
    Route::get('/extensions', [
        SailDashboardController::class,
        'extensions'
    ])->name('extensions');


    // 8. Route Inspector
    Route::get('/routes', [
        SailDashboardController::class,
        'routes'
    ])->name('routes');


    // 9. Laravel Log Viewer
    Route::get('/logs', [
        SailDashboardController::class,
        'logs'
    ])->name('logs');


    // 10. Environment Diagnostics
    Route::get('/environment', [
        SailDashboardController::class,
        'environment'
    ])->name('environment');


    // 11. Application Configuration
    Route::get('/configuration', [
        SailDashboardController::class,
        'configuration'
    ])->name('configuration');


    // 12. Server Metrics
    Route::get('/metrics', [
        SailDashboardController::class,
        'metrics'
    ])->name('metrics');

    // 13. Interactive Live Docker Container Health Dashboard
    Route::get('/container-health', [
        SailDashboardController::class,
        'containerHealth'
    ])->name('container-health');

    Route::get('/container-health/json', [
        SailDashboardController::class,
        'containerTelemetryJson'
    ])->name('container-health.json');

    // 14. Sail Volume Database Dump & Backup Exporter
    Route::get('/backups', [
        \App\Http\Controllers\SailBackupController::class,
        'index'
    ])->name('backups');

    Route::post('/backups/create', [
        \App\Http\Controllers\SailBackupController::class,
        'create'
    ])->name('backups.create');

    Route::post('/backups/restore', [
        \App\Http\Controllers\SailBackupController::class,
        'restore'
    ])->name('backups.restore');

    Route::get('/backups/download/{filename}', [
        \App\Http\Controllers\SailBackupController::class,
        'download'
    ])->name('backups.download');

    Route::delete('/backups/delete/{filename}', [
        \App\Http\Controllers\SailBackupController::class,
        'delete'
    ])->name('backups.delete');
});

