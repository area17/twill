<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title }}</title>
  <link href="/style.css" rel="stylesheet">
  <script src="//unpkg.com/alpinejs" defer></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body
  class="overflow-x-hidden page--{{ Str::slug(Str::replace(['/', '.html'], ['-', ''], $url)) }}"
  x-data="{ isMobile: (window.innerWidth < 1024) ? true : false, openNav: false }"
  x-bind:class="{ isMobile: isMobile ? true : false, openNav: openNav ? true : false }"
  x-on:resize.window="isMobile = (window.innerWidth < 1024) ? true : false"
  x-on:keyup.escape="if(isMobile && openNav){
        openNav = false;
        $nextTick(() => $refs.openMenu.focus())
    }"
>
@php $currentSegment = \Illuminate\Support\Str::before(ltrim($url, '/'), '/'); @endphp
<div class="min-h-screen-minus-header">
  <x-twilldocs::header/>

  <div class="container">
    <div class="flex flex-row flex-nowrap justify-between">
      <x-twilldocs::sidebar :tree="$tree" :currentSegment="$currentSegment" :url="$url"/>
      <div class="content w-full lg:w-9-cols xl:w-6-cols lg:max-w-740 xl:mx-auto mt-68 markdown">
        {{-- <div class="markdown lg:w-6-cols xl:w-full mx-auto"> --}}
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
        {{-- </div> --}}
      </div>

      @if ($toc)
        <div class="chapters-nav-fixed hidden xl:block xl:w-240 top-[80px] sticky h-screen-minus-header overflow-auto">
            <h2 id="quick-reference" class="sr-only">Quick chapter reference</h2>
            {!! $toc !!}
        </div>
      @endif
    </div>
  </div>
</div>

{{-- <x-twilldocs::devTools /> --}}

<script src="/js/nav.js"></script>

</body>

</html>
