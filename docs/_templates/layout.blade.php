@php
  $feConfig = file_get_contents("./../frontend.config.json");
  $feConfig = json_decode($feConfig, true);
@endphp
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $seoTitle ?? ($title . ' — Twill CMS') }}</title>
  <link rel="mask-icon" href="/dist/images/favicons/safari-pinned-tab.svg?v=3" color="#000000">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="theme-color" content="#000000">
  <meta property="og:image" content="https://twillcms.com/dist/images/social_share.png" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />

  <meta name="twitter:image" content="https://twillcms.com/dist/images/social_share.png">
  <meta itemprop="image" content="https://twillcms.com/dist/images/social_share.png">

  <!-- Facebook / Open Graph globals -->
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="Twill" />
  <meta property="og:author" content="https://www.facebook.com/twillcms/" />

  <!-- Twitter globals -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:site" content="@twillcms" />
  <meta name="twitter:domain" content="twillcms.com" />
  <meta name="twitter:creator" content="@twillcms" />

  <!-- Main Favicon -->
  <link rel="shortcut icon" href="/dist/images/favicons/favicon.ico">
  <!-- Apple Touch Icons (ipad/iphone standard+retina) -->
  <link rel="apple-touch-icon" href="/dist/images/favicons/favicon-192.png">
  <!-- General use iOS/Android icon, auto-downscaled by devices. -->
  <link rel="apple-touch-icon" type="image/png" href="/dist/images/favicons/favicon-192.png" sizes="192x192">
  <!-- iPhone retina touch icon -->
  <link rel="apple-touch-icon" type="image/png" href="/dist/images/favicons/favicon-180.png" sizes="180x180">
  <!-- iPad home screen icons -->
  <!-- Favicon Fallbacks for old browsers that don't read .ico -->
  <link rel="icon" type="image/png" href="/dist/images/favicons/favicon-32.png" sizes="32x32">
  <link rel="icon" type="image/png" href="/dist/images/favicons/favicon-16.png" sizes="16x16">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://89hnjpxalf-dsn.algolia.net" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3" />
  <link href="/style.css" rel="stylesheet">
  <script src="//unpkg.com/alpinejs" defer></script>
</head>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-EE0Y26M81B"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-EE0Y26M81B');
</script>

<body
  class="overflow-x-hidden page--{{ Str::slug(Str::replace(['/', '.html'], ['-', ''], $url)) }}"
  x-data="{ isMobile: (window.innerWidth < {{ intval($feConfig['structure']['breakpoints']['lg']) }}) ? true : false, openNav: false }"
  x-bind:class="{ isMobile: isMobile ? true : false, openNav: openNav ? true : false }"
  x-on:resize.window="isMobile = (window.innerWidth < {{ intval($feConfig['structure']['breakpoints']['lg']) }}) ? true : false"
  x-on:keyup.escape="if(isMobile && openNav){
        openNav = false;
        $nextTick(() => $refs.openMenu.focus())
    }"
>
@php $currentSegment = \Illuminate\Support\Str::before(ltrim($url, '/'), '/'); @endphp
<div class="min-h-screen-minus-header">
  <x-twilldocs::header/>
  <div class="container">
    @if ($currentSegment === 'guides' && strpos($url, 'index.html') && !strpos($url, 'guides/page-builder-with-blade'))
      {{-- guides index --}}
      <div class="content markdown mt-68">
        <h1>{{$title}}</h1>
        {!! $content !!}
        <x-twilldocs::grid-auto-generated :tree="$tree" :currentSegment="$currentSegment" :url="$url" />
      </div>
    @elseif ($currentSegment === 'blog' && strpos($url, 'index.html'))
      {{-- blog index --}}
      <div class="content markdown mt-68">
        <h1>{{$title}}</h1>
        {!! $content !!}
        <x-twilldocs::grid-auto-generated :tree="$tree" :currentSegment="$currentSegment" :url="$url" />
      </div>
    @elseif ($currentSegment === 'docs' || $currentSegment === 'guides')
      <div class="flex flex-row flex-nowrap justify-between">
        <x-twilldocs::sidebar :tree="$tree" :currentSegment="$currentSegment" :url="$url"/>
        <div class="content markdown w-full lg:w-9-cols xl:w-6-cols lg:max-w-740 xl:mx-auto">
          {{-- documentation and guide details --}}
          @if (isset($tree[$currentSegment]))
            <div class="print:!hidden" x-transition x-bind:class="{ hidden: !open }"></div>
          @endif

          <h1>{{$title}}</h1>
          @if ($toc)
            <div class="chapters-nav border border-primary p-24 xl:hidden">
              <h2  id="quick-reference"  class="!f-doc-title !mt-0">Content</h2>
              {!! $toc !!}
            </div>
          @endif
          {!! $content !!}

          <x-twilldocs::contentFooter :currentSegment="$currentSegment" :url="$url" :githubLink="$githubLink" :tree="$tree" />
        </div>

        <div class="chapters-nav-fixed hidden xl:block xl:w-240 top-80 sticky h-screen-minus-header overflow-auto">
          @if ($toc)
            <div class="border border-primary p-24">
              <h2  id="quick-reference" class="f-doc-title">Content</h2>
              {!! $toc !!}
            </div>
          @endif
        </div>
      </div>
    @elseif (strpos($url, 'index.html'))
      {{-- home --}}
      <div class="content markdown mt-68">
        <h1>{{$title}}</h1>
        {!! $content !!}
      </div>
    @elseif ($currentSegment === 'blog')
      {{-- blog details --}}
      <div class="flex flex-row flex-nowrap">
        <div class="content markdown w-full lg:w-9-cols xl:w-6-cols lg:max-w-740 lg:mx-auto mt-68">
          <h1>{{$title}}</h1>
          {!! $content !!}
        </div>
      </div>
    @else
      {{-- capture for any other page type --}}
      <div class="content markdown mt-68">
        <h1>{{$title}}</h1>
        {!! $content !!}
      </div>
    @endif
  </div>
</div>

{{--<x-twilldocs::devTools />--}}

<script src="https://cdn.jsdelivr.net/npm/@docsearch/js@3"></script>
<script src="/js/nav.js"></script>
<script src="/js/search.js"></script>
</body>

</html>
