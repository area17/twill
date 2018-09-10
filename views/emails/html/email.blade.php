@component('twill::emails.html.layout')
{{-- Header --}}
@slot('header')
@component('twill::emails.html.header', ['url' => config('app.url')])
# {{ config('app.name') }}&nbsp;<span class="envlabel envlabel--{{ app()->environment() }}">{{ app()->environment() }}</span>
## {{ $title ?? $actionText }}
@endcomponent
@endslot

{{-- Body --}}
Hello!

{{ $copy }}

Regards,<br>
{{ config('app.name') }}

{{-- Button --}}
@slot('button')
@component('twill::emails.html.button', ['url' => $url])
{{ $actionText }}
@endcomponent
@endslot

{{-- Subcopy --}}
@slot('subcopy')
@component('twill::emails.html.subcopy')
If you are having trouble clicking the "{{ $actionText }}" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endcomponent
@endslot

{{-- Footer --}}
@slot('footer')
@component('twill::emails.html.footer')
&copy; {{ date('Y') }}  &mdash; CMS powered by AREA 17. All rights reserved.
@endcomponent
@endslot
@endcomponent
