<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Создать HTTP-ответ после успешного входа.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[\Override] public function toResponse($request): JsonResponse
    {
        $user = Auth::user();


        return response()->json([
            'two_factor' => false, // Fortify требует этот флаг для работы
            'user' => $user
        ]);
    }
}
