@if(isset($currentUser))
    <a17-dropdown ref="userDropdown" position="bottom-right" :offset="-10">
        <a href="{{ route('admin.users.edit', $currentUser->id) }}" @click.prevent="$refs.userDropdown.toggle()">{{ $currentUser->name }} <span symbol="dropdown_module" class="icon icon--dropdown_module"><svg><title>dropdown_module</title><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#dropdown_module"></use></svg></span></a>
        <div slot="dropdown__content">
            @can('manage-users')
                <a href="{{ route('admin.users.index') }}">CMS Users</a>
            @endcan
            <a href="{{ route('admin.users.edit', $currentUser->id) }}">Settings</a>
            <a href="{{ route('admin.logout') }}">Logout</a>
        </div>
    </a17-dropdown>
@endif
