<nav class="p-4">
    <ul class="pl-4">
        @foreach($links as $link)
            <li>
                <a href="{{route('frontend.page', [$link->getRelated('page')->first()->slug])}}">
                    {{$link->title}}
                </a>

                @if ($link->children->isNotEmpty())
                    <ul class="pl-4">
                        @foreach($link->children as $link)
                            <li>
                                <a href="{{route('frontend.page', [$link->getRelated('page')->first()->slug])}}">
                                    {{$link->title}}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</nav>
