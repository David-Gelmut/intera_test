@component('mail::message')
    # Здравствуйте!

    У вас есть новое непрочитанное сообщение в мессенджере от пользователя **{{ $incomingMessage->user->name }}**:

    @if($decryptedText)
        @component('mail::panel')
            {{ $decryptedText }}
        @endcomponent
    @endif

    @if($incomingMessage->attachments && $incomingMessage->attachments->count() > 0)
        ### Прикрепленные файлы:
        @foreach($incomingMessage->attachments as $file)
            @if($file->file_type === 'image')
                • **Изображение:** [Открыть картинку]({{ config('app.url') . $file->file_path }})
            @elseif($file->file_type === 'video')
                • **Видеоролик:** [Открыть видео]({{ config('app.url') . $file->file_path }})
            @else
                • **Документ:** [Скачать {{ $file->file_name }}]({{ config('app.url') . $file->file_path }})
            @endif
        @endforeach
    @endif

    @component('mail::button', ['url' => config('app.url') . '/chat'])
        Перейти в диалог
    @endcomponent

    С уважением,<br>
    {{ config('app.name') }}
@endcomponent
