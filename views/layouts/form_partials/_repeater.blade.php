@php
    $form_field_name = $form_field_name ?? $moduleName;
@endphp

<section class="box" data-behavior="repeater_ajax sortable_box" data-repeater-url="{{ moduleRoute($moduleName, $routePrefix ?? null, 'repeater') }}">
    <header class="header_small">
        <h3>{{ $title or ucfirst($moduleName) }}</h3>
    </header>
    @if (isset($form_fields[$form_field_name]))
        @foreach($form_fields[$form_field_name] as $index => $module)
            @include("admin.{$moduleName}.repeater", [
                'repeaterIndex' => $index,
                'moduleName' => $moduleName,
                'routePrefix' => $routePrefix ?? null,
            ])
        @endforeach
    @endif

    <footer data-repeater-footer>
        <a href="#" class="btn btn-small btn-border" data-trigger="">Add {{ $title_singular or ucfirst(str_singular($moduleName)) }}</a>
    </footer>
</section>
