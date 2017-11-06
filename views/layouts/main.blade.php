<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('cms-toolkit::partials.head')
    </head>
    <body class="env env--{{ app()->environment() }} s--app">
        <div class="a17">
            <header class="header">
                <div class="container">
                    @partialView($moduleName, 'navigation._title')
                    @partialView($moduleName, 'navigation._global_navigation')
                    @partialView($moduleName, 'navigation._user')
                    <div class="ham"><span class="ham__label">Home</span> <button type="button" class="btn ham__btn" data-ham-btn><span class="ham__icon"><span class="ham__line"></span></span></button>
                </div>
            </header>
            @partialView($moduleName, 'navigation._global_navigation', [
                'mobile' => true
            ])
            @partialView($moduleName, 'navigation._primary_navigation')
            <section class="main">
                @yield('content')
                @include('cms-toolkit::partials.footer')
            </section>
        </div>
        @stack('extra_js')
    </body>
</html>
