<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                screens: {
                    'tablet-v': '600px',
                    'tablet-h': '800px',
                    'laptop': '1040px',
                    'desktop': '1280px',
                },
                extend: {},
            },
        }
    </script>
    <title>@yield('title')</title>
    <style>
        .text p {
            margin-bottom: 15px;
        }

        .personVideo {
            border-top: 1px solid rgba(26, 26, 26, 0.13);
        }

        .time__separator {
            position: relative;
            animation: fadeinout 2s steps(1) infinite;
        }

        @keyframes fadeinout {
            0%   {opacity: 0}
            50%  {opacity: 1}
            100% {opacity: 0}
        }

        .workQuote p::before {
            content: open-quote;
        }

        .workQuote p::after {
            content: close-quote;
        }

        .clear::after {
            clear: both;
            content: "";
            display: table;
        }
    </style>
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
