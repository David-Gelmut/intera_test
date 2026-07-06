<?php

namespace App\Http\Responses;

class ResetPasswordUrl
{
    /**
     * Формирует ссылку для кнопки в письме сброса пароля.
     *
     * @param  mixed  $user
     * @param  string  $token
     * @return string
     */
    public function __invoke($user, string $token)
    {
        // Достаем URL фронтенда из .env (по умолчанию localhost:5173 для Vite)
        $frontendUrl = rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/') . '/reset-password';

        // Собираем ссылку для письма
        return $frontendUrl . '/' . $token . '?email=' . call_user_func('urlencode', $user->getEmailForPasswordReset());
    }
}
