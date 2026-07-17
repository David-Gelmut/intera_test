@component('mail::message')
# Здравствуйте!

Пока вас не было в сети, пользователь **{{ $senderName }}** отправил вам несколько сообщений:

@foreach($messages as $msg)
* **[{{ $msg['time'] }}]**
@if($msg['text'])
{{ $msg['text'] }}
@endif
@if($msg['attachments'] && $msg['attachments']->count() > 0)
@foreach($msg['attachments'] as $file)
@if($file->file_type === 'image')
    * 🖼️ [Открыть картинку]({{ $file->file_path }})
@elseif($file->file_type === 'video')
    * 🎥 [Открыть ролик]({{$file->file_path }})
@else
    * 📎 [Скачать {{ $file->file_name }}]({{$file->file_path }})
@endif
@endforeach
@endif

@endforeach

@component('mail::button', ['url' => config('app.frontend_url') . '/chat'])
Ответить в мессенджере
@endcomponent

С уважением,<br>
{{ config('app.name') }}
@endcomponent

