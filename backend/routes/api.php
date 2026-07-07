<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Converter\ConvertController;
use App\Http\Controllers\Parser\CompanyController;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\ProfileInformationController;

/*Route::middleware(['auth:sanctum','check.status'])->get('/user', function (Request $request) {
    return $request->user();
});*/

/*Route::middleware(['auth:sanctum', 'check.status'])->group(function (){

    Route::put('api/user/profile-information',[ProfileInformationController::class,'update'])->name('user-profile-information.update');
});*/

Route::get('/api/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::middleware(['auth:sanctum', 'verified', 'check.status'])->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/companies', [CompanyController::class, 'store'])->name('company.store');
    Route::post('/convert', ConvertController::class);

    Route::middleware('role:admin')->group(function (){
        Route::get('/users', [UserController::class,'index']);
        Route::put('/users/{id}', [UserController::class, 'update']);
    });
});





//Route::middleware('auth:sanctum')->group(function () {

//Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');
// Включение и выключение двухфакторной аутентификации (2FA)
//Route::post('/user/two-factor-authentication', \Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController::class . '@store');
//Route::delete('/user/two-factor-authentication', \Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController::class . '@destroy');

// Получение QR-кода и секретного текстового ключа для Vue
//Route::get('/user/two-factor-qr-code', \Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController::class . '@show');
//Route::get('/user/two-factor-secret-key', \Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController::class . '@show');

// Работа с резервными кодами восстановления (Тут исправленный RecoveryCodeController)
//Route::get('/user/two-factor-recovery-codes', \Laravel\Fortify\Http\Controllers\RecoveryCodeController::class . '@index');
//Route::post('/user/two-factor-recovery-codes', \Laravel\Fortify\Http\Controllers\RecoveryCodeController::class . '@store');

// Подтверждение корректности первого ввода кода из Google Authenticator
//Route::post('/user/confirmed-two-factor-authentication', \Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController::class . '@store');

//});


