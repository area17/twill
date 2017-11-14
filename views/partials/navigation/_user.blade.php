@if(isset($currentUser))
    <a href="{{ route('admin.users.edit', $currentUser->id) }}">{{ $currentUser->name }}</a>
@endif
