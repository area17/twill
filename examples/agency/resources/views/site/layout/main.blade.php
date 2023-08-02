<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <title>@yield('title')</title>
    @stack('extra_css')
</head>
<body>
<header>
    @include('site.nav.header')
</header>
<main class="p-4 tablet-h:p-6 laptop:p-8">
    @yield('main')
</main>

@stack('scripts')
<script src="{{ asset('/twill-image.js') }}"></script>
</body>

</html>
