<!DOCTYPE html>
<html dir="ltr" lang="en-US">
    <head>
        @include('twill::partials.head')
    </head>
    <body class="env env--{{ app()->environment() }} @yield('appTypeClass')">
        <div class="svg-sprite">
            {!! File::exists(public_path("/assets/admin/icons/icons.svg")) ? File::get(public_path("/assets/admin/icons/icons.svg")) : '' !!}
            {!! File::exists(public_path("/assets/admin/icons/icons-files.svg")) ? File::get(public_path("/assets/admin/icons/icons-files.svg")) : '' !!}
        </div>
        @if(config('twill.enabled.search', false))
            @partialView(($moduleName ?? null), 'navigation._overlay_navigation', ['search' => true])
        @else
            @partialView(($moduleName ?? null), 'navigation._overlay_navigation')
        @endif
        <div class="a17">
            <header class="header">
                <div class="container">
                    @partialView(($moduleName ?? null), 'navigation._title')
                    @partialView(($moduleName ?? null), 'navigation._global_navigation')
                    <div class="header__user" id="headerUser" v-cloak>
                        @partialView(($moduleName ?? null), 'navigation._user')
                    </div>
                    @if(config('twill.enabled.search', false) && !($isDashboard ?? false))
                      <div class="headerSearch" id="searchApp">
                        <a href="#" class="headerSearch__toggle" @click.prevent="toggleSearch">
                          <span v-svg symbol="search" v-show="!open"></span>
                          <span v-svg symbol="close_modal" v-show="open"></span>
                        </a>
                        <transition name="fade_search-overlay" @after-enter="afterAnimate">
                          <div class="headerSearch__wrapper" :style="positionStyle" v-show="open" v-cloak>
                            <div class="headerSearch__overlay" :style="positionStyle" @click="toggleSearch"></div>
                            <a17-search endpoint="{{ route(config('twill.dashboard.search_endpoint')) }}" :open="open" :opened="opened"></a17-search>
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
                @partialView(($moduleName ?? null), 'navigation._secondary_navigation')
            @endif
            <section class="main">
                <div class="app" id="app" v-cloak>
                    @yield('content')
                    @if (config('twill.enabled.media-library') || config('twill.enabled.file-library'))
                        <a17-medialibrary ref="mediaLibrary"
                                          :authorized="{{ json_encode(auth('twill_users')->user()->can('upload')) }}" :extra-metadatas="{{ json_encode(array_values(config('twill.media_library.extra_metadatas_fields', []))) }}"
                                          :translatable-metadatas="{{ json_encode(array_values(config('twill.media_library.translatable_metadatas_fields', []))) }}"
                        ></a17-medialibrary>
                        <a17-dialog ref="warningMediaLibrary" modal-title="Delete media" confirm-label="Delete">
                            <p class="modal--tiny-title"><strong>Delete media</strong></p>
                            <p>Are you sure ?<br />This change can't be undone.</p>
                        </a17-dialog>
                    @endif
                    <a17-notif variant="success"></a17-notif>
                    <a17-notif variant="error"></a17-notif>
                    <a17-notif variant="info" :auto-hide="false" :important="false"></a17-notif>
                    <a17-notif variant="warning" :auto-hide="false" :important="false"></a17-notif>
                </div>
                <div class="appLoader">
                    <span>
                        <span class="loader"><span></span></span>
                    </span>
                </div>
                @include('twill::partials.footer')
            </section>
        </div>

        <script>
            window.STORE = {}
            window.STORE.form = {}
            window.STORE.medias = {}
            window.STORE.medias.types = []
            window.STORE.languages = {!! json_encode(getLanguagesForVueStore($form_fields ?? [], $translate ?? false)) !!}

            @if (config('twill.enabled.media-library'))
                window.STORE.medias.types.push({
                    value: 'image',
                    text: 'Images',
                    total: {{ \A17\Twill\Models\Media::count() }},
                    endpoint: '{{ route('admin.media-library.medias.index') }}',
                    tagsEndpoint: '{{ route('admin.media-library.medias.tags') }}',
                    uploaderConfig: {!! json_encode($mediasUploaderConfig) !!}
                })
            @endif

            @if (config('twill.enabled.file-library'))
                window.STORE.medias.types.push({
                    value: 'file',
                    text: 'Files',
                    total: {{ \A17\Twill\Models\File::count() }},
                    endpoint: '{{ route('admin.file-library.files.index') }}',
                    tagsEndpoint: '{{ route('admin.file-library.files.tags') }}',
                    uploaderConfig: {!! json_encode($filesUploaderConfig) !!}
                })
            @endif

            @yield('initialStore')
            @stack('vuexStore')
        </script>
        @stack('extra_js')
    </body>
</html>
