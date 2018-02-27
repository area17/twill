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
                    @hasSection('globalNavSearch')
                      <div class="header__search" id="searchApp">
                        <a href="#" class="header__search__toggle" @click.prevent="toggleSearch">
                          <span v-svg symbol="search" v-show="!open"></span>
                          <span v-svg symbol="close_modal" v-show="open"></span>
                        </a>
                        <transition name="fade_search-overlay" @after-enter="afterAnimate">
                          <div class="search__positioner" v-show="open" v-cloak>
                            <div class="search__overlay" @click="toggleSearch"></div>
                            <a17-search endpoint="http://www.mocky.io/v2/5a7b81d43000004b0028bf3d" :open="open" :opened="opened"></a17-search>
                          </div>
                        </transition>
                      </div>
                    @endif
                </div>
            </header>
            @hasSection('primaryNavigation')
                @yield('primaryNavigation')
            @else
                @partialView(($moduleName ?? null), 'navigation._primary_navigation')
                @partialView(($moduleName ?? null), 'navigation._breadcrumb')
                {{-- TODO secondary navigation : need Back-end logic : @partialView(($moduleName ?? null), 'navigation._secondary_navigation') --}}
            @endif
            <section class="main">
                <div class="app @yield('appTypeClass')" id="app" v-cloak>
                    @yield('content')
                    @if (config('cms-toolkit.enabled.media-library'))
                        <a17-medialibrary ref="mediaLibrary" endpoint="{{ route('admin.media-library.medias.index') }}" :authorized="{{ json_encode(auth()->user()->can('edit')) }}"></a17-medialibrary>
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
