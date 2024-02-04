@if (!empty($links) || !empty(\A17\Twill\Facades\TwillNavigation::getTertiaryRequestLinks()))
    <nav class="navUnder">
        <div class="container">
            <ul class="navUnder__list">
                @foreach (\A17\Twill\Facades\TwillNavigation::getTertiaryRequestLinks() as $navItem)
                    @if ($navItem->shouldShow())
                        {!! $navItem->render(class: 'navUnder__item') !!}
                    @endif
                @endforeach
                @foreach ($links as $navItem)
                    {!! $navItem->render(class: 'navUnder__item') !!}
                @endforeach
            </ul>
        </div>
    </nav>
@endif
