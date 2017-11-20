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
                    @if (config('cms-toolkit.enabled.media-library'))
                        <a17-modal ref="mediaLibrary" title="Media Library" mode="wide">
                            <a17-medialibrary endpoint="{{ $mediaLibraryUrl }}" />
                        </a17-modal>
                    @endif
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
            window.STORE.languages.all = {!! json_encode(getLanguagesForVueStore()) !!}

            // form
            window.STORE.publication = {}
            window.STORE.form = {}

            // media library
            window.STORE.medias = {}
            window.STORE.medias.types = [
              {
                value: 'image',
                text: 'Images',
                total: 0
              },
              {
                value: 'file',
                text: 'Files',
                total: 0
              }
            ]

            @yield('initialStore')
        </script>
        @stack('extra_js')
    </body>
</html>
