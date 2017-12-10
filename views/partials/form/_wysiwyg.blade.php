@php
    $toolbarOptions = $toolbarOptions ?? false;
    if ($toolbarOptions) {
        $toolbarOptions = array_map(function ($option) {
            if ($option == 'list-unordered') {
                return (object) ['list' => 'bullet'];
            }
            if ($option == 'list-ordered') {
                return (object) ['list' => 'ordered'];
            }

            return $option;
        }, $toolbarOptions);

        $toolbarOptions = [
            'modules' => [
                'toolbar' => $toolbarOptions
            ]
        ];
    }

    $options = $customOptions ?? $toolbarOptions ?? false;
@endphp

@if($translated ?? false)
    <a17-locale
        type="a17-wysiwyg"
        :attributes='{
            label: "{{ $label }}",
            @if ($renderForBlocks) name: fieldName("{{ $name }}"), @else name: "{{ $name }}", @endif
            @if ($maxlength ?? false) maxlength: {{ $maxlength }}, @endif
            @if ($options ?? false) options: {!! json_encode($options) !!}, @endif
            @if ($placeholder ?? false) placeholder: "{{ $placeholder }}", @endif
            @if ($note ?? false) note: "{{ $note }}", @endif
            @if ($disabled ?? false) disabled: true, @endif
            @if ($readonly ?? false) readonly: true, @endif
            inStore: "value"
        }'
    ></a17-locale>
@else
    <a17-wysiwyg
        label="{{ $label }}"
        @if ($renderForBlocks) :name="fieldName('{{ $name }}')" @else name="{{ $name }}" @endif
        @if ($maxlength ?? false) :maxlength='{{ $maxlength }}' @endif
        @if ($toolbarOptions ?? false) :options='{!! json_encode($options) !!}' @endif
        @if ($placeholder ?? false) placeholder='{{ $placeholder }}' @endif
        @if ($note ?? false) note="{{ $note }}" @endif
        @if ($disabled ?? false) disabled @endif
        @if ($readonly ?? false) readonly @endif
        in-store="value"
    ></a17-wysiwyg>
@endif

@push('fieldsStore')
@unless($renderForBlocks || ($renderForModal ?? false))
    @if($translated ?? false && isset($form_fields['translations']) && isset($form_fields['translations'][$name]))
        var field = {
            name: '{{ $name }}',
            value: {
                @foreach(getLocales() as $locale)
                    '{{ $locale }}': "{!! $form_fields['translations'][$name][$locale] ?? '' !!}"@unless($loop->last),@endif
                @endforeach
            }
        }
    @else
        var field = {
            name: '{{ $name }}',
            value: "{!! $item->$name !!}"
        }
    @endif

    window.STORE.form.fields.push(field)
@endpush
@endunless
