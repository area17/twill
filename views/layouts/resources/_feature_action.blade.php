@php
    $icon_class = $icon_class ?? 'icon-feature';
    $itemIsFeaturable = $item->canFeature ?? true;
@endphp

<td>
    <a  href="#"
        data-behavior="toggle_active"
        data-toggle-id="{{ $item->id }}"
        data-toggle-url="{{ moduleRoute($moduleName, $routePrefix, 'feature', ['featureField' => $toggle_field]) }}"
        class="icon {{ $itemIsFeaturable ? $icon_class : '' }} {{ ($item->$toggle_field) ? 'active' : '' }}@if ($currentUser->cannot('feature') || !$itemIsFeaturable) disabled @endif"
        title="Feature {{ strtolower($modelName) }}">Feature {{ strtolower($modelName) }}
    </a>
</td>
