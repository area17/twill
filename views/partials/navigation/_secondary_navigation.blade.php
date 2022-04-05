@if (!($disable_secondary_navigation ?? false) && isset($_primary_active_navigation) && isset(config('twill-navigation.' . $_global_active_navigation . '.primary_navigation.' . $_primary_active_navigation)['secondary_navigation']))
    <nav class="navUnder">
        <div class="container">
            <ul class="navUnder__list">
                @foreach(config('twill-navigation.'. $_global_active_navigation . '.primary_navigation.' . $_primary_active_navigation)['secondary_navigation'] as $secondary_navigation_key => $secondary_navigation_element)
                    @php
                        $gate = $secondary_navigation_element['can'] ?? 'access-module-list';
                    @endphp
                    @unless(($secondary_navigation_element['module'] ?? false) && Auth::user()->cannot($gate, $secondary_navigation_key))
                        @if(isActiveNavigation($secondary_navigation_element, $secondary_navigation_key, $_secondary_active_navigation))
                            <li class="navUnder__item s--on">
                        @else
                            <li class="navUnder__item">
                        @endif
                                <a href="{{ getNavigationUrl($secondary_navigation_element, $secondary_navigation_key, $_global_active_navigation . '.' . $_primary_active_navigation) }}" @if (isset($secondary_navigation_element['target']) && $secondary_navigation_element['target'] == 'external') target="_blank" @endif>{{ $secondary_navigation_element['title'] }}</a>
                            </li>
                    @endunless
                @endforeach
            </ul>
        </div>
    </nav>
@endif
