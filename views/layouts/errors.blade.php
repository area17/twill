<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('twill::partials.head')
    </head>
    <body class="env env--{{ app()->environment() }}">
        <div class="a17">
            <header class="header">
                <div class="container">
                    @include('twill::partials.navigation._title')
                </div>
            </header>
            <section class="main">
                <div class="app error">
                    <div class="container">
                    @yield('content')
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>
