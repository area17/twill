@php
    $name = $name ?? $moduleName;
    $label = $label ?? 'Missing browser label';
    $endpoint = $endpoint ?? moduleRoute($moduleName, $routePrefix ?? null, 'browser', $params ?? []);
    $max = $max ?? 1;
    $note = $note ?? 'Add' . ($max > 1 ? " up to $max ". strtolower($label) : ' one ' . str_singular(strtolower($label)));
    $itemLabel = $itemLabel ?? strtolower($label);
@endphp

<a17-inputframe label="{{ $label }}">
    <a17-browserfield
        @if ($renderForBlocks) :name="fieldName('{{ $name }}')" @else name="{{ $name }}" @endif
        :max="{{ $max }}"
        item-label="{{ $itemLabel }}"
        endpoint="{{ $endpoint }}"
        modal-title="Attach {{ strtolower($label) }}"
    >{{ $note }}</a17-browserfield>
</a17-inputframe>

@unless($renderForBlocks)
@push('vuexStore')
    @if (isset($item->$name))
        window.STORE.browser.selected["{{ $name }}"] = {!! json_encode($item->$name) !!}
    @endif
@endpush
@endunless
