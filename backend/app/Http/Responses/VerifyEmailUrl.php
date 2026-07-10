<?php

namespace App\Http\Responses;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmailUrl
{
    /**
     * Генерирует кастомную ссылку для фронтенда Vue.
     *
     * @param mixed $notifiable Модель пользователя (User)
     * @return string
     */
    public function __invoke($notifiable)
    {
        /*// 1. Генерируем стандартную временную защищенную ссылку Laravel
        $temporarySignedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // 2. Получаем URL фронтенда из конфига (или .env)
        // Лучше использовать env('FRONTEND_URL'), чтобы не хардкодить localhost
        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:3000'), '/') . '/email/verify';

        // 3. Собираем итоговую ссылку
        return $frontendUrl . '?url=' . call_user_func('urlencode', $temporarySignedUrl);*/

        // 1. Генерируем оригинальную временную ссылку бэкенда
        // Laravel создаст её с учетом префикса, например: http://localhost:8000/api/email/verify/...
        $temporarySignedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        if (config('app.env') === 'production') {
            $temporarySignedUrl = str_replace('http://', 'https://', $temporarySignedUrl);
        }

        // 2. Указываем адрес вашего Vue-фронтенда
        $frontendUrl = env('FRONTEND_URL') . '/email-verify';

        // 3. Упаковываем всю ссылку бэкенда в query-параметр для фронтенда
        // В почту улетит: http://localhost:3000/email-verify?url=http%3A%2F%2Flocalhost%3A8000%2Fapi%2Femail%2Fverify%2F...
        return $frontendUrl . '?url=' . call_user_func('urlencode', $temporarySignedUrl);

    }
}
