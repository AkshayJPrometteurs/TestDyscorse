<x-mail::message>
# Dear Admin,

Your dyscore admin portal credentials are metioned below,

<strong>Email-ID - {{ $data['email'] }}</strong>
<br>
<strong>Password - {{ $data['password'] }}</strong>

@component('mail::button', ['url' => route('login_page')])
Login Now
@endcomponent

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
