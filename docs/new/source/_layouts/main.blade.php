<!DOCTYPE html>
<html lang="{{ $page->language ?? 'en' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="canonical" href="{{ $page->getUrl() }}">
    <meta name="description" content="{{ $page->description }}">
    <title>{{ $page->title }}</title>
    <link rel="stylesheet" href="{{ mix('css/main.css', 'assets/build') }}">
    <script defer src="{{ mix('js/main.js', 'assets/build') }}"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="text-gray-900 font-sans antialiased">
<div class="relative mx-auto flex max-w-8xl justify-center sm:px-2 lg:px-8 xl:px-12">
    <div class="hidden lg:relative lg:block lg:flex-none print:hidden">
        <div class="sticky top-[4.5rem] -ml-0.5 h-[calc(100vh-4.5rem)] overflow-y-auto overflow-x-hidden py-16 pl-0.5">
            <ul class="space-y-9">
                @foreach ($page->navigation as $title => $data)
                    <li>
                        <a class="font-display font-medium text-slate-900" href="{{$data['url'] ?? '#'}}">{{$title}}</a>
                        <ul class="mt-2 space-y-2 border-l-2 border-slate-100 dark:border-slate-800 lg:mt-4 lg:space-y-4 lg:border-slate-200">
                            @foreach ($data['children'] as $title => $data)
                                <li class="relative ml-5">
                                    <a href="/{{$data['url'] ?? '#'}}">{{$title}}</a>
                                    <ul class="mt-2">
                                        @foreach ($data['children'] ?? [] as $title => $url)
                                            <li class="ml-2 mt-1">
                                                <a href="/{{$url ?? '#'}}">{{$title}}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="min-w-0 max-w-2xl flex-auto px-4 py-16 lg:max-w-none lg:pr-0 lg:pl-8 xl:px-16">
        <div class="prose max-w-7xl bg-white">
            @yield('body')
        </div>
    </div>
    <div
        class="hidden xl:sticky xl:top-[4.5rem] xl:-mr-6 xl:block xl:h-[calc(100vh-4.5rem)] xl:flex-none xl:overflow-y-auto xl:py-16 xl:pr-6">

    </div>
</div>
</body>
</html>
