@component('mail::message')
# {{ $email->subject }}

{!! $email->body !!}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
