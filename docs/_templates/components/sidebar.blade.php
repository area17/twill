<nav
    class="sidebar w-3-cols bg-white sticky top-0 h-screen pr-32 border-r-[1px] border-grey overflow-x-hidden overflow-y-auto"
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
                <ul class="core-list mt-30">
                    @foreach ($tree[$currentSegment]['items'] ?? [] as $item)
                        @php $open = \Illuminate\Support\Str::betweenFirst(ltrim($item['url'], '/'), '/', '/') === \Illuminate\Support\Str::betweenFirst(ltrim($url, '/'), '/', '/'); @endphp
                        <li class="relative mt-18" x-data="{ open: {{ $open ? 'true' : 'false' }} }">
                            <div class="flex">
                                <a class="inline pl-3.5 before:pointer-events-none before:absolute before:-left-1 before:top-1/2 before:h-1.5 before:w-1.5 before:-translate-y-1/2 before:rounded-full text-slate-500 before:hidden before:bg-slate-300 hover:text-slate-600 hover:before:block no-underline hover:text-purple @if ($open) text-purple font-bold no-underline @endif"
                                    href="{{ $item['url'] ?? '#' }}">{{ $item['title'] ?? '' }}</a>
                                @if (!empty($item['items'] ?? []))
                                    <div
                                        class="flex-1 cursor-pointer px-5 items-center flex"
                                        x-on:click="open = !open">
                                    </div>
                                @endif
                            </div>
                            @if (!empty($item['items'] ?? []))
                                <ul class="@if ($open) block @else hidden @endif ml-24 mt-18"
                                    x-bind:class="{ 'hidden': !open }"
                                    @foreach ($item['items'] ?? [] as $item) @php $active=$url===$item['url']; @endphp
                                        <li class="relative mt-8">
                                            <a class="block w-full pl-3.5 before:pointer-events-none text-black no-underline hover:text-purple ___inline_directive______________________________________2___" href="{{ $item['url'] ?? '#' }}">{{ $item['title'] ?? '' }}</a>
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
