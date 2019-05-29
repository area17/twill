@php
    $user_management_route = 'admin.users.index';
    if ($currentUser->can('edit-users')) {
        $user_management_route = 'admin.users.index';
    } elseif ($currentUser->can('edit-user-role')) {
        $user_management_route = 'admin.roles.index';
    } elseif ($currentUser->can('edit-user-groups')) {
        $user_management_route = 'admin.groups.index';
    }
@endphp

@if(isset($currentUser))
    <a17-dropdown ref="userDropdown" position="bottom-right" :offset="-10">
        <a href="{{ route('admin.users.edit', $currentUser->id) }}" @click.prevent="$refs.userDropdown.toggle()">{{ $currentUser->name }} <span symbol="dropdown_module" class="icon icon--dropdown_module"><svg><title>dropdown_module</title><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#dropdown_module"></use></svg></span></a>
        <div slot="dropdown__content">
            @if ($currentUser->can('access-user-management'))
                <a href="{{ route($user_management_route) }}">CMS Users</a>
            @endif
            <a href="{{ route('admin.users.edit', $currentUser->id) }}">Profile</a>
            <a href="{{ route('admin.logout') }}">Logout</a>
        </div>
    </a17-dropdown>
@endif
