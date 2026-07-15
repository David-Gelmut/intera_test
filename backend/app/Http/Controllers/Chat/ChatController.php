<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function getChats(Request $request): JsonResponse
    {
        $myId = $request->user()->id;
        $chats = $request->user()->chats()
            ->with(['users' => function ($query) use ($myId) {
                $query
                    ->select('users.id', 'users.name', 'users.email', 'users.role')
                    ->where('users.id', '!=', $myId);
            }])
            ->get()
            ->map(function ($chat) use ($myId) {
                // Считаем сообщения, отправленные НЕ мной, у которых read_at равен null
                $chat->unread_count = \App\Models\Message::where('chat_id', $chat->id)
                    ->where('user_id', '!=', $myId)
                    ->whereNull('read_at')
                    ->count();
                return $chat;
            });

        return response()->json($chats);
    }

    /**
     * Создать новый чат или вернуть существующий.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id' // ID пользователя, с кем создаем чат
        ]);

        $myId = $request->user()->id;
        $recipientId = (int) $request->user_id;

        // Защита: нельзя создать чат с самим собой
        if ($myId === $recipientId) {
            return response()->json(['message' => 'Вы не можете создать чат с самим собой.'], 422);
        }

        // 1. ПРОВЕРКА: Ищем, есть ли уже общий чат между этими двумя пользователями
        // Проверяем чаты текущего пользователя, у которых среди участников есть recipientId
        $existingChat = $request->user()->chats()
            ->whereHas('users', function ($query) use ($recipientId) {
                $query->where('users.id', $recipientId);
            })
            ->first();

        // 2. Если чат уже существует — просто возвращаем его данные во Vue
        if ($existingChat) {
            return response()->json([
                'message' => 'Диалог уже существует.',
                'chat_id' => $existingChat->id
            ]);
        }

        // 3. Если чата нет — создаем новую запись в таблице chats
        $chat = Chat::create([
            'title' => null // Для личных чатов название можно оставить null
        ]);

        // 4. Связываем обоих пользователей с этим чатом в таблице chat_user
        $chat->users()->attach([$myId, $recipientId]);

        return response()->json([
            'message' => 'Новый диалог успешно создан.',
            'chat_id' => $chat->id
        ], 201);
    }

    /**
     * Получить список всех пользователей системы для вкладки "Контакты".
     */
    public function getUsers(Request $request): JsonResponse
    {
        $currentUserId = $request->user()->id;

        // Выбираем всех активных пользователей, кроме текущего залогиненного
        $users = \App\Models\User::select('id', 'name', 'email', 'role')
            ->where('id', '!=', $currentUserId)
            ->where('status', 'active')
            ->get();

        return response()->json($users);
    }

    /**
     * Очистить историю сообщений в чате.
     */
    public function clearMessages(Request $request, $id): JsonResponse
    {
        $chat = $request->user()->chats()->findOrFail($id);
        \App\Models\Message::where('chat_id', $chat->id)->delete();

        return response()->json(['message' => 'История чата успешно очищена.']);
    }

    /**
     * Полностью удалить чат для всех участников.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $chat = $request->user()->chats()->findOrFail($id);
        $chat->delete(); // Каскадно удалит связи в chat_user и сообщения

        return response()->json(['message' => 'Чат успешно удален.']);
    }
}
