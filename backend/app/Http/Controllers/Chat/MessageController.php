<?php

namespace App\Http\Controllers\Chat;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Jobs\CheckUnreadMessageJob;
use App\Models\Message;
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
                    'file_path' => Storage::url($path),
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
}
