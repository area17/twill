<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('cms-toolkit::partials.head')
    </head>
    <body class="env env--{{ app()->environment() }} s--app">
        <div class="a17">
            <header class="header">
                <div class="container">
                    @partialView(($moduleName ?? null), 'navigation._title')
                    @partialView(($moduleName ?? null), 'navigation._global_navigation')
                    @partialView(($moduleName ?? null), 'navigation._user')
                    <div class="ham"><span class="ham__label">Home</span> <button type="button" class="btn ham__btn" data-ham-btn><span class="ham__icon"><span class="ham__line"></span></span></button>
                </div>
            </header>
            @partialView(($moduleName ?? null), 'navigation._global_navigation', [
                'mobile' => true
            ])
            @partialView(($moduleName ?? null), 'navigation._primary_navigation')
            <section class="main">
                @yield('content')
                @include('cms-toolkit::partials.footer')
            </section>
        </div>

        <script>
            window.STORE = {}

            // datatable
            window.STORE.datatable = {}

            // languages
            window.STORE.languages = {}

            window.STORE.languages.all = [
              {
                shortlabel: 'FR',
                label: 'French (Default)',
                value: 'fr-FR',
                published: true
              },
              {
                shortlabel: 'EN',
                label: 'English - UK',
                value: 'en-UK',
                published: false
              },
              {
                shortlabel: 'US',
                label: 'English - US',
                value: 'en-US',
                published: true
              },
              {
                shortlabel: 'DE',
                label: 'German',
                value: 'de',
                published: false
              }
            ]

            @yield('initialStore')
        </script>
        @stack('extra_js')
    </body>
</html>
