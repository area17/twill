@php
    $original_field_name = $field_name ?? 'content';

    $media_library_route = route('admin.media-library.medias.index');
    $media_crop_route = route('admin.media-library.medias.crop');
    $media_thumbnail_route = route('admin.media-library.medias.thumbnail');
    $file_library_route = route('admin.file-library.files.index');
    $block_preview_route = route('admin.blocks.preview');

    $blocks_css = revAsset('blocks.css');
    $blocks_js = config('cms-toolkit.blocks.blocks_js_rev') ? revAsset(config('cms-toolkit.blocks.blocks_js_path')) : config('cms-toolkit.blocks.blocks_js_path');
@endphp

@include('cms-toolkit::layouts.form_partials.block_settings')

<section class="box box-sir-trevor">
    <header class="header_small">
        <h3><b>{{ $field_title or 'Block Editor' }}</b></h3>
    </header>
    @php
        $field_name = isset($field_wrapper) ? $field_wrapper . '[' . $original_field_name . ']' : $original_field_name;
    @endphp
    <div class="input text optional">
        <textarea
            class="text optional"
            name="{{ $field_name }}"
            id="{{ $field_name }}"
            data-behavior="sir_trevor"
            data-sir-trevor-defaults="sir_trevor_defaults"
            data-sir-trevor-settings="sir_trevor_settings"
            data-sir-trevor-js="assets/admin/vendor/sir-trevor/sir-trevor-with-eventable.min.js, assets/admin/vendor/medium-editor/medium-editor.min.js, {{ ltrim($blocks_js, '/') }}"
            data-sir-trevor-css="assets/admin/vendor/sir-trevor/sir-trevor.css, assets/admin/vendor/sir-trevor/sir-trevor-icons.css, assets/admin/vendor/medium-editor/medium-editor.css, assets/admin/vendor/medium-editor/themes/flat.min.css, {{ ltrim($blocks_css, '/') }}">
            @if ((Form::old($field_name)) !== null)
                {{ Form::old($field_name) }}
            @elseif (isset($item))
                @if (isset($field_wrapper))
                    @if ($item->$field_wrapper)
                        {{ $item->$field_wrapper->$original_field_name }}
                    @endif
                @else
                    {{ $item->$original_field_name }}
                @endif
            @endif
        </textarea>
    </div>
</section>

@section('extra_css')
    <style>
        .simple_form .a17cms-editor {
            max-width: none;
        }

        .simple_form .a17cms-editor .a17cms-editor-mode {
            max-width: 1000px;
            margin: 0px auto;
        }

        .simple_form .a17cms-editor .a17cms-preview-mode a {
            pointer-events: none;
        }

        .simple_form .a17cms-editor .a17cms-preview-mode {
            min-height: 63px;
        }

        @if(count(getLocales()) === 1)
            .simple_form .a17cms-editor .a17cms-editor-mode .lang_tag {
                display: none;
            }
        @endif

        @if (isset($blockLimit) && $blockLimit === 1)
            .st-block__ui {
                display: none !important;
            }
        @endif
    </style>
@stop
