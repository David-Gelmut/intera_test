<?php

namespace App\Http\Controllers\Chat;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Получить историю сообщений конкретного чата (с защитой).
     */
    public function getMessages(Request $request, int $chatId): JsonResponse
    {
        // Проверяем, состоит ли текущий залогиненный пользователь в этом чате
        $hasAccess = $request->user()->chats()->where('chat_id', $chatId)->exists();

        if (! $hasAccess) {
            return response()->json([
                'message' => 'У вас нет доступа к переписке в этом диалоге.'
            ], 403);
        }

        // Загружаем сообщения по порядку (от старых к новым) вместе с именами авторов
        $messages = Message::where('chat_id', $chatId)
            ->with('user:id,name')
            ->oldest()
            ->get();

        return response()->json($messages);
    }

    /**
     * Отправить новое сообщение в чат (с бродкастом в Laravel Reverb).
     */
    public function sendMessage(Request $request, int $chatId): JsonResponse
    {
        $request->validate([
            'text' => 'required|string|max:5000',
        ]);
        $myId = $request->user()->id;

        // Проверяем, имеет ли пользователь право писать в этот чат
        $hasAccess = $request->user()->chats()->where('chat_id', $chatId)->exists();

        if (! $hasAccess) {
            return response()->json([
                'message' => 'Вы не являетесь участником этого чата и не можете отправлять сообщения.'
            ], 403);
        }

        $message = Message::create([
            'chat_id' => $chatId,
            'user_id' => $request->user()->id,
            'text' => $request->text,
        ]);

        $message->load('user:id,name');

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    /**
     * Пометить все входящие сообщения в чате как прочитанные.
     */
    public function markAsRead(Request $request, $chatId): JsonResponse
    {
        $hasAccess = $request->user()->chats()->where('chat_id', $chatId)->exists();
        if (!$hasAccess) return response()->json(['message' => 'Доступ запрещен.'], 403);

        // Проставляем текущее время всем чужим непрочитанным сообщениям в этом чате
        Message::where('chat_id', $chatId)
            ->where('user_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
