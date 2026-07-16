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
        //$currentMessage = Message::with('chat.users')->find($this->message->id);
        $currentMessage = Message::query()->find($this->message->id);
        $currentMessage->load('chat.users');

        if (!$currentMessage || $currentMessage->is_read) {
            return;
        }

        // Находим получателя (того, кто НЕ является автором сообщения)
        $recipient = $currentMessage->chat->users
            ->where('id', '!=', $currentMessage->user_id)
            ->first();

        if ($recipient && $recipient->email) {
            // Вызываем системное уведомление Laravel
            $recipient->notify(new NewMessageNotification($currentMessage));
        }
    }
}
