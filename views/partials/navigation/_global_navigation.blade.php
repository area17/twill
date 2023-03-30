@if (config()->has('twill-navigation'))
    <nav class="header__nav">
        @if(!empty(config('twill-navigation')))
            <ul class="header__items">
                @foreach(config('twill-navigation') as $global_navigation_key => $global_navigation_element)
                    @can($global_navigation_element['can'] ?? 'list')
                        @if(isActiveNavigation($global_navigation_element, $global_navigation_key, $_global_active_navigation))
                            <li class="header__item s--on">
                        @else
                            <li class="header__item">
                        @endif
                                <a href="{{ getNavigationUrl($global_navigation_element, $global_navigation_key) }}" @if (isset($global_navigation_element['target']) && $global_navigation_element['target'] == 'external') target="_blank" @endif>{{ $global_navigation_element['title'] }}</a>
                            </li>
                    @endcan
                @endforeach
            </ul>
        @endif
        @if (config('twill.enabled.media-library') || config('twill.enabled.file-library') || config('twill.enabled.site-link'))
            <ul class="header__items">
                @can('list')
                    @if (config('twill.enabled.media-library') || config('twill.enabled.file-library'))
                        <li class="header__item"><a href="#" data-medialib-btn>{{ twillTrans('twill::lang.nav.media-library') }}</a></li>
                    @endif
                @endcan
                @if (config('twill.enabled.site-link'))
                    <li class="header__item"><a href="{{ config('app.url') }}" target="_blank">{{ twillTrans('twill::lang.nav.open-live-site') }} &#8599;</a></li>
                @endif
            </ul>
        @endif
    </nav>
@endif
