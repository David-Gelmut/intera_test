<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Converter\ConvertController;
use App\Http\Controllers\Parser\CompanyController;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::post('/auth/login', [AuthController::class, 'login'])->name('login');
//Route::post('/auth/register', [AuthController::class, 'register'])->name('register');
// Роут верификации почты (защищен только крипто-подписью 'signed')
Route::get('/api/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
// Обратите внимание: здесь больше НЕТ middleware 'auth' или 'auth:sanctum'
/*Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return response()->json(['message' => 'Email успешно подтвержден!']);
})->middleware(['signed'])->name('verification.verify');*/ // <-- Имя должно быть строго таким!


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

});


Route::middleware('auth:sanctum')->group(function () {


    Route::post('/companies', [CompanyController::class, 'store'])->name('company.store');
    Route::post('/convert', ConvertController::class);

    // Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

    // Включение и выключение двухфакторной аутентификации (2FA)
    Route::post('/user/two-factor-authentication', \Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController::class . '@store');
    Route::delete('/user/two-factor-authentication', \Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController::class . '@destroy');

    // Получение QR-кода и секретного текстового ключа для Vue
    Route::get('/user/two-factor-qr-code', \Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController::class . '@show');
    Route::get('/user/two-factor-secret-key', \Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController::class . '@show');

    // Работа с резервными кодами восстановления (Тут исправленный RecoveryCodeController)
    Route::get('/user/two-factor-recovery-codes', \Laravel\Fortify\Http\Controllers\RecoveryCodeController::class . '@index');
    Route::post('/user/two-factor-recovery-codes', \Laravel\Fortify\Http\Controllers\RecoveryCodeController::class . '@store');

    // Подтверждение корректности первого ввода кода из Google Authenticator
    Route::post('/user/confirmed-two-factor-authentication', \Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController::class . '@store');


});


