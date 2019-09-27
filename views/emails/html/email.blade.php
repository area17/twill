@component('twill::emails.html.layout')
{{-- Header --}}
@slot('header')
@component('twill::emails.html.header', ['url' => config('app.url')])
# {{ config('app.name') }}
## {{ $title ?? $actionText }}
@endcomponent
@endslot

{{-- Body --}}
Hola!

{{ $copy }}

Cordialment,<br>
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
Si tens cap problema a l'hora d'apretar el botó de "{{ $actionText }}" , copia i enganxa aquesta URL al teu navegador: [{{ $url }}]({{ $url }})
@endcomponent
@endslot

{{-- Footer --}}
@slot('footer')
@component('twill::emails.html.footer')
&copy; {{ date('Y') }}  &mdash; recomana.cat - CMS powered by AREA 17. All rights reserved.
@endcomponent
@endslot
@endcomponent
