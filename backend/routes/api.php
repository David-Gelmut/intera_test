<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Converter\ConvertController;
use App\Http\Controllers\Parser\CompanyController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->name('login');
Route::post('/auth/register', [AuthController::class, 'register'])->name('register');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/companies', [CompanyController::class, 'store'])->name('company.store');
    Route::post('/convert', ConvertController::class);
});
