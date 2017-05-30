@php
    $with_multiselect = $with_multiselect ?? false;
@endphp

<section class="box" @if($with_multiselect) style="border: none; margin: -15px 0 0 0;" @endif data-behavior="repeater_ajax sortable_box" data-repeater-url="{{ moduleRoute($moduleName, $routePrefix ?? null, 'repeater') }}">
    @unless ($with_multiselect)
        <header class="header_small">
            <h3>{{ $title or ucfirst($moduleName) }}</h3>
        </header>
    @endunless
    @if (isset($form_fields[$moduleName]))
        @foreach($form_fields[$moduleName] as $index => $module)
            @include("admin.{$moduleName}.repeater", [
                'repeater' => true,
                'repeaterIndex' => $index,
                'moduleName' => $moduleName,
                'routePrefix' => $routePrefix ?? null,
            ])
        @endforeach
    @endif

    <footer data-repeater-footer>
        <a href="#" class="btn btn-tiny btn-border" data-trigger="">{{ $custom_title_prefix or 'Create new' }} {{ $title_singular or str_singular($moduleName) }}</a>
    </footer>
</section>
