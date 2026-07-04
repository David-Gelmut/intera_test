<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmailVerificationController extends Controller
{
    /**
     * Подтверждение почты пользователя по подписанной ссылке.
     */
    public function verify(Request $request, $id, $hash): JsonResponse
    {
        // 1. Находим пользователя в базе данных по ID из ссылки
        $user = User::findOrFail($id);

        // 2. Проверяем, совпадает ли хэш почты из ссылки с реальным хэшем в базе
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Недействительная ссылка верификации.'], 403);
        }

        // 3. Если пользователь уже подтвердил почту ранее
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email уже был подтвержден ранее.']);
        }

        // 4. Отмечаем почту подтвержденной в базе данных
        if ($user->markEmailAsVerified()) {
            // Вызываем стандартное системное событие Laravel
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return response()->json(['message' => 'Email успешно подтвержден!']);
    }
}
