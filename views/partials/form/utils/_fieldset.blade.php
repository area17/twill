<a17-fieldset title="{{ $title }}" @isset($id) id="{{ $id }}" @endisset @isset($open) :open="{{$open ? 'true' : 'false'}}" @endif>
    {{ $slot }}
</a17-fieldset>
