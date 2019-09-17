@php
    $name = $name ?? $moduleName;
    $label = $label ?? 'Missing browser label';
    $endpoints = $endpoints ?? [];
    $endpoint = $endpoint ?? (!empty($endpoints) ? null : moduleRoute($moduleName, $routePrefix ?? null, 'browser', $params ?? [], false));
    $max = $max ?? 1;
    $note = $note ?? 'Add' . ($max > 1 ? " up to $max ". strtolower($label) : ' one ' . Str::singular(strtolower($label)));
    $itemLabel = $itemLabel ?? strtolower($label);
    $sortable = $sortable ?? true;
    $wide = $wide ?? false;
@endphp

<a17-inputframe label="{{ $label }}" name="browsers.{{ $name }}">
    <a17-browserfield
        @include('twill::partials.form.utils._field_name')
        item-label="{{ $itemLabel }}"
        :max="{{ $max }}"
        :wide="{{ json_encode($wide) }}"
        endpoint="{{ $endpoint }}"
        :endpoints="{{ json_encode($endpoints) }}"
        modal-title="Attach {{ strtolower($label) }}"
        :draggable="{{ json_encode($sortable) }}"
    >{{ $note }}</a17-browserfield>
</a17-inputframe>

@unless($renderForBlocks)
    @push('vuexStore')
        @if (isset($form_fields['browsers']) && isset($form_fields['browsers'][$name]))
            window.STORE.browser.selected["{{ $name }}"] = {!! json_encode($form_fields['browsers'][$name]) !!}
        @endif
    @endpush
@endunless
