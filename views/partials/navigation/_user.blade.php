@if(isset($currentUser))
    <a17-dropdown ref="userDropdown" position="bottom-right" :offset="-10">
        <a href="{{ route('twill.users.edit', $currentUser->id) }}" @click.prevent="$refs.userDropdown.toggle()">
            {{ $currentUser->role === 'SUPERADMIN' ? twillTrans('twill::lang.nav.admin') : $currentUser->name }}
            <span symbol="dropdown_module" class="icon icon--dropdown_module">
                <svg>
                    <title>dropdown_module</title>
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon--dropdown_module"></use>
                </svg>
            </span>
        </a>
        <div slot="dropdown__content">
            @can('manage-users')
                <a href="{{ route('twill.users.index') }}">{{ twillTrans('twill::lang.nav.cms-users') }}</a>
            @endcan
            <a href="{{ route('twill.users.edit', $currentUser->id) }}">{{ twillTrans('twill::lang.nav.settings') }}</a>
            <a href="#" data-logout-btn>{{ twillTrans('twill::lang.nav.logout') }}</a>
        </div>
    </a17-dropdown>
@endif
