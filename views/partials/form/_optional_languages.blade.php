@php
    if (isset($field_wrapper)) {
        $activeField = $field_wrapper . '[active_en]';
    } else {
        $activeField = 'active.en';
    }
@endphp

{!! Form::hidden($activeField, 1) !!}
