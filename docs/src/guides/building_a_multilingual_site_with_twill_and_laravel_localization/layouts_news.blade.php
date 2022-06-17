<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <title>The News</title>
    </head>
    <body>
        <header>
            <ul>
                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <li>
                        <a 
                            rel="alternate" 
                            hreflang="{{ $localeCode }}" 
                            href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                        >
                            {{ strtoupper($localeCode) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </header>
        <main>
            @yield('content')
        </main>
    </body>
</html>
