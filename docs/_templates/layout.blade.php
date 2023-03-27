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
  <title>{{ $title }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">
  <link href="/style.css" rel="stylesheet">
  <script src="//unpkg.com/alpinejs" defer></script>
</head>

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
    @elseif ($currentSegment === 'blogs' && strpos($url, 'index.html'))
      {{-- blog index --}}
      <div class="content markdown mt-68">
        <h1>{{$title}}</h1>
        {!! $content !!}
        <x-twilldocs::grid-auto-generated :tree="$tree" :currentSegment="$currentSegment" :url="$url" />
      </div>
    @elseif ($currentSegment === 'documentation' || $currentSegment === 'guides')
      <div class="flex flex-row flex-nowrap justify-between">
        <x-twilldocs::sidebar :tree="$tree" :currentSegment="$currentSegment" :url="$url"/>
        <div class="content markdown w-full lg:w-9-cols xl:w-6-cols lg:max-w-740 xl:mx-auto mt-68">
            {{-- documentation and guide details --}}
            @if (isset($tree[$currentSegment]))
              <div class="print:!hidden" x-transition x-bind:class="{ hidden: !open }"></div>
            @endif

            <h1>{{$title}}</h1>
            @if ($toc)
              <div class="chapters-nav xl:hidden">
                {!! $toc !!}
              </div>
            @endif
            {!! $content !!}

            <x-twilldocs::contentFooter :currentSegment="$currentSegment" :url="$url" :githubLink="$githubLink" :tree="$tree" />
        </div>

        <div class="chapters-nav-fixed hidden xl:block xl:w-240 top-80 sticky h-screen-minus-header overflow-auto">
          @if ($toc)
            <h2 id="quick-reference" class="sr-only">Quick chapter reference</h2>
            {!! $toc !!}
          @endif
        </div>
      @elseif (strpos($url, 'index.html'))
        {{-- home --}}
        <div class="content markdown mt-68">
          <h1>{{$title}}</h1>
          {!! $content !!}
        </div>
      @elseif ($currentSegment === 'blogs')
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
</div>

<x-twilldocs::devTools />

<script src="/js/nav.js"></script>

</body>

</html>
