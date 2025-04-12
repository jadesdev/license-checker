<?php

use App\Http\Controllers\LicenseValidationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('validate', [LicenseValidationController::class, 'validate']);

