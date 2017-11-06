@if(isset($currentUser))
    <div class="header__user">
        <a href="{{ route('admin.users.edit', $currentUser->id) }}">{{ $currentUser->name }}</a>
    </div>
@endif
