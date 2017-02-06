@php
    $original_field_name = $field_name ?? 'content';

    $blocks_css = revAsset('blocks.css');
    $blocks_js = revAsset('blocks.js');

    $media_library_route = route('admin.media-library.medias.index');
    $media_crop_route = route('admin.media-library.medias.crop');
    $media_thumbnail_route = route('admin.media-library.medias.thumbnail');
    $file_library_route = route('admin.file-library.files.index');
    $block_preview_route = route('admin.blocks.preview');
@endphp

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
            min-height: 60px;
        }
    </style>
@stop

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

      var medium_editor_buttons_blocktext = ['anchor', 'h3'];
      var medium_editor_buttons = ['bold', 'italic', 'strikethrough', 'superscript', 'unorderedlist', 'orderedlist', 'anchor', 'removeFormat'];

      var BLOCK_LANGUAGES = {!! json_encode(getLocales()) !!};

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
            buttons: medium_editor_buttons_blocktext
          },
          anchor: medium_editor_anchor_options,
          paste: medium_editor_paste_options
        }
      }

      sir_trevor_defaults = function() {
        SirTrevor.setBlockOptions("Blocktext", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Blockquote", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Blockseparator", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Imagesimple", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Imagefull", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Imagegrid", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Imagetext", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Button", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Download", DEFAULT_OPTIONS);

        // SirTrevor.setBlockOptions("Blocktextsimple", DEFAULT_OPTIONS);
        // SirTrevor.setBlockOptions("Diaporama", DEFAULT_OPTIONS);
        // SirTrevor.setBlockOptions("List", DEFAULT_OPTIONS);
        // SirTrevor.setBlockOptions("Stats", DEFAULT_OPTIONS);

        // var IMAGE_OPTIONS = $.extend({}, DEFAULT_OPTIONS);
        // IMAGE_OPTIONS.option_crop_ratio = "2.4";

        // var COLLECTION_OPTIONS = $.extend({}, DEFAULT_OPTIONS);
        // COLLECTION_OPTIONS.option_browser = "route('admin.catalog.collections.browser')";
        // SirTrevor.setBlockOptions("Collection", COLLECTION_OPTIONS);
      }

      @if(isset($blockList))
        var blockTypes = {!! json_encode($blockList) !!}
      @else
        var blockTypes = [
            "Blocktext",
            "Blockquote",
            "Blockseparator",
            "Imagesimple",
            "Imagefull",
            "Imagegrid",
            "Imagetext",
            "Button",
            "Download"
            // "Blocktextsimple",
            // "Diaporama"
            // "Collection",
            // "List",
            // "Stats",
        ];
      @endif

      var sir_trevor_settings = {
        blockTypes: blockTypes,
        blockLimit: {{ $blockLimit or 100 }},
        @if (isset($blockLimit) && $blockLimit === 1)
        defaultType: blockTypes[0],
        @endif
      }

    </script>

    @if (isset($blockLimit) && $blockLimit === 1)
    <style>
        .st-block__ui {
            display: none !important;
        }
    </style>
    @endif

</section>
