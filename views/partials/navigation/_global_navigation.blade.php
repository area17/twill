@if (config()->has('twill-navigation'))
    <nav class="header__nav" id="headerMenu" v-cloak>
        @if(!empty(config('twill-navigation')))
            <ul class="header__items">
                @foreach(config('twill-navigation') as $global_navigation_key => $global_navigation_element)
                    @can($global_navigation_element['can'] ?? 'list')
                        @php
                            $dropdown = false;
                            if (($global_navigation_element['dropdown'] ?? false) && isset($global_navigation_element['primary_navigation'])) {
                                $dropdown = $global_navigation_element['dropdown'];
                            }
                        @endphp
                        @if(isActiveNavigation($global_navigation_element, $global_navigation_key, $_global_active_navigation))
                            <li class="header__item s--on">
                        @else
                            <li class="header__item">
                        @endif
                        @if($dropdown)
                            <a17-dropdown ref="navDropdown{{$global_navigation_key}}" position="bottom-left" :offset="-10">
                                @endif
                                <a href="{{ getNavigationUrl($global_navigation_element, $global_navigation_key) }}"
                                   @if (isset($global_navigation_element['target']) && $global_navigation_element['target'] == 'external') target="_blank" @endif
                                    @if($dropdown) @click.prevent="$refs.navDropdown{{$global_navigation_key}}.toggle()" @endif
                                >
                                    {{ $global_navigation_element['title'] }}
                                    @if ($dropdown)
                                        <span symbol="dropdown_module" class="icon icon--dropdown_module">
                                            <svg>
                                                <title>dropdown_module</title>
                                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon--dropdown_module"></use>
                                            </svg>
                                        </span>
                                    @endif
                                </a>
                                @if($dropdown)
                                    <div slot="dropdown__content">
                                        @foreach($global_navigation_element['primary_navigation'] as $primary_navigation_key => $primary_navigation_element)
                                            @can($primary_navigation_element['can'] ?? 'list')
                                                @if(isActiveNavigation($primary_navigation_element, $primary_navigation_key, $_primary_active_navigation))
                                                @else
                                                @endif
                                                    <a href="{{ getNavigationUrl($primary_navigation_element, $primary_navigation_key, $_global_active_navigation) }}" @if (isset($primary_navigation_element['target']) && $primary_navigation_element['target'] == 'external') target="_blank" @endif>{{ $primary_navigation_element['title'] }}</a>
                                            @endcan
                                        @endforeach
                                    </div>
                                @endif
                        @if($dropdown)
                            </a17-dropdown>
                        @endif
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
                    <li class="header__item"><a href="{{ config('app.url') }}" target="_blank">Open live site &#8599;</a></li>
                @endif
            </ul>
        @endif
    </nav>
@endif
