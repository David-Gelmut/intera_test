<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    #[\Override] public function toResponse($request): JsonResponse
    {
        $userId = $request->user()->id;
        $user = User::query()->where('id',$userId)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        event(new Registered($user));

        return new JsonResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }
}
