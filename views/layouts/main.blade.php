<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('cms-toolkit::partials.head')
    </head>
    <body class="env env--{{ app()->environment() }} s--app">
        @partialView(($moduleName ?? null), 'navigation._global_navigation', [
            'mobile' => true
        ])
        <div class="a17">
            <header class="header">
                <div class="container">
                    @partialView(($moduleName ?? null), 'navigation._title')
                    @partialView(($moduleName ?? null), 'navigation._global_navigation')
                    @partialView(($moduleName ?? null), 'navigation._user')
                </div>
            </header>
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

            // buckets
            window.STORE.buckets = {}

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

            // form
            window.STORE.publication = {}
            window.STORE.form = {}

            // media library
            window.STORE.medias = {}
            window.STORE.medias.types = [
              {
                value: 'image',
                text: 'Images',
                total: 1321
              },
              {
                value: 'video',
                text: 'Vidéos',
                total: 152
              },
              {
                value: 'file',
                text: 'Files',
                total: 81
              }
            ]

            @yield('initialStore')
        </script>
        @stack('extra_js')
    </body>
</html>
