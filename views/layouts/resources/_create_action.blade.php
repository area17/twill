@php
    $createTitle = isset($title) ? str_singular($title) : strtolower($modelName);
@endphp

<a href="{{ moduleRoute($moduleName, $routePrefix, 'create') }}" class="btn btn-primary" title="Add {{ $createTitle }}">Add {{ $createTitle }}</a>
