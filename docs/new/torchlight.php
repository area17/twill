<?php

return [
    // Which theme you want to use. You can find all of the themes at
    // https://torchlight.dev/themes, or you can provide your own.
    'theme' => 'nord',

    // Your API token from torchlight.dev. You can set it as an ENV variable
    // (shown below), or just hardcode it if your repo is private.
    'token' => getenv('TORCHLIGHT_API_TOKEN'),

    // If you want to register the blade directives, set this to true.
    'blade_components' => true,

    // The Host of the API.
    'host' => 'https://api.torchlight.dev',

    // If you want to specify the cache path, you can do so here. Note
    // that you should *not* use the same path that Jigsaw uses,
    // which is `cache` at the root level of your app.
    'cache_path' => 'torchlight_cache',

    // Because of the way Jigsaw works as a static site generator, all the
    // code blocks for your entire site will be sent as one request. We
    // increase the timeout to 15 seconds to cover for that.
    'request_timeout' => 15,

    // Global options to control blocks-level settings.
    // https://torchlight.dev/docs/options
    'options' => [
        // Turn line numbers on or off globally.
        'lineNumbers' => true,

        // Control the `style` attribute applied to line numbers.
        // 'lineNumbersStyle' => '',

        // Turn on +/- diff indicators.
        'diffIndicators' => true,

        // If there are any diff indicators for a line, put them
        // in place of the line number to save horizontal space.
        'diffIndicatorsInPlaceOfLineNumbers' => true,

        // When lines are collapsed, this is the text that will
        // be shown to indicate that they can be expanded.
        // 'summaryCollapsedIndicator' => '...',
    ]
];
