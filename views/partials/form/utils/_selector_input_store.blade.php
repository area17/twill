window.STORE.form.fields.push({
    name: '{{ $name }}',
    value: @if(isset($item) && is_numeric($item->$name)) {{ $item->$name }}
           @elseif(isset($item->$name)) {!! json_encode($item->$name) !!}
           @elseif(isset($formFieldsValue) && is_numeric($formFieldsValue)) {{ $formFieldsValue }}
           @else {!! $formFieldsValue !!}
           @endif
})
