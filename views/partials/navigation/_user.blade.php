@if(isset($currentUser))
    <a17-dropdown ref="userDropdown" position="bottom-right">
        <a href="{{ route('admin.users.edit', $currentUser->id) }}" @click.prevent="$refs.userDropdown.toggle()">{{ $currentUser->name }}</a>
        <div slot="dropdown__content">
            <a href="{{ route('admin.users.edit', $currentUser->id) }}">Settings</a>
            <a href="/logout">Logout</a>
        </div>
    </a17-dropdown>
@endif
