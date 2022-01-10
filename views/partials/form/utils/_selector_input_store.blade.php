window['{{ config('twill.js_namespace') }}'].STORE.form.fields.push({
    name: '{{ $name }}',
    value: @if(isset($item) && is_numeric($item->$name)) {{ $item->$name }}
           @elseif(isset($item->$name)) {!! json_encode($item->$name) !!}
           @elseif(isset($formFieldsValue))
                @if (is_array($formFieldsValue))
                    @php
                        $formFieldsValue = Arr::first($formFieldsValue, null, '');
                    @endphp
                @endif
                @if (is_numeric($formFieldsValue)) {{ $formFieldsValue }}
                @elseif(is_string($formFieldsValue)) '{{ $formFieldsValue }}'
                @else {!! $formFieldsValue === null ? "''" : $formFieldsValue !!}
                @endif
           @else
                @if(is_bool($default))
                    {{ $default ? 'true' : 'false'}}
                @else
                    {{ $default ?? '' }}
                @endif
           @endif
})
