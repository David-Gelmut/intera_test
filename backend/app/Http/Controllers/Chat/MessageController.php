<?php

namespace App\Http\Controllers\Chat;

use App\Events\MessageDeleted;
use App\Events\MessageSent;
use App\Events\MessageUpdated;
use App\Http\Controllers\Controller;
use App\Jobs\CheckUnreadMessageJob;
use App\Models\Message;
use App\Models\MessageReaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

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

        $messages = Message::where('chat_id', $chatId)
            ->with('user:id,name')
            ->with('attachments')
            ->with('reactions')
            ->oldest()
            ->get();

        $messages->transform(function ($msg) {
            if ($msg->text) {
                try {
                    $msg->text = Crypt::decryptString($msg->text);
                } catch (\Exception $e) {
                    // Если сообщение старое или не зашифровано, оставляем как есть
                }
            }
            return $msg;
        });

        return response()->json($messages);
    }

    /**
     * Отправить новое сообщение в чат (с бродкастом в Laravel Reverb).
     */
    public function sendMessage(Request $request, int $chatId): JsonResponse
    {
        $request->validate([
            'parent_id'=>'nullable|exists:messages,id',
            'text' => 'nullable|string|max:5000',
            'files'   => 'nullable|array',
            'files.*' => 'file|max:102400', // 100MB
        ]);

        $hasAccess = $request->user()->chats()->where('chat_id', $chatId)->exists();

        if (! $hasAccess) {
            return response()->json([
                'message' => 'Вы не являетесь участником этого чата и не можете отправлять сообщения.'
            ], 403);
        }

        $message = Message::create([
            'parent_id' => $request->parent_id,
            'chat_id' => $chatId,
           // 'user_id' => auth()->id(),
            'user_id' => $request->user()->id,
            'text'    => $request->text ? Crypt::encryptString($request->text) : null,
        ]);


        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('chat_attachments', 'public');

                $mime = $file->getMimeType();
                $fileType = 'file';

                if (str_contains($mime, 'image')) {
                    $fileType = 'image';
                } elseif (str_contains($mime, 'video')) {
                    $fileType = 'video';
                }


                $message->attachments()->create([
                    'file_path' => Storage::disk('public')->url($path),
                    //'file_path' => Storage::url($path),
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $fileType,
                    'file_size' => $file->getSize(),
                    'driver'    => 'local',
                ]);
            }
        }

        $message->load('user:id,name','attachments');

        if ($message->text) {
            $message->text = Crypt::decryptString($message->text);
        }

        broadcast(new MessageSent($message))->toOthers();

        CheckUnreadMessageJob::dispatch($message)->delay(now()->addMinutes(10));

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

    // 1. РЕДАКТИРОВАНИЕ СООБЩЕНИЯ
    public function update(Request $request, Message $message): JsonResponse
    {
        // Защита: редактировать можно только свои сообщения
        if ($message->user_id !== auth()->id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $request->validate([
            'text' => 'required|string',
        ]);

        // Шифруем новый текст
        $message->update([
            'text' => Crypt::encryptString($request->text)
        ]);

        // Для сокета возвращаем ЧИСТЫЙ расшифрованный текст
        $message->text = $request->text;
        $message->load('attachments', 'user', 'reactions');

        // Пушим в сокеты событие обновления, чтобы у собеседника текст тоже изменился
        broadcast(new MessageUpdated($message))->toOthers();

        return response()->json($message);
    }

    // 2. УДАЛЕНИЕ СООБЩЕНИЯ ДЛЯ ВСЕХ
    public function destroy(Message $message): JsonResponse
    {
        if ($message->user_id !== auth()->id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $chatId = $message->chat_id;
        $messageId = $message->id;

        // Удаляем прикрепленные файлы из MinIO/Public
        foreach ($message->attachments as $file) {
            Storage::disk('public')
                ->delete(str_replace( config('app.url') . '/storage/', '', $file->file_path));
        }

        // Удаляем из базы
        $message->delete();

        // Шлем сигнал сокетам: "Удали сообщение с ID таким-то"
        broadcast(new MessageDeleted($chatId, $messageId))->toOthers();

        return response()->json(['success' => true]);
    }

    public function toggleReaction(Request $request, Message $message)
    {

        $request->validate([
            'emoji' => 'required|string',
        ]);

        $userId = auth()->user()->id;
        $emoji = $request->input('emoji');

        $existingReaction = MessageReaction::where('message_id', $message->id)
            ->where('user_id', $userId)
            ->where('emoji', $emoji)
            ->first();

        if ($existingReaction) {
            // Если реакция существует — удаляем её (дизлайк)
            $existingReaction->delete();
            $action = 'removed';
        } else {
            // Если реакции нет — создаем новую
            MessageReaction::create([
                'message_id' => $message->id,
                'user_id' => $userId,
                'emoji' => $emoji,
            ]);
            $action = 'added';
        }

        // Возвращаем обновленный список ВСЕХ реакций для этого сообщения
        // чтобы фронтенд мог синхронизировать состояние интерфейса
        $updatedReactions = $message->reactions()->get();

        return response()->json([
            'status' => 'success',
            'action' => $action,
            'reactions' => $updatedReactions
        ]);
    }
}
