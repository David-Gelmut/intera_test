<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Responses\LoginResponse;
use App\Http\Responses\RegisterResponse;
use App\Http\Responses\ResetPasswordUrl;
use App\Http\Responses\ResetPasswordView;
use App\Http\Responses\VerifyEmailUrl;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Fortify;
use Illuminate\Auth\Notifications\ResetPassword;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

        VerifyEmail::createUrlUsing(new VerifyEmailUrl());

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()) . '|' . $request->ip()
            );
        });

        Fortify::resetPasswordView(new ResetPasswordView());
        ResetPassword::createUrlUsing(new ResetPasswordUrl());


       /* // Кастомизируем URL в письме сброса пароля
        Fortify::resetPasswordView(function ($request) {
            // Получаем токен и email из запроса Laravel
            $token = $request->route('token');
            $email = $request->query('email');

            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173') . '/reset-password';

            $finalUrl = $frontendUrl . '/' . $token . '?email=' . call_user_func('urlencode', $email);

            return redirect()->away($finalUrl);
        });

        // Исправляем ошибку "Route [password.reset] not defined"
        // Напрямую говорим Laravel, какую ссылку вставлять в письмо "Забыл пароль"
        ResetPassword::createUrlUsing(function ($user, string $token) {

            // Базовый URL вашего Vue-приложения
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173') . '/reset-password';

            // Собираем ссылку: http://localhost:3000/reset-password/TOKEN?email=USER_EMAIL
            return $frontendUrl . '/' . $token . '?email=' . call_user_func('urlencode', $user->getEmailForPasswordReset());
        });*/
    }
}
