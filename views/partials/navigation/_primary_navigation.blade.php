@if ((isset($_global_active_navigation) && isset(config('twill-navigation.'.$_global_active_navigation)['primary_navigation'])) || isset($single_primary_nav))

    @if (isset($single_primary_nav))
        @php
        $primaryNavElements = $single_primary_nav;
        $_global_active_navigation = null;
        $_primary_active_navigation = Arr::first(array_keys($single_primary_nav));
        @endphp
    @else
        @php
        $primaryNavElements = config('twill-navigation.'.$_global_active_navigation)['primary_navigation'];
        @endphp
    @endif

    <nav class="nav">
        <div class="container">
            <ul class="nav__list">
                @foreach($primaryNavElements as $primary_navigation_key => $primary_navigation_element)
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
