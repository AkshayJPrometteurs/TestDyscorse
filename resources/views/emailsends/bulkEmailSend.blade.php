<x-mail::message>
# Hello, {{ $data['name'] }}
<br>
{!! $data['body'] !!}
<br>
<strong>Thanks,<br>
{{ config('app.name') }} Team.</strong>
</x-mail::message>
