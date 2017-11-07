@if (config()->has('cms-navigation'))
    @if(isset($mobile) && $mobile)
        <header class="headerMobile" data-header-mobile>
            <nav class="headerMobile__nav">
                <div class="container">
                    <ul class="headerMobile__list">
                        @foreach(config('cms-navigation') as $global_navigation_key => $global_navigation_element)
                            @can($global_navigation_element['can'] ?? 'list')
                                @if(isActiveNavigation($global_navigation_element, $global_navigation_key, $_global_active_navigation))
                                    <li class="headerMobile__item s--on">
                                @else
                                    <li class="headerMobile__item">
                                @endif
                                        <a href="{{ getNavigationUrl($global_navigation_element, $global_navigation_key) }}">{{ $global_navigation_element['title'] }}</a>
                                    </li>
                            @endcan
                        @endforeach
                    </ul>
                </div>
            </nav>
        </header>
    @else
        <nav class="header__nav">
            <ul class="header__items">
                @foreach(config('cms-navigation') as $global_navigation_key => $global_navigation_element)
                    @can($global_navigation_element['can'] ?? 'list')
                        @if(isActiveNavigation($global_navigation_element, $global_navigation_key, $_global_active_navigation))
                            <li class="header__item s--on">
                        @else
                            <li class="header__item">
                        @endif
                                <a href="{{ getNavigationUrl($global_navigation_element, $global_navigation_key) }}">{{ $global_navigation_element['title'] }}</a>
                            </li>
                    @endcan
                @endforeach
            </ul>
            <ul class="header__items">
                @can('list')
                    @if (config('cms-toolkit.enabled.media-library'))
                        <li class="header__item"><a href="#">Media Library</a></li>
                    @endif
                @endcan
                @if (config('cms-toolkit.enabled.site-link'))
                    <li class="header__item"><a href="{{ route(config('cms-toolkit.frontend.home_route_name')) }}" target="_blank">Open live site &#8599;</a></li>
                @endif
            </ul>
        </nav>
    @endif
@endif
