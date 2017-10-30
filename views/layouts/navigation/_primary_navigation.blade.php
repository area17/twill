@if (isset($_global_active_navigation) && isset(config('cms-navigation.'.$_global_active_navigation)['primary_navigation']))
    <nav id="primary-navigation">
        <ul>
            @foreach(config('cms-navigation.'.$_global_active_navigation)['primary_navigation'] as $primary_navigation_key => $primary_navigation_element)
                @can($primary_navigation_element['can'] ?? 'list')
                    @if(isset($_primary_active_navigation) && $primary_navigation_key === $_primary_active_navigation || (($primary_navigation_element['raw'] ?? false) && Request::url() == $primary_navigation_element['route']))
                        <li class="on">
                    @else
                        <li>
                    @endif
                    @php
                        $isModule = $primary_navigation_element['module'] ?? false;
                    @endphp
                    @if ($isModule)
                        @php
                            $module = $primary_navigation_key;
                            $action = $primary_navigation_element['route'] ?? 'index';
                            $href = moduleRoute($module, $_global_active_navigation, $action);
                        @endphp
                    @elseif ($primary_navigation_element['raw'] ?? false)
                        @php
                            $href = !empty($primary_navigation_element['route']) ? $primary_navigation_element['route'] : '#';
                        @endphp
                    @else
                        @php
                            $href = !empty($primary_navigation_element['route']) ? route($primary_navigation_element['route'], $primary_navigation_element['params'] ?? []) : '#';
                        @endphp
                    @endif
                    <a href="{{ $href }}">{{ $primary_navigation_element['title'] }}</a>
                    </li>
                @endcan
            @endforeach
        </ul>
    </nav>
@endif
