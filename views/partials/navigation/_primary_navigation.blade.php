<nav class="header__nav">
    @foreach ($linkGroups as $group => $links)
        <ul class="header__items">
            @foreach ($links as $nav_item)
                {!! $nav_item->render(class: 'header__item') !!}
            @endforeach
        </ul>
    @endforeach
</nav>
