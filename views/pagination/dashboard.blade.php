@if ($paginator->hasPages())
    <nav>
        <ul class="pagination" style="display: flex; margin-bottom: 8px; justify-content: center;s">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li style="flex: 1; text-align: end;" class="disabled" aria-disabled="true">
                    <span rel="prev" aria-label="@lang('pagination.previous')"><svg style="height: 1em; opacity: .1;" class="MuiSvgIcon-root MuiSvgIcon-fontSizeSmall css-f5io2" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ArrowBackIosNewIcon" aria-label="fontSize small"><path d="M17.77 3.77 16 2 6 12l10 10 1.77-1.77L9.54 12z"></path></svg></span>
                </li>
            @else
                <li style="flex: 1; text-align: end;">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')"><svg style="height: 1em" class="MuiSvgIcon-root MuiSvgIcon-fontSizeSmall css-f5io2" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ArrowBackIosNewIcon" aria-label="fontSize small"><path d="M17.77 3.77 16 2 6 12l10 10 1.77-1.77L9.54 12z"></path></svg></a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled" aria-disabled="true"><span>{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active" aria-current="page"><span>{{ $page }}</span></li>
                        @else
                            <li class="f--link"><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li style="flex: 1; text-align: start;">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')"><svg style="height: 1em;" class="MuiSvgIcon-root MuiSvgIcon-fontSizeSmall  css-f5io2" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ArrowForwardIosIcon" aria-label="fontSize small"><path d="M6.23 20.23 8 22l10-10L8 2 6.23 3.77 14.46 12z"></path></svg></a>
                </li>
            @else
                <li style="flex: 1; text-align: start;" class="disabled">
                    <span rel="next" aria-label="@lang('pagination.next')"><svg style="height: 1em; opacity: .1;" class="MuiSvgIcon-root MuiSvgIcon-fontSizeSmall  css-f5io2" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="ArrowForwardIosIcon" aria-label="fontSize small"><path d="M6.23 20.23 8 22l10-10L8 2 6.23 3.77 14.46 12z"></path></svg></span>
                </li>
            @endif
        </ul>
    </nav>
    <div>
        <p class="f--note">
            {!! __('Showing') !!}
            @if ($paginator->firstItem())
                <span class="">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            {!! __('of') !!}
            <span class="">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </p>
    </div>
@endif
