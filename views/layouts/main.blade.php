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
                    <div class="header__user">
                        @partialView(($moduleName ?? null), 'navigation._user')
                    </div>
                </div>
            </header>
            @partialView(($moduleName ?? null), 'navigation._primary_navigation')
            <section class="main">
                <div class="app @yield('appTypeClass')" id="app" v-cloak>
                    @yield('content')
                    <a17-modal ref="mediaLibrary" title="Media Library" mode="wide">
                        <a17-medialibrary endpoint="https://www.mocky.io/v2/59edf8273300000e00b5c7d6" />
                    </a17-modal>
                    <a17-notif variant="success"></a17-notif>
                    <a17-notif variant="error"></a17-notif>
                </div>
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
                // This is the default language
                disabled: true,
                published: true
              },
              {
                shortlabel: 'EN',
                label: 'English - UK',
                value: 'en-UK',
                disabled: false,
                published: false
              },
              {
                shortlabel: 'US',
                label: 'English - US',
                value: 'en-US',
                disabled: false,
                published: true
              },
              {
                shortlabel: 'DE',
                label: 'German',
                value: 'de',
                disabled: false,
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
