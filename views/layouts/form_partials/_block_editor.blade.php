@php
    $original_field_name = $field_name ?? 'content';

    $media_library_route = route('admin.media-library.medias.index');
    $media_crop_route = route('admin.media-library.medias.crop');
    $media_thumbnail_route = route('admin.media-library.medias.thumbnail');
    $file_library_route = route('admin.file-library.files.index');
    $block_preview_route = route('admin.blocks.preview');

    $blocks_css = config('cms-toolkit.block-editor.blocks_css_rev') ? revAsset(config('cms-toolkit.block-editor.blocks_css_path')) : config('cms-toolkit.block-editor.blocks_css_path');
    $blocks_js = config('cms-toolkit.block-editor.blocks_js_rev') ? revAsset(config('cms-toolkit.block-editor.blocks_js_path')) : config('cms-toolkit.block-editor.blocks_js_path');

    $sir_trevor_defaults = $sir_trevor_defaults ?? 'sir_trevor_defaults';
@endphp

<section class="box box-sir-trevor">
    <header class="header_small">
        <h3><b>{{ $field_title or 'Block editor' }}</b></h3>
    </header>
    @php
        $field_name = isset($field_wrapper) ? $field_wrapper . '[' . $original_field_name . ']' : $original_field_name;

        if (isset($repeater) && $repeater) {
            $field_name = $moduleName . '[' . $repeaterIndex . '][' . $field_name . ']';
        }
    @endphp
    <div class="input text optional">
        <textarea
            class="text optional"
            name="{{ $field_name }}"
            id="{{ $field_name }}"
            data-behavior="sir_trevor"
            data-sir-trevor-defaults="{{ $sir_trevor_defaults }}"
            data-sir-trevor-settings="sir_trevor_settings"
            data-sir-trevor-js="assets/admin/vendor/sir-trevor/sir-trevor-with-eventable.min.js, assets/admin/vendor/medium-editor/medium-editor.min.js, {{ ltrim($blocks_js, '/') }}"
            data-sir-trevor-css="assets/admin/vendor/sir-trevor/sir-trevor.css, assets/admin/vendor/sir-trevor/sir-trevor-icons.css, assets/admin/vendor/medium-editor/medium-editor.css, assets/admin/vendor/medium-editor/themes/flat.min.css @unless(config('cms-toolkit.block-editor.use_iframes')), {{ ltrim($blocks_css, '/') }} @endunless">
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

<script>
  var medium_editor_paste_options = {
    forcePlainText: true,
    cleanPastedHTML: true,
    cleanAttrs: ['class', 'style', 'dir', 'content', 'itemscope', 'itemprop', 'itemtype', 'property', 'data-behavior', 'data-url', 'font'],
    cleanTags: ['xml', 'body', 'section', 'aside', 'article', 'meta', 'input', 'h1', 'h2', 'font', 'iframe', 'object', 'script', 'style', 'img'],
    cleanReplacements: [
      [new RegExp(/<!--[\s\S]*?-->/g), '']
    ]
  };

  var medium_editor_anchor_options = {
    placeholderText: 'Insert a complete link',
    targetCheckbox: true,
    targetCheckboxText: 'Open in new window'
  };

  var medium_editor_buttons = ['bold', 'italic', 'strikethrough', 'superscript', 'unorderedlist', 'orderedlist', 'anchor', 'removeFormat'];

  var BLOCK_LANGUAGES = {!! json_encode(getLocales()) !!};
  var BLOCK_DEFAULT_LANGUAGE = '{{ getFallbackLocale() }}';

  var DEFAULT_OPTIONS = {
    option_library: "{{ $media_library_route }}",
    option_crop: "{{ $media_crop_route }}",
    option_crop_ratio: '1',
    option_library_thumbnail: "{{ $media_thumbnail_route }}",
    option_browser: "{{ $file_library_route }}",
    option_template: "{{ $block_preview_route }}",
    option_settings: {
      placeholder: {
        text: 'Enter text here.'
      },
      toolbar: {
        buttons: medium_editor_buttons
      },
      anchor: medium_editor_anchor_options,
      paste: medium_editor_paste_options
    }
  }

  sir_trevor_defaults = function() {
    SirTrevor.setBlockOptions("Blocktitle", DEFAULT_OPTIONS);
    SirTrevor.setBlockOptions("Blocktext", DEFAULT_OPTIONS);
    SirTrevor.setBlockOptions("Blockquote", DEFAULT_OPTIONS);
    SirTrevor.setBlockOptions("Image", DEFAULT_OPTIONS);
    SirTrevor.setBlockOptions("Imagetext", DEFAULT_OPTIONS);
    SirTrevor.setBlockOptions("Imagegrid", DEFAULT_OPTIONS);
    SirTrevor.setBlockOptions("Diaporama", DEFAULT_OPTIONS);
    SirTrevor.setBlockOptions("Blockseparator", DEFAULT_OPTIONS);
    SirTrevor.setBlockOptions("Blocktest", DEFAULT_OPTIONS);
    @if (isset($blocks_config))
        {{$blocks_config}}.call();
    @endif
  }

  @if(isset($block_list))
    var blockTypes = {!! json_encode($block_list) !!}
  @else
    var blockTypes = [
        "Blocktitle",
        "Blocktext",
        "Blockquote",
        "Image",
        "Imagegrid",
        "Imagetext",
        "Diaporama",
        "Blockseparator"
    ];
  @endif

  var sir_trevor_settings = {
    blockTypes: blockTypes,
    blockLimit: {{ $block_limit or 100 }},
    @if (isset($block_limit) && $block_limit === 1)
    defaultType: blockTypes[0],
    @endif
  }

</script>

<script>
    $( document ).ready(function() {
        $(window).on('resize', function (){
            $('.blockFrame').each(function () {
                this.style.height = this.contentWindow.document.body.offsetHeight + 'px';
            });
        });
    });
</script>

@push('extra_css')
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

        @if (isset($block_limit) && $block_limit === 1)
            .st-block__ui {
                display: none !important;
            }
        @endif
    </style>
@endpush
