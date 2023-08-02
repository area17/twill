@if ($unpack ?? false)
    <a17-singleselect
        label="{{ $label }}"
        {!! $formFieldName() !!}
        :options='{{ json_encode($options) }}'
        :columns="{{ $columns }}"
        @if (isset($default)) selected="{{ $default }}" @endif
        @if ($required) :required="true" @endif
        @if ($inModal) :in-modal="true" @endif
        @if ($inTable) :in-table="true" :inline="true" @endif
        @if (!$inGrid) :grid="false" @endif
        @if ($disabled) disabled @endif
        @if ($addNew) add-new='{{ $storeUrl }}' @elseif ($note) note='{{ $note }}' @endif
        :has-default-store="true"
        in-store="value"
    >
        @if($addNew)
            <div slot="addModal">
                @php
                    unset($note, $placeholder, $emptyText, $default, $required, $inModal, $addNew, $options);
                @endphp
                @partialView(($formModuleName ?? null), 'create', ['renderForModal' => true, 'fieldsInModal' => true])
            </div>
        @endif
    </a17-singleselect>
@elseif ($native ?? false)
    <a17-select
        label="{{ $label }}"
        {!! $formFieldName() !!}
        :options='{{ json_encode($options) }}'
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if (isset($default)) selected="{{ $default }}" @endif
        @if ($required) :required="true" @endif
        @if ($inModal) :in-modal="true" @endif
        @if ($disabled) disabled @endif
        @if ($addNew) add-new='{{ $storeUrl }}' @elseif ($note) note='{{ $note }}' @endif
        :has-default-store="true"
        size="large"
        in-store="value"
    >
        @if($addNew)
            <div slot="addModal">
                @php
                    unset($note, $placeholder, $emptyText, $default, $required, $inModal, $addNew, $options);
                @endphp
                @partialView(($formModuleName ?? null), 'create', ['renderForModal' => true, 'fieldsInModal' => true])
            </div>
        @endif
    </a17-select>
@else
    <a17-vselect
        label="{{ $label }}"
        {!! $formFieldName() !!}
        :options='{{ json_encode($options) }}'
        @if ($emptyText ?? false) empty-text="{{ $emptyText }}" @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if (isset($default)) :selected="{{ json_encode(collect($options)->first(function ($option) use ($default) {
            return $option['value'] === $default;
        })) }}" @endif
        @if ($required) :required="true" @endif
        @if ($disabled) disabled @endif
        @if ($inModal) :in-modal="true" @endif
        @if ($addNew) add-new='{{ $storeUrl }}' @elseif ($note) note='{{ $note }}' @endif
        :has-default-store="true"
        @if ($searchable) :searchable="true" @endif
        size="large"
        in-store="inputValue"
    >
        @if($addNew)
            <div slot="addModal">
                @php
                    unset($note, $placeholder, $emptyText, $default, $required, $inModal, $addNew, $options);
                @endphp
                @partialView(($formModuleName ?? null), 'create', ['renderForModal' => true, 'fieldsInModal' => true])
            </div>
        @endif
    </a17-vselect>
@endif

@unless($renderForBlocks || $renderForModal || (!isset($item->$name) && null == $formFieldsValue = getFormFieldsValue($form_fields, $name)))
@push('vuexStore')
    @include('twill::partials.form.utils._selector_input_store')
@endpush
@endunless
