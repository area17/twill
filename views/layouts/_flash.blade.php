@if (session()->has('flash_notification.message'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $.event.trigger({ type: "notification_open", message: "{{ session('flash_notification.message') }}", style: "{{ session('flash_notification.level') }}" });
        });
    </script>
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
