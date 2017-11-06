@if (isset($_global_active_navigation) && isset(config('cms-navigation.'.$_global_active_navigation)['primary_navigation']))
    <nav class="nav">
        <div class="container">
            <ul class="nav__list">
                @foreach(config('cms-navigation.'.$_global_active_navigation)['primary_navigation'] as $primary_navigation_key => $primary_navigation_element)
                    @can($primary_navigation_element['can'] ?? 'list')
                        @if(isActiveNavigation($primary_navigation_element, $primary_navigation_key, $_primary_active_navigation))
                            <li class="nav__item s--on">
                        @else
                            <li class="nav__item">
                        @endif
                                <a href="{{ getNavigationUrl($primary_navigation_element, $primary_navigation_key, $_global_active_navigation) }}">{{ $primary_navigation_element['title'] }}</a>
                            </li>
                    @endcan
                @endforeach
            </ul>
        </div>
    </nav>
@endif
