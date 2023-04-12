@if(isset($currentUser) && config('twill.enabled.users-management'))
    @php
        $user_management_route = 'twill.users.index';
        if ($currentUser->can('edit-users')) {
            $user_management_route = 'twill.users.index';
        } elseif ($currentUser->can('edit-user-roles')) {
            $user_management_route = 'twill.roles.index';
        } elseif ($currentUser->can('edit-user-groups')) {
            $user_management_route = 'twill.groups.index';
        }
    @endphp

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
            @if ($currentUser->can('access-user-management'))
                <a href="{{ route($user_management_route) }}">{{ twillTrans('twill::lang.nav.cms-users') }}</a>
            @endif
            <a href="{{ route('twill.users.edit', $currentUser->id) }}">{{ twillTrans('twill::lang.nav.profile') }}</a>
            <a href="#" data-logout-btn>{{ twillTrans('twill::lang.nav.logout') }}</a>
        </div>
    </a17-dropdown>
@endif
