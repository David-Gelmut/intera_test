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

        $frontendUrl = env('FRONTEND_URL') . '/email-verify';

        return $frontendUrl . '?url=' . call_user_func('urlencode', $temporarySignedUrl);

    }
}
