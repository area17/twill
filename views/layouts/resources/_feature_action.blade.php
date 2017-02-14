@php
    $icon_class = $icon_class ?? 'icon-feature';
@endphp

<td>
    <a  href="#"
        data-behavior="toggle_active"
        data-toggle-id="{{ $item->id }}"
        data-toggle-url="{{ moduleRoute($moduleName, $routePrefix, 'feature', ['featureField' => $toggle_field]) }}"
        class="icon {{ $icon_class }} {{ ($item->$toggle_field) ? 'active' : '' }}@if ($currentUser->cannot('feature')) disabled @endif"
        title="Feature {{ strtolower($modelName) }}">Feature {{ strtolower($modelName) }}
    </a>
</td>
