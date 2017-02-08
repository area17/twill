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
    option_crop_ratio: '0',
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
    SirTrevor.setBlockOptions("Button", DEFAULT_OPTIONS);
    SirTrevor.setBlockOptions("Blockseparator", DEFAULT_OPTIONS);
  }

  @if(isset($blockList))
    var blockTypes = {!! json_encode($blockList) !!}
  @else
    var blockTypes = [
        "Blocktitle",
        "Blocktext",
        "Blockquote",
        "Image",
        "Imagegrid",
        "Imagetext",
        "Diaporama",
        "Button",
        "Blockseparator"
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
