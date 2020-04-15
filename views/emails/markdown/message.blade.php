@component('emails.markdown.layout')
    {{-- Header --}}
    @slot('header')
        @component('emails.markdown.header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('emails.markdown.subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('emails.markdown.footer')
            © {{ date('Y') }} {{ config('app.name') }}. {{ twillTrans('twill::lang.emails.all-rights-reserved') }}
        @endcomponent
    @endslot
@endcomponent
