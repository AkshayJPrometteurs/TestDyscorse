<x-mail::message>
# Hello {{ $data['name'] }},

Your reset password OTP is <strong>{{ $data['otp'] }}</strong>

Thanks,<br>
{{ config('app.name') }} Teams,
</x-mail::message>
