@php
    $createTitle = isset($title) ? str_singular($title) : strtolower($modelName);
    $param = isset($parent_id) ? [$parent_id] : [];
@endphp

<a href="{{ moduleRoute($moduleName, $routePrefix, 'create', $param) }}" class="btn btn-primary" title="Add {{ $createTitle }}">Add {{ $createTitle }}</a>
