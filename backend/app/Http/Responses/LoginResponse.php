<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Создать HTTP-ответ после успешного входа.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[\Override] public function toResponse($request)
    {
        // Получаем текущего вошедшего пользователя
        $user = Auth::user();

        // Если запрос пришел от Vue (ожидается JSON)
        if ($request->wantsJson()) {
            return response()->json([
                'two_factor' => false, // Fortify требует этот флаг для работы
                'user' => $user
            ], 200);
        }

        // Стандартный редирект для обычных веб-страниц (на всякий случай)
        return redirect()->intended(config('fortify.home'));
    }
}
