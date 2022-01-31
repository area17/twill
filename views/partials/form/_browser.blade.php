@unless(\A17\Twill\TwillServiceProvider::supportsBladeComponents())
    @php
        $name = $name ?? $moduleName;
        $label = $label ?? 'Missing browser label';

        $endpointsFromModules = isset($modules) ? collect($modules)->map(function ($module) {
            return [
                'label' => $module['label'] ?? ucfirst($module['name']),
                'value' => moduleRoute($module['name'], $module['routePrefix'] ?? null, 'browser', $module['params'] ?? [], false)
            ];
        })->toArray() : null;

        $endpoints = $endpoints ?? $endpointsFromModules ?? [];

        $endpoint = $endpoint ?? (!empty($endpoints) ? null : moduleRoute($moduleName, $routePrefix ?? null, 'browser', $params ?? [], false));

        $max = $max ?? 1;
        $itemLabel = $itemLabel ?? strtolower($label);
        $note = $note ?? 'Add' . ($max > 1 ? " up to $max ". $itemLabel : ' one ' . Str::singular($itemLabel));
        $fieldNote = $fieldNote ?? '';
        $sortable = $sortable ?? true;
        $wide = $wide ?? false;
        $buttonOnTop = $buttonOnTop ?? false;
        $browserNote = $browserNote ?? '';
    @endphp
@endunless

<a17-inputframe label="{{ $label }}" name="browsers.{{ $name }}" note="{{ $fieldNote }}">
    <a17-browserfield
        @include('twill::partials.form.utils._field_name')
        item-label="{{ $itemLabel }}"
        :max="{{ $max }}"
        :wide="{{ json_encode($wide) }}"
        endpoint="{{ $endpoint }}"
        :endpoints="{{ json_encode($endpoints) }}"
        modal-title="{{ twillTrans('twill::lang.fields.browser.attach') . ' ' . strtolower($label) }}"
        :draggable="{{ json_encode($sortable) }}"
        browser-note="{{ $browserNote }}"
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
