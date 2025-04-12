<?php

use App\Http\Controllers\Admin\LicenseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// admin Routes
Route::middleware(['auth:sanctum', 'throttle:60,1'])->controller(LicenseController::class)->as('admin.')->prefix('admin')->group(function () {
    Route::get('access-keys', 'index')->name('access-keys');

    Route::get('validation-logs', 'logs')->name('logs');
    Route::get('validation-logs/{access_key}', 'forKey')->name('logs.details');
});
