@if (!empty($links))
    <nav class="navUnder">
        <div class="container">
            <ul class="navUnder__list">
                @foreach ($links as $link)
                    {!! $link->render(class: 'navUnder__item') !!}
                @endforeach
            </ul>
        </div>
    </nav>
@endif
