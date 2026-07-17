<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;

class NewMessageNotification extends Notification
{
    use Queueable;

    public array $preparedMessages = [];
    //public string $decryptedText = '';

    /**
     * Create a new notification instance.
     */
    public function __construct(public Collection $messages)
    {
        foreach ($this->messages as $msg) {
            $decryptedText = '';
            if ($msg->text) {
                try {
                    $decryptedText = Crypt::decryptString($msg->text);
                } catch (\Exception $e) {
                    $decryptedText = $msg->text;
                }
            }

            $this->preparedMessages[] = [
                'text'        => $decryptedText,
                'attachments' => $msg->attachments,
                'time'        => $msg->created_at->format('H:i'),
            ];
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $senderName = $this->messages->first()->user->name ?? 'Пользователя';

        return (new MailMessage)
            ->subject("У вас новые непрочитанные сообщения от {$senderName}")
            ->markdown('emails.new_message', [
                'senderName' => $senderName,
                'messages'   => $this->preparedMessages,
            ]);

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
