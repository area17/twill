<header class="headerMobile" data-header-mobile>
    <nav class="headerMobile__nav">
        <div class="container">
            <x-twill.partials::navigation.title />
            <div class="headerMobile__list">
                @foreach ($linkGroups['left'] as $link)
                    {!! $link->render('mobile') !!} <br />
                @endforeach
            </div>
            <div class="headerMobile__list">
                @foreach ($linkGroups['right'] as $link)
                    {!! $link->render('mobile') !!}<br />
                @endforeach
                @if (($currentUser = auth()->user()) && config('twill.enabled.users-management'))
                    <a
                        href="{{ route('twill.users.index') }}">{{ twillTrans('twill::lang.nav.cms-users') }}</a><br />
                    <a
                        href="{{ route('twill.users.edit', $currentUser->id) }}">{{ twillTrans('twill::lang.nav.profile') }}</a><br />
                    <a href="#" data-logout-btn>{{ twillTrans('twill::lang.nav.logout') }}</a>
                @endif
            </div>
        </div>
    </nav>
</header>

<button class="ham @if (isset($search) && $search) ham--search @endif" data-ham-btn>
    @if ($active_title)
        <span class="ham__label">{{ $active_title }}</span>
    @endif
    <span class="btn ham__btn">
        <span class="ham__icon">
            <span class="ham__line"></span>
        </span>
        <span class="icon icon--close_modal"><svg>
                <title>{{ twillTrans('twill::lang.nav.close-menu') }}</title>
                <use xlink:href="#icon--close_modal"></use>
            </svg></span>
    </span>
</button>
