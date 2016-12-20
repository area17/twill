@if (config()->has('cms-navigation'))
    <nav id="global-navigation">
        <ul>
            @foreach(config('cms-navigation') as $global_navigation_key => $global_navigation_element)
                @can($global_navigation_element['can'] ?? 'list')
                    @if(isset($_global_active_navigation) && $global_navigation_key === $_global_active_navigation)
                        <li class="on">
                    @else
                        <li>
                    @endif
                    @php
                        $isModule = $global_navigation_element['module'] ?? false;
                    @endphp
                    @if ($isModule)
                        @php
                            $module = $global_navigation_key;
                            $action = $global_navigation_element['route'] ?? 'index';
                            $href = moduleRoute($module, null, $action);
                        @endphp
                    @elseif ($global_navigation_element['page'] ?? false)
                        @php
                            $href = pageRoute($global_navigation_element['key'], $global_navigation_key);
                        @endphp
                    @else
                        @php
                            $href = !empty($global_navigation_element['route']) ? route($global_navigation_element['route'], $global_navigation_element['params'] ?? []) : '#';
                        @endphp
                    @endif
                    <a href="{{ $href }}">{{ $global_navigation_element['title'] }}</a>
                    </li>
                @endcan
            @endforeach
            @can('list')
                @if (config('cms-toolkit.enabled.media-library'))
                    <li>
                        @include('cms-toolkit::medias._open_media_library_link')
                    </li>
                @endif
                @if (config('cms-toolkit.enabled.file-library'))
                    <li>
                        @include('cms-toolkit::files._open_file_library_link')
                    </li>
                @endif
            @endcan
        </ul>
    </nav>
@endif
