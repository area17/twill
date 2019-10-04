@if(isset($currentUser))
    <a17-dropdown ref="userDropdown" position="bottom-right" :offset="-10">
        <a href="{{ route('admin.users.edit', $currentUser->id) }}" @click.prevent="$refs.userDropdown.toggle()">{{ $currentUser->name }} <span symbol="dropdown_module" class="icon icon--dropdown_module"><svg><title>dropdown_module</title><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon--dropdown_module"></use></svg></span></a>
        <div slot="dropdown__content">
            @can('manage-users')
                <a href="{{ route('admin.users.index') }}">@lang('twill::nav.cms-users')</a>
            @endcan
            <a href="{{ route('admin.users.edit', $currentUser->id) }}">@lang('twill::nav.settings')</a>
            <a href="{{ route('admin.logout') }}">@lang('twill::nav.logout')</a>
        </div>
    </a17-dropdown>
@endif
