<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="robots" content="noindex,nofollow" />

<title>{{ config('app.name') }} {{ config('twill.admin_app_title_suffix') }}</title>

<!-- Fonts -->
@if(app()->isProduction())
    <link href="{{ twillAsset('Inter-Regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin>
    <link href="{{ twillAsset('Inter-Medium.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin>
@endif

<!-- CSS -->
@if(app()->isProduction())
    <link href="{{ twillAsset('chunk-common.css') }}" rel="preload" as="style" crossorigin/>
    <link href="{{ twillAsset('chunk-vendors.css') }}" rel="preload" as="style" crossorigin/>
@endif

@unless(config('twill.dev_mode', false))
    <link href="{{ twillAsset('chunk-common.css') }}" rel="stylesheet" crossorigin/>
    <link href="{{ twillAsset('chunk-vendors.css' )}}" rel="stylesheet" crossorigin/>
@endunless

<!-- head.js -->
<script>
    !function(e){var i=window.A17||{},n=e.documentElement,l=window;i.browserSpec="html5",i.touch=!!("ontouchstart"in l||l.documentTouch&&e instanceof DocumentTouch),i.objectFit="objectFit"in n.style,window.A17=i,n.className=n.className.replace(/\bno-js\b/," js "+i.browserSpec+(i.touch?" touch":" no-touch")+(i.objectFit?" objectFit":" no-objectFit"))}(document);
</script>

@stack('extra_css')
@stack('extra_js_head')
