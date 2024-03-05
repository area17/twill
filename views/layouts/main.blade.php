<!DOCTYPE html>
<html dir="ltr" lang="{{ config('twill.locale', 'en') }}">

<head>
    @include('twill::partials.head')
    @if (app()->environment('local', 'development') && config('twill.dev_mode', false))
        <script>
            window.hmr_url = '{{config('twill.dev_mode_url', 'http://localhost:8080')}}';
        </script>
    @endif
</head>

<body class="env env--{{ app()->environment() }} @yield('appTypeClass')">
@include('twill::partials.icons.svg-sprite')
<x-twill.partials::navigation.overlay/>
<div class="a17">
    <header class="header">
        <div class="container">
            <x-twill.partials::navigation.title/>
            <x-twill.partials::navigation.primary/>
            <div class="header__user" id="headerUser" v-cloak>
                <x-twill.partials::navigation.user/>
            </div>
            @if (config('twill.enabled.search', false) && !($isDashboard ?? false))
                <div class="headerSearch" id="searchApp">
                    <a href="#" class="headerSearch__toggle" @click.prevent="toggleSearch">
                        <span v-svg symbol="search" v-show="!open"></span>
                        <span v-svg symbol="close_modal" v-show="open"></span>
                    </a>
                    <transition name="fade_search-overlay" @after-enter="afterAnimate">
                        <div class="headerSearch__wrapper" :style="positionStyle" v-show="open" v-cloak>
                            <div class="headerSearch__overlay" :style="positionStyle" @click="toggleSearch"></div>
                            <a17-search endpoint="{{ route(config('twill.dashboard.search_endpoint')) }}"
                                        :open="open" :opened="opened"></a17-search>
                        </div>
                    </transition>
                </div>
            @endif
        </div>
    </header>
    @hasSection('primaryNavigation')
        @yield('primaryNavigation')
    @else
        <x-twill.partials::navigation.secondary/>
        <x-twill.partials::navigation.tertiary/>
        <x-twill.partials::navigation.breadcrumbs :breadcrumb="$breadcrumb ?? []"/>
    @endif
    <section class="main">
        <div class="app" id="app" v-cloak>
            @yield('content')
            @if (config('twill.enabled.media-library') || config('twill.enabled.file-library'))
                <a17-medialibrary ref="mediaLibrary"
                                  :authorized="{{ json_encode(auth('twill_users')->user()->can('edit-media-library')) }}"
                                  :extra-metadatas="{{ json_encode(array_values(config('twill.media_library.extra_metadatas_fields', []))) }}"
                                  :translatable-metadatas="{{ json_encode(array_values(config('twill.media_library.translatable_metadatas_fields', []))) }}">
                </a17-medialibrary>
                <a17-dialog ref="deleteWarningMediaLibrary"
                            modal-title="{{ twillTrans('twill::lang.media-library.dialogs.delete.delete-media-title') }}"
                            confirm-label="{{ twillTrans('twill::lang.media-library.dialogs.delete.delete-media-confirm') }}">
                    <p class="modal--tiny-title">
                        <strong>{{ twillTrans('twill::lang.media-library.dialogs.delete.delete-media-title') }}</strong>
                    </p>
                    <p>{!! twillTrans('twill::lang.media-library.dialogs.delete.delete-media-desc') !!}</p>
                </a17-dialog>
                <a17-dialog ref="replaceWarningMediaLibrary"
                            modal-title="{{ twillTrans('twill::lang.media-library.dialogs.replace.replace-media-title') }}"
                            confirm-label="{{ twillTrans('twill::lang.media-library.dialogs.replace.replace-media-confirm') }}">
                    <p class="modal--tiny-title">
                        <strong>{{ twillTrans('twill::lang.media-library.dialogs.replace.replace-media-title') }}</strong>
                    </p>
                    <p>{!! twillTrans('twill::lang.media-library.dialogs.replace.replace-media-desc') !!}</p>
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

@if (config('twill.enabled.users-management'))
    <form style="display: none" method="POST" action="{{ route(config('twill.admin_route_name_prefix') . 'logout') }}" data-logout-form>
        @csrf
    </form>
@endif

<script>
    window['{{ config('twill.js_namespace') }}'] = {}
    window['{{ config('twill.js_namespace') }}'].debug = {{ config('twill.debug') ? 'true' : 'false' }};
    window['{{ config('twill.js_namespace') }}'].version = '{{ config('twill.version') }}';
    window['{{ config('twill.js_namespace') }}'].twillLocalization = {!! json_encode($twillLocalization) !!};
    window['{{ config('twill.js_namespace') }}'].STORE = {}
    window['{{ config('twill.js_namespace') }}'].STORE.form = {}
    window['{{ config('twill.js_namespace') }}'].STORE.config = {
        publishDateDisplayFormat: '{{ config('twill.publish_date_display_format') }}',
    }
    window['{{ config('twill.js_namespace') }}'].STORE.medias = {}
    window['{{ config('twill.js_namespace') }}'].STORE.medias.types = []
    window['{{ config('twill.js_namespace') }}'].STORE.medias.config = {
        useWysiwyg: {{ config('twill.media_library.media_caption_use_wysiwyg') ? 'true' : 'false' }},
        wysiwygOptions: {!! json_encode(config('twill.media_library.media_caption_wysiwyg_options')) !!}
    }
    window['{{ config('twill.js_namespace') }}'].STORE.languages = {!! json_encode(getLanguagesForVueStore($form_fields ?? [], $translate ?? false)) !!};

    @if (config('twill.enabled.media-library'))
        window['{{ config('twill.js_namespace') }}'].STORE.medias.types.push({
            value: 'image',
            text: '{{ twillTrans('twill::lang.media-library.images') }}',
            total: {{ \A17\Twill\Models\Media::count() }},
            endpoint: '{{ route(config('twill.admin_route_name_prefix') . 'media-library.medias.index') }}',
            tagsEndpoint: '{{ route(config('twill.admin_route_name_prefix') . 'media-library.medias.tags') }}',
            uploaderConfig: {!! json_encode($mediasUploaderConfig) !!}
        })
        window['{{ config('twill.js_namespace') }}'].STORE.medias.showFileName = !!'{{ config('twill.media_library.show_file_name') }}'
    @endif

    @if (config('twill.enabled.file-library'))
        window['{{ config('twill.js_namespace') }}'].STORE.medias.types.push({
            value: 'file',
            text: '{{ twillTrans('twill::lang.media-library.files') }}',
            total: {{ \A17\Twill\Models\File::count() }},
            endpoint: '{{ route(config('twill.admin_route_name_prefix') . 'file-library.files.index') }}',
            tagsEndpoint: '{{ route(config('twill.admin_route_name_prefix') . 'file-library.files.tags') }}',
            uploaderConfig: {!! json_encode($filesUploaderConfig) !!}
        })
    @endif


    @yield('initialStore')

    window.STORE = {}
    window.STORE.form = {}
    window.STORE.publication = {}
    window.STORE.medias = {}
    window.STORE.medias.types = []
    window.STORE.medias.selected = {}
    window.STORE.browsers = {}
    window.STORE.browsers.selected = {}

    @stack('vuexStore')

    window['{{config('twill.js_namespace')}}'].STORE.form.allAvailableBlocks = {!! (string)TwillBlocks::getListOfUsedBlocks() ?: '{}' !!}

</script>
<script src="{{ twillAsset('chunk-vendors.js') }}"></script>
<script src="{{ twillAsset('chunk-common.js') }}"></script>
@stack('extra_js')
</body>

</html>
