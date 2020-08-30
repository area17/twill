@php
    $name = $name ?? $moduleName;
    $label = $label ?? 'Missing browser label';
    $browsersWithCreate = $browsersWithCreate ?? [];

    $endpointsFromModules = isset($modules) ? collect($modules)->map(function ($module) {
        return [
            'label' => $module['label'] ?? ucfirst($module['name']),
            'value' => moduleRoute($module['name'], $module['routePrefix'] ?? null, 'browser', $module['params'] ?? [], false)
        ];
    })->toArray() : null;

    $endpoints = $endpoints ?? $endpointsFromModules ?? [];

    $createUrl = moduleRoute($moduleName, $routePrefix ?? null, 'store', []);

    $endpoint = $endpoint ?? (!empty($endpoints) ? null : moduleRoute($moduleName, $routePrefix ?? null, 'browser', $params ?? [], false));

    $max = $max ?? 1;
    $itemLabel = $itemLabel ?? strtolower($label);
    $note = $note ?? 'Add' . ($max > 1 ? " up to $max ". $itemLabel : ' one ' . Str::singular($itemLabel));
    $fieldNote = $fieldNote ?? '';
    $sortable = $sortable ?? true;
    $wide = $wide ?? false;
    $buttonOnTop = $buttonOnTop ?? false;

    $allowCreate = collect($browsersWithCreate)->filter(function ($browser) use ($moduleName) {
        if (is_array($browser)) {
            return $moduleName === ($browser['moduleName'] ?: $browser['browserName']);
        } else {
            return $moduleName === $browser;
        }
    })->toArray();

    $allowCreate = !!$allowCreate ?: false;
@endphp

<a17-inputframe label="{{ $label }}" name="browsers.{{ $name }}" note="{{ $fieldNote }}">
    <a17-browserfield
        @include('twill::partials.form.utils._field_name')
        item-label="{{ $itemLabel }}"
        :max="{{ $max }}"
        :wide="{{ json_encode($wide) }}"
        endpoint="{{ $endpoint }}"
        create-url="{{ $createUrl }}"
        :allow-create="{{ json_encode($allowCreate) }}"
        :endpoints="{{ json_encode($endpoints) }}"
        modal-title="{{ twillTrans('twill::lang.fields.browser.attach') . ' ' . strtolower($label) }}"
        :draggable="{{ json_encode($sortable) }}"
        @if ($buttonOnTop) :button-on-top="true" @endif
    >{{ $note }}</a17-browserfield>
</a17-inputframe>

@unless($renderForBlocks)
    @push('vuexStore')
        @if (isset($form_fields['browsers']) && isset($form_fields['browsers'][$name]))
            window['{{ config('twill.js_namespace') }}'].STORE.browser.selected["{{ $name }}"] = {!! json_encode($form_fields['browsers'][$name]) !!}
        @endif
    @endpush
@endunless
