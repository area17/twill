<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('cms-toolkit::partials.head')
    </head>
    <body class="env env--{{ app()->environment() }} s--app">
        <div class="a17">
            <header class="header">
                <div class="container">
                    @include('cms-toolkit::partials.navigation._title')
                </div>
            </header>
            <section class="main">
                @yield('content')
                @include('cms-toolkit::partials.footer')
            </section>
        </div>
    </body>
</html>
