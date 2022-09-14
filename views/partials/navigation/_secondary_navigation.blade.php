@if (!empty($links))
    <nav class="nav">
        <div class="container">
            <ul class="nav__list">
                @foreach ($links as $navItem)
                    {!! $navItem->render(class: 'nav__item') !!}
                @endforeach
            </ul>
        </div>
    </nav>
@endif
