@component('mail::message')

Please click below to reset your password

@component('mail::button', ['url' =>  config('app.vue_app_url') ."/resetPassword?token={$data->token}"])
    Click Here
@endcomponent
If you have any questions, please send an email to celtenglish@celt.az.
<br>
Regards,<br>
{{ config('app.name') }}
@endcomponent
