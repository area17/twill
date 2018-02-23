@component('cms-toolkit::emails.html.layout')
{{-- Header --}}
@slot('header')
@component('cms-toolkit::emails.html.header', ['url' => config('app.url')])
# {{ config('app.name') }}&nbsp;<span class="envlabel envlabel--{{ app()->environment() }}">{{ app()->environment() }}</span>
## {{ $title or $actionText }}
@endcomponent
@endslot

{{-- Body --}}
Hello!

{{ $copy }}

Regards,<br>
{{ config('app.name') }}

{{-- Button --}}
@slot('button')
@component('cms-toolkit::emails.html.button', ['url' => $url])
{{ $actionText }}
@endcomponent
@endslot

{{-- Subcopy --}}
@slot('subcopy')
@component('cms-toolkit::emails.html.subcopy')
If you are having trouble clicking the "{{ $actionText }}" button, copy and paste the URL below into your web browser: [{{ $url }}]({{ $url }})
@endcomponent
@endslot

{{-- Footer --}}
@slot('footer')
@component('cms-toolkit::emails.html.footer')
&copy; {{ date('Y') }}  &mdash; CMS powered by AREA 17. All rights reserved.
@endcomponent
@endslot
@endcomponent
