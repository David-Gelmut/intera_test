<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Crypt;

class NewMessageNotification extends Notification
{
    use Queueable;

    public string $decryptedText = '';

    /**
     * Create a new notification instance.
     */
    public function __construct(public Message $message)
    {
        $this->message = $message->loadMissing('attachments', 'user');

        if ($message->text) {
            try {
                $this->decryptedText = Crypt::decryptString($message->text);
            } catch (\Exception $e) {
                $this->decryptedText = $message->text;
            }
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
        $mail = (new MailMessage)
            ->subject('У вас новое сообщение от ' . ($this->message->user?->name ?? 'Пользователя'))
            ->greeting('Здравствуйте!')
            ->line('У вас есть новое непрочитанное сообщение в мессенджере от ' . $this->message->user->name . ':');

        if ($this->decryptedText) {
            $mail->line('"' . $this->decryptedText . '"');
        }

        if ($this->message->attachments && $this->message->attachments->count() > 0) {
            $mail->line('Прикрепленные файлы:');
            foreach ($this->message->attachments as $file) {
                $url = config('app.url') . $file->file_path;
                $mail->line('• ' . $file->file_name . ': ' . $url);
            }
        }

        return $mail->action('Перейти в диалог', config('app.frontend_url') . '/chat')
            ->line('Спасибо, что используете наше приложение!');
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
