@if (session()->has('flash_notification.message'))
    <div class="message message-{{ session('flash_notification.level') }}">
        <p>
            {{ session('flash_notification.message') }}
        </p>
        @if (session('flash_notification.close'))
            <a href="#" class="close" data-behavior="close_message">Close</a>
        @endif
    </div>
@elseif (isset($errors) && count($errors) > 0)
    <div class="message message-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <a href="#" class="close" data-behavior="close_message">Close</a>
    </div>
@elseif (session('status'))
    <div class="message message-notice">
        <p>{{ session('status') }}</p>
    </div>
@endif
