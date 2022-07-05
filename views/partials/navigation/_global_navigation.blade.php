@if ($nav_items !== [])
    <nav class="header__nav">
        <ul class="header__items">
            @foreach($nav_items as $nav_item)
                <li class=" header__item @if($nav_item['is_active']) s--on @endif">
                    <a href="{{$nav_item['href']}}"
                       @if ($nav_item['target_blank']) target="_blank" @endif>
                        {{ $nav_item['title'] }}
                    </a>
                </li>
            @endforeach
        </ul>
        @if (config('twill.enabled.media-library') || config('twill.enabled.file-library') || config('twill.enabled.site-link'))
            <ul class="header__items">
                @can('access-media-library')
                    @if (config('twill.enabled.media-library') || config('twill.enabled.file-library'))
                        <li class="header__item"><a href="#"
                                                    data-medialib-btn>{{ twillTrans('twill::lang.nav.media-library') }}</a>
                        </li>
                    @endif
                @endcan
                @if (config('twill.enabled.site-link'))
                    <li class="header__item"><a href="{{ config('app.url') }}"
                                                target="_blank">{{ twillTrans('twill::lang.nav.open-live-site') }} &#8599;</a>
                    </li>
                @endif
            </ul>
        @endif
    </nav>
@endif
