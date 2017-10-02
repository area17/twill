@if (!($disable_secondary_navigation ?? false) && isset($_primary_active_navigation) && isset(config('cms-navigation.' . $_global_active_navigation . '.primary_navigation.' . $_primary_active_navigation)['secondary_navigation']))
    <nav id="secondary-navigation">
        <ul>
            @foreach(config('cms-navigation.'. $_global_active_navigation . '.primary_navigation.' . $_primary_active_navigation)['secondary_navigation'] as $secondary_navigation_key => $secondary_navigation_element)
                @can($secondary_navigation_element['can'] ?? 'list')
                    @if((isset($_secondary_active_navigation) && $secondary_navigation_key === $_secondary_active_navigation) || (isset($secondary_navigation_element['params']) && request()->input(current(array_keys($secondary_navigation_element['params']))) == array_first($secondary_navigation_element['params'])) || (($secondary_navigation_element['raw'] ?? false) && Request::url() == $secondary_navigation_element['route']))
                        <li class="on">
                    @else
                        <li>
                    @endif
                    @php
                        $isModule = $secondary_navigation_element['module'] ?? false;
                    @endphp
                    @if ($isModule)
                        @php
                            $module = $secondary_navigation_key;
                            $action = $secondary_navigation_element['route'] ?? 'index';
                            $href = moduleRoute($module, $_global_active_navigation . '.' . $_primary_active_navigation, $action);
                        @endphp
                    @elseif ($secondary_navigation_element['page'] ?? false)
                        @php
                            $href = pageRoute($secondary_navigation_key, $_global_active_navigation . '.' . $_primary_active_navigation);
                        @endphp
                    @elseif ($primary_navigation_element['raw'] ?? false)
                        @php
                            $href = !empty($primary_navigation_element['route']) ? $primary_navigation_element['route'] : '#';
                        @endphp
                    @else
                        @php
                            $href = !empty($secondary_navigation_element['route']) ? route($secondary_navigation_element['route'], $secondary_navigation_element['params'] ?? []) : '#';
                        @endphp
                    @endif
                    <a href="{{ $href }}">{{ $secondary_navigation_element['title'] }}</a>
                    </li>
                @endcan
            @endforeach
        </ul>
    </nav>
@endif
