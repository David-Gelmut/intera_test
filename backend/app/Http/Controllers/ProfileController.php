<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function updateAvatar(Request $request): JsonResponse
    {
        // 1. Валидация: файл должен быть картинкой (jpeg, png, jpg) размером до 5МБ
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $user = auth()->user();

        if ($request->file('avatar')) {
            // 2. Если у пользователя УЖЕ БЫЛ аватар — удаляем старый файл с диска
            if ($user->avatar_path) {
                // Извлекаем относительный путь (убираем префикс /storage/)
                $oldPath = str_replace('/storage/', '', $user->avatar_path);
                Storage::disk('public')->delete($oldPath);
            }

            // 3. Сохраняем новую картинку в подпапку 'avatars' на диск 'public'
            $path = $request->file('avatar')->store('avatars', 'public');

            // 4. Обновляем путь в базе данных через генерацию URL
            $user->update([
                'avatar_path' => Storage::disk('public')->url($path),
            ]);
        }

        return response()->json([
            'success' => true,
            'avatar_url' => $user->avatar_path,
            'user' => $user
        ]);
    }
}
