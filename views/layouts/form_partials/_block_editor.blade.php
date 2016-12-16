@set('blocks_css', ltrim(rev_asset('blocks.css'), '/'))
@set('blocks_js', ltrim(rev_asset('blocks.js'), '/'))

@set('original_field_name', $field_name ?? 'content')

@set('media_library_route', route('admin.media-library.medias.index'))
@set('media_crop_route', route('admin.media-library.medias.crop'))
@set('media_thumbnail_route', route('admin.media-library.medias.thumbnail'))
@set('file_library_route', route('admin.file-library.files.index'))
@set('block_preview_route', route('admin.blocks.preview'))

@section('extra_js')
    <!--  Start async webfont loading -->
    <link rel="preload" href="https://cloud.typography.com/6162114/6171952/css/fonts.css" as="style" onload="this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cloud.typography.com/6162114/6171952/css/fonts.css"></noscript>
    <script>
        var THG_webfonts_stylesheet_url = 'https://cloud.typography.com/6162114/6171952/css/fonts.css';
        /*! https://github.com/filamentgroup/loadCSS ©2016 Scott Jehl, Zach Leat, Filament Group, Inc. Licensed MIT */
        (function(w){var loadCSS=function(href,before,media){var doc=w.document;var ss=doc.createElement("link");var ref;if(before)ref=before;else{var refs=(doc.body||doc.getElementsByTagName("head")[0]).childNodes;ref=refs[refs.length-1]}var sheets=doc.styleSheets;ss.rel="stylesheet";ss.href=href;ss.media="only x";function ready(cb){if(doc.body)return cb();setTimeout(function(){ready(cb)})}ready(function(){ref.parentNode.insertBefore(ss,before?ref:ref.nextSibling)});var onloadcssdefined=function(cb){var resolvedHref=ss.href;var i=sheets.length;while(i--)if(sheets[i].href===resolvedHref)return cb();setTimeout(function(){onloadcssdefined(cb)})};function loadCB(){if(ss.addEventListener)ss.removeEventListener("load",loadCB);ss.media=media||"all"}if(ss.addEventListener)ss.addEventListener("load",loadCB);ss.onloadcssdefined=onloadcssdefined;onloadcssdefined(loadCB);return ss};if(typeof exports!=="undefined")exports.loadCSS=loadCSS;else w.loadCSS=loadCSS})(typeof global!=="undefined"?global:this);
        (function(w){if(!w.loadCSS)return;var rp=loadCSS.relpreload={};rp.support=function(){try{return w.document.createElement("link").relList.supports("preload")}catch(e){return false}};rp.poly=function(){var links=w.document.getElementsByTagName("link");for(var i=0;i<links.length;i++){var link=links[i];if(link.rel==="preload"&&link.getAttribute("as")==="style"){w.loadCSS(link.href,link);link.rel=null}}};if(!rp.support()){rp.poly();var run=w.setInterval(rp.poly,300);if(w.addEventListener)w.addEventListener("load",function(){w.clearInterval(run)});if(w.attachEvent)w.attachEvent("onload",function(){w.clearInterval(run)})}})(this);
        function onloadCSS(ss,callback){var called;function newcb(){if(!called&&callback){called=true;callback.call(ss)}}if(ss.addEventListener)ss.addEventListener("load",newcb);if(ss.attachEvent)ss.attachEvent("onload",newcb);if("isApplicationInstalled"in navigator&&"onloadcssdefined"in ss)ss.onloadcssdefined(newcb)};
    </script>
    <!--  End async webfont loading -->
@stop

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
    @set('field_name', isset($field_wrapper) ? $field_wrapper . '[' . $original_field_name . ']' : $original_field_name)
    <div class="input text optional">
        <textarea
            class="text optional"
            name="{{ $field_name }}"
            id="{{ $field_name }}"
            data-behavior="sir_trevor"
            data-sir-trevor-defaults="sir_trevor_defaults"
            data-sir-trevor-settings="sir_trevor_settings"
            data-sir-trevor-js="assets/admin/vendor/sir-trevor/sir-trevor-with-eventable.min.js, assets/admin/vendor/medium-editor/medium-editor.min.js, {{ $blocks_js }}"
            data-sir-trevor-css="assets/admin/vendor/sir-trevor/sir-trevor.css, assets/admin/vendor/sir-trevor/sir-trevor-icons.css, assets/admin/vendor/medium-editor/medium-editor.css, assets/admin/vendor/medium-editor/themes/flat.min.css, {{ $blocks_css }}">
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
        SirTrevor.setBlockOptions("Blocktextsimple", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Blockquote", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Blockintro", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Blocktitle", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Stats", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Button", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Download", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Imagegrid", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Imagetext", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("List", DEFAULT_OPTIONS);
        SirTrevor.setBlockOptions("Blockseparator", DEFAULT_OPTIONS);

        var IMAGE_OPTIONS = $.extend({}, DEFAULT_OPTIONS);
        IMAGE_OPTIONS.option_crop_ratio = "2.4";
        SirTrevor.setBlockOptions("Imagesimple", IMAGE_OPTIONS);
        SirTrevor.setBlockOptions("Imagefull", IMAGE_OPTIONS);
        SirTrevor.setBlockOptions("Diaporama", IMAGE_OPTIONS);

        var COLLECTION_OPTIONS = $.extend({}, DEFAULT_OPTIONS);
        COLLECTION_OPTIONS.option_browser = "{{ route('admin.catalog.collections.browser') }}";
        SirTrevor.setBlockOptions("Collection", COLLECTION_OPTIONS);

      }

      @if(isset($blockList))
        var blockTypes = {!! json_encode($blockList) !!}
      @else
        var blockTypes = [
            "Blockintro",
            "Blocktext",
            "Blocktextsimple",
            "Blockquote",
            // "Blocktitle",
            "Imagesimple",
            "Imagefull",
            "Imagegrid",
            "Imagetext",
            "Diaporama",
            "Collection",
            "List",
            "Stats",
            "Blockseparator",
            "Button",
            "Download"
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
