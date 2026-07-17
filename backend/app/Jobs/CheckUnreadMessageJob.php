<?php

namespace App\Jobs;

use App\Models\Message;
use App\Notifications\NewMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckUnreadMessageJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Message $message)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $triggerMessage = Message::query()->find($this->message->id);
        if (!$triggerMessage || $triggerMessage->read_at) {
            return; // Если стартовое сообщение уже прочитали — тушим задачу
        }
        $triggerMessage->load('chat.users');

        $unreadMessages = Message::query()
            ->where('chat_id', $triggerMessage->chat_id)
            ->where('user_id', $triggerMessage->user_id)
            ->where('is_notified', false)
            ->where('read_at', null)
            ->with('attachments', 'user')
            ->orderBy('id', 'asc')
            ->get();

        if ($unreadMessages->isEmpty()) {
            return;
        }

        $recipient = $triggerMessage->chat->users
            ->where('id', '!=', $triggerMessage->user_id)
            ->first();

        if ($recipient && $recipient->email) {

            // маркируем всю эту пачку сообщений в БД как "уведомленные"
            // Делаем это ДО отправки письма, чтобы параллельные джобы сразу увидели изменения
            Message::query()
                ->whereIn('id', $unreadMessages->pluck('id'))
                ->update(['is_notified' => true]);

            $recipient->notify(new NewMessageNotification($unreadMessages));
        }

    }
}
