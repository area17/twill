@if($activeSyntax)
    @pushonce('extra_css:wysiwyg')
        <link rel="stylesheet" href="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.12.0/build/styles/{{$theme}}.min.css">
    @endpushonce
@endif

@if($type === 'tiptap')
    @if($translated)
        <a17-locale
            type="a17-wysiwyg-tiptap"
            :attributes="{
            label: '{{ $label }}',
            {!! $formFieldName(true) !!},
            @if ($note) note: '{{ $note }}', @endif
            @if ($required) required: true, @endif
            @if ($options) options: {!! e(json_encode($options)) !!}, @endif
            @if ($placeholder) placeholder: '{{ addslashes($placeholder) }}', @endif
            @if ($direction) direction: '{{ $direction }}', @endif
            @if ($maxlength) maxlength: {{ $maxlength }}, @endif
            @if ($hideCounter) showCounter: false, @endif
            @if ($disabled) disabled: true, @endif
            @if ($readOnly) readonly: true, @endif
            @if ($editSource) editSource: true, @endif
            @if ($inModal) inModal: true, @endif
            @if ($limitHeight) limitHeight: true, @endif
            @if ($endpoints) browserEndpoints: {!! e(json_encode($endpoints)) !!}, @endif
            @if ($default)
                initialValue: '{{ $default }}',
                hasDefaultStore: true,
            @endif
                inStore: 'value'
            }"
        ></a17-locale>
    @else
        <a17-wysiwyg-tiptap
            label="{{ $label }}"
            {!! $formFieldName() !!}
            @if ($note) note="{{ $note }}" @endif
            @if ($required) :required="true" @endif
            @if ($options) :options='{!! json_encode($options) !!}' @endif
            @if ($placeholder) placeholder='{{ $placeholder }}' @endif
            @if ($direction) direction="{{ $direction }}" @endif
            @if ($maxlength) :maxlength='{{ $maxlength }}' @endif
            @if ($hideCounter) :showCounter='false' @endif
            @if ($disabled) disabled @endif
            @if ($readOnly) readonly @endif
            @if ($editSource) :edit-source='true' @endif
            @if ($limitHeight) :limit-height='true' @endif
            @if ($endpoints) :browser-endpoints='{!! json_encode($endpoints) !!}' @endif
            @if ($default)
            :initial-value="'{{ $default }}'"
            :has-default-store="true"
            @endif
            @if ($inModal) :in-modal="true" @endif
            in-store="value"
        ></a17-wysiwyg-tiptap>
    @endif
@else
    @if($translated)
        <a17-locale
            type="a17-wysiwyg"
            :attributes="{
            label: '{{ $label }}',
            {!! $formFieldName(true) !!},
            @if ($note) note: '{{ $note }}', @endif
            @if ($required) required: true, @endif
            @if ($options) options: {!! e(json_encode($options)) !!}, @endif
            @if ($placeholder) placeholder: '{{ addslashes($placeholder) }}', @endif
            @if ($direction) direction: '{{ $direction }}', @endif
            @if ($maxlength) maxlength: {{ $maxlength }}, @endif
            @if ($hideCounter) showCounter: false, @endif
            @if ($disabled) disabled: true, @endif
            @if ($readOnly) readonly: true, @endif
            @if ($editSource) editSource: true, @endif
            @if ($inModal) inModal: true, @endif
            @if ($limitHeight) limitHeight: true, @endif
            @if ($default)
                initialValue: '{{ $default }}',
                hasDefaultStore: true,
            @endif
                inStore: 'value'
            }"
        ></a17-locale>
    @else
        <a17-wysiwyg
            label="{{ $label }}"
            {!! $formFieldName() !!}
            @if ($note) note="{{ $note }}" @endif
            @if ($required) :required="true" @endif
            @if ($options) :options='{!! json_encode($options) !!}' @endif
            @if ($placeholder) placeholder='{{ $placeholder }}' @endif
            @if ($direction) direction="{{ $direction }}" @endif
            @if ($maxlength) :maxlength='{{ $maxlength }}' @endif
            @if ($hideCounter) :showCounter='false' @endif
            @if ($disabled) disabled @endif
            @if ($readOnly) readonly @endif
            @if ($editSource) :edit-source='true' @endif
            @if ($limitHeight) :limit-height='true' @endif
            @if ($default)
            :initial-value="'{{ $default }}'"
            :has-default-store="true"
            @endif
            @if ($inModal) :in-modal="true" @endif
            in-store="value"
        ></a17-wysiwyg>
    @endif

@endif

@unless($renderForBlocks || $renderForModal)
    @push('vuexStore')
        @include('twill::partials.form.utils._translatable_input_store')
    @endpush
@endunless
