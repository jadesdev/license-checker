<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ValidationLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::middleware(['admin'])->prefix('admin')->as('admin.')->group(function () {

    // Admin Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Licence Management
    Route::controller(LicenseController::class)->group(function () {
        // List and Create
        Route::get('access-keys', 'index')->name('access-keys.index');
        Route::get('access-keys/create', 'create')->name('access-keys.create');
        Route::post('access-keys', 'store')->name('access-keys.store');

        // View, Edit, Update, Delete
        Route::get('access-keys/{id}', 'show')->name('access-keys.show');
        Route::get('access-keys/{id}/edit', 'edit')->name('access-keys.edit');
        Route::put('access-keys/{id}', 'update')->name('access-keys.update');
        Route::delete('access-keys/{id}', 'destroy')->name('access-keys.destroy');

        // Special Actions
        Route::post('access-keys/{id}/revoke', 'revoke')->name('access-keys.revoke');
        Route::post('access-keys/{id}/restore', 'restore')->name('access-keys.restore');
        Route::post('access-keys/{id}/reset-domains', 'resetDomains')->name('access-keys.reset-domains');
        Route::post('access-keys/{id}/extend', 'extendValidity')->name('access-keys.extend');
        Route::post('access-keys/{id}/domains', 'manageDomains')->name('access-keys.domains');
    });

    // Validation Logs Management
    Route::controller(ValidationLogController::class)->group(function () {
        // General logs view
        Route::get('validation-logs', 'index')->name('logs.index');

        // Filtered logs
        Route::get('validation-logs/domain/{domain}', 'byDomain')->name('logs.by-domain');
        Route::get('validation-logs/key/{access_key}', 'byKey')->name('logs.by-key');
        Route::get('validation-logs/status/{status}', 'byStatus')->name('logs.by-status');

        // Search and filtering
        Route::get('validation-logs/search', 'search')->name('logs.search');

        // Export options
        Route::get('validation-logs/export', 'export')->name('logs.export');

        // Delete logs
        Route::delete('validation-logs/{id}', 'destroy')->name('logs.destroy');
        Route::post('validation-logs/cleanup', 'cleanup')->name('logs.cleanup'); // Delete old logs
    });

    // Dashboard Statistics
    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'index')->name('dashboard');
        Route::get('stats/usage', 'usageStats')->name('stats.usage');
        Route::get('stats/keys', 'keyStats')->name('stats.keys');
        Route::get('stats/domains', 'domainStats')->name('stats.domains');
    });

    // Settings
    Route::controller(SettingsController::class)->group(function () {
        Route::get('settings', 'index')->name('settings');
        Route::post('settings', 'update')->name('settings.update');

        // License tiers configuration
        Route::get('tiers', 'tiers')->name('tiers');
        Route::post('tiers', 'updateTiers')->name('tiers.update');
    });
});
