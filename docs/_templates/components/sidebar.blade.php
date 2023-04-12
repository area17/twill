<nav
    class="sidebar w-3-cols xl:w-240 bg-primary sticky top-80 h-screen-minus-header pr-32 pb-16 border-r border-primary overflow-x-hidden overflow-y-auto"
    x-bind:inert="if (isMobile) {
    if (openNav) {
        return false
    } else {
        return true
    }
    } else { return false }"
    x-bind:aria-hidden="if (isMobile) {
        if (openNav) {
            return false
        } else {
            return true
        }
    } else { return false }">
    <button
        class="nav-close hover:opacity-60 h-18 w-18 absolute right-20"
        x-cloak
        x-show="isMobile"
        x-ref="closeMenu"
        aria-label="close nav"
        x-on:click="openNav = false;  $nextTick(() => $refs.openMenu.focus())">
        <svg class="h-18 w-18" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><defs><style>.close-icon-line{fill:none;stroke-miterlimit:10;stroke-width:1.5px;}</style></defs><title>close_icon</title><line class="close-icon-line" x1="1" y1="1" x2="9" y2="9" stroke="currentColor"/><line class="close-icon-line" x1="9" y1="1" x2="1" y2="9" stroke="currentColor"/>
        </svg>
    </button>
    <ul role="list">
        @foreach ($tree as $key => $item)
            @unless($key === '')
                <li class="md:hidden">
                    <a href="{{ $item['url'] }}" >{{ $item['title'] }}</a>
                </li>
            @endunless
        @endforeach
        <li class="mt-32">
            <h2 class="f-doc-title strong">
                {{ $tree[$currentSegment]['title'] ?? '' }}
            </h2>

            @if (!empty($tree[$currentSegment]['items'] ?? []))
                <ul class="core-list mt-30 f-sidebar subpixel-antialiased">
                    @php
                        $index = 0;
                    @endphp
                    @foreach ($tree[$currentSegment]['items'] ?? [] as $item)
                        @php
                            $open = \Illuminate\Support\Str::betweenFirst(ltrim($item['url'], '/'), '/', '/') === \Illuminate\Support\Str::betweenFirst(ltrim($url, '/'), '/', '/');
                            $index++;
                        @endphp
                        <li
                            class="relative mt-12"
                            x-data="{ open: {{ $open ? 'true' : 'false' }} }"
                            :class="open ? 'is-open' : ''"
                        >

                            <div class="flex items-center">
                                <a class="inline no-underline hover:text-link
                                    @if ($open) text-link font-medium no-underline @endif"
                                   href="{{ $item['url'] ?? '#' }}">
                                    {{ $item['title'] ?? '' }}
                                </a>

                                @if (!empty($item['items'] ?? []))
                                    <button class="accordion-trigger" x-on:click="open = !open" aria-label="expand"></button>
                                @endif
                            </div>
                            @if (!empty($item['items'] ?? []))
                                <ul class="overflow-hidden pl-[12px] {{!$open ? 'max-h-0' : ''}}"
                                    x-ref="container{{$index}}"
                                    x-bind:style="open ? 'max-height: ' + $refs.container{{$index}}.scrollHeight + 'px' : ''"
                                    x-init="$nextTick(() => {
                                        $refs.container{{$index}}.classList.add('duration-700')
                                        if({{$open ? 'true' : 'false'}}){
                                            $refs.container{{$index}}.classList.add('max-h-0')
                                        }
                                     })">
                                    @foreach ($item['items'] ?? [] as $item)
                                        @php $active = $url === $item['url']; @endphp
                                        <li class="relative pt-12">
                                            <a
                                                @class([
                                                    'block w-full pl-3.5 before:pointer-events-none no-underline',
                                                    'text-primary hover:text-link' => !$active,
                                                    'text-link font-medium' => $active,
                                                ])
                                                href="{{ $item['url'] ?? '#' }}">
                                                {{ $item['title'] ?? '' }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    </ul>
</nav>
