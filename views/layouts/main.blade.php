<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('cms-toolkit::partials.head')
    </head>
    <body class="env env--{{ app()->environment() }} s--app">
        <div class="svg-sprite">
            {!! File::exists(public_path("/assets/admin/icons/icons.svg")) ? File::get(public_path("/assets/admin/icons/icons.svg")) : '' !!}
            {!! File::exists(public_path("/assets/admin/icons/icons-files.svg")) ? File::get(public_path("/assets/admin/icons/icons-files.svg")) : '' !!}
        </div>
        @partialView(($moduleName ?? null), 'navigation._global_navigation', [
            'mobile' => true
        ])
        <div class="a17">
            <header class="header">
                <div class="container">
                    @partialView(($moduleName ?? null), 'navigation._title')
                    @partialView(($moduleName ?? null), 'navigation._global_navigation')
                    <div class="header__user" id="headerUser" v-cloak>
                        @partialView(($moduleName ?? null), 'navigation._user')
                    </div>
                    <div class="header__search" id="searchApp" v-cloak>
                        <a17-search></a17-search>
                    </div>
                </div>
            </header>
            @partialView(($moduleName ?? null), 'navigation._primary_navigation')
            <section class="main">
                <div class="app @yield('appTypeClass')" id="app" v-cloak>
                    @yield('content')
                    @if (config('cms-toolkit.enabled.media-library'))
                        <a17-modal ref="mediaLibrary" title="Media Library" mode="wide">
                            <a17-medialibrary endpoint="{{ route('admin.media-library.medias.index') }}" />
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
            window.STORE.form = {}
            window.STORE.medias = {}
            window.STORE.languages = {!! json_encode(getLanguagesForVueStore($form_fields ?? [], $translate ?? false)) !!}

            @if (config('cms-toolkit.enabled.media-library'))
                window.STORE.medias.tagsEndpoint = '{{ route('admin.media-library.medias.tags') }}'
                window.STORE.medias.uploaderConfig = {!! json_encode($uploaderConfig) !!}
                window.STORE.medias.types = [
                  {
                    value: 'image',
                    text: 'Images',
                    total: 0
                  }
                ]
            @endif

            @yield('initialStore')
            @stack('vuexStore')
        </script>
        @stack('extra_js')
    </body>
</html>
