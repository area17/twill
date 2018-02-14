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
                <div class="app error container">
                    @yield('content')
                </div>
            </section>
        </div>
    </body>
</html>
