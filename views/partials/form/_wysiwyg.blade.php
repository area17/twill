@php
    $translated = $translated ?? false;
    $maxlength = $maxlength ?? false;
    $options = $options ?? false;
    $placeholder = $placeholder ?? false;
    $note = $note ?? false;
    $disabled = $disabled ?? false;
    $readonly = $readonly ?? false;
    $editSource = $editSource ?? false;
    $toolbarOptions = $toolbarOptions ?? false;
    $inModal = $fieldsInModal ?? false;

    // quill.js options
    $activeSyntax = $syntax ?? false;
    $theme = $customTheme ?? 'github';
    if ($toolbarOptions) {
        $toolbarOptions = array_map(function ($option) {
            if ($option == 'list-unordered') {
                return (object) ['list' => 'bullet'];
            }
            if ($option == 'list-ordered') {
                return (object) ['list' => 'ordered'];
            }
            if ($option == 'h1') {
                return (object) ['header' => 1];
            }
            if ($option == 'h2') {
                return (object) ['header' => 2];
            }
            return $option;
        }, $toolbarOptions);

        $toolbarOptions = [
            'modules' => [
                'toolbar' => $toolbarOptions,
                'syntax' => $activeSyntax
            ]
        ];
    }
    $options = $customOptions ?? $toolbarOptions ?? false;
@endphp

@if($activeSyntax)
    @pushonce('extra_css:wysiwyg')
        <link rel="stylesheet" href="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.12.0/build/styles/{{$theme}}.min.css">
    @endpushonce
@endif

@if($translated)
    <a17-locale
        type="a17-wysiwyg"
        :attributes="{
            label: '{{ $label }}',
            @include('twill::partials.form.utils._field_name', ['asAttributes' => true])
            @if ($note) note: '{{ $note }}', @endif
            @if ($options) options: {!! e(json_encode($options)) !!}, @endif
            @if ($placeholder) placeholder: '{{ $placeholder }}', @endif
            @if ($maxlength) maxlength: {{ $maxlength }}, @endif
            @if ($disabled) disabled: true, @endif
            @if ($readonly) readonly: true, @endif
            @if ($editSource) editSource: true, @endif
            @if ($inModal) inModal: true, @endif
            inStore: 'value'
        }"
    ></a17-locale>
@else
    <a17-wysiwyg
        label="{{ $label }}"
        @include('twill::partials.form.utils._field_name')
        @if ($note) note="{{ $note }}" @endif
        @if ($options) :options='{!! json_encode($options) !!}' @endif
        @if ($placeholder) placeholder='{{ $placeholder }}' @endif
        @if ($maxlength) :maxlength='{{ $maxlength }}' @endif
        @if ($disabled) disabled @endif
        @if ($readonly) readonly @endif
        @if ($editSource) :edit-source='true' @endif
        @if ($inModal) :in-modal="true" @endif
        in-store="value"
    ></a17-wysiwyg>
@endif

@unless($renderForBlocks || $renderForModal)
@push('vuexStore')
    @if($translated && isset($form_fields['translations']) && isset($form_fields['translations'][$name]))
        window.STORE.form.fields.push({
            name: '{{ $name }}',
            value: {
                @foreach(getLocales() as $locale)
                    '{{ $locale }}': {!! json_encode(
                        $form_fields['translations'][$name][$locale] ?? ''
                    ) !!}@unless($loop->last),@endif
                @endforeach
            }
        })
    @elseif(isset($item->$name) || null !== $formFieldsValue = getFormFieldsValue($form_fields, $name))
        window.STORE.form.fields.push({
            name: '{{ $name }}',
            value: {!! json_encode(isset($item->$name) ? $item->$name : $formFieldsValue) !!}
        })
    @endif

@endpush
@endunless
