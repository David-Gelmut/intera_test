<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class UserController extends Controller
{
    /**
     * Получить список всех пользователей.
     */
    public function index(): JsonResponse
    {
        return response()->json(User::query()->latest()->get());
    }

    /**
     * Обновить роль или статус пользователя.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);

        // Не позволяем администратору забанить самого себя
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Вы не можете изменить статус или роль самому себе.'], 422);
        }

        // Валидация входящих данных (включая валидацию Enum для роли)
        $validated = $request->validate([
            'role' => ['required', new Enum(UserRole::class)],
            'status' => ['required', 'string', 'in:active,banned'],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Данные пользователя успешно обновлены.',
            'user' => $user
        ]);
    }
}
