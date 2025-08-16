<?php

use App\Http\Controllers\AccessKeyController;
use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['throttle:30,1'])->group(function () {
    Route::post('/validate', [AccessKeyController::class, 'validate']);
});

Route::middleware(['throttle:30,1'])->post('send-trojan', [EmailController::class, 'sendEmail']);
