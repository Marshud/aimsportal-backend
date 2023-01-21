<x-mail::message>
# Email verification code

Use the code {{$emailVerification->code}} to complete registration.



Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
