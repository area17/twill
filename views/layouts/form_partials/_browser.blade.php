@php
    $with_multiple = $with_multiple ?? false;
    $media_max = $with_multiple ? ($max ?? 1) :1;
    $role_relationship = $relationship ?? $role_relationship;
    $role_relationship_name = $relationship_name ?? $role_relationship_name;
    $module_name = $module_name ?? $role_relationship;
@endphp

<script>
    var resources_options_{{$role_relationship}} = {
      "role": "{{ $role_relationship }}",
      "type": "{{ $with_multiple ? 'generic_multiple' : 'generic_single' }}",
      "url": "{{ moduleRoute($module_name, $routePrefix, 'browser')}}",
      "title": "Attach {{ $role_relationship_name or $role_relationship }}",
      "max": {{ $media_max }}
    }
</script>

<section class="box bucket-medias @if (isset($item) && count($item->$role_relationship) >= $media_max) full @endif" id="generic_library_multiple" data-behavior="media_library" data-options="resources_options_{{$role_relationship}}">
    <header class="header_small">
    <h3>
        <b>{{ isset($role_relationship_name) ? ucfirst($role_relationship_name) : ucfirst($role_relationship) }}
        </b>
        @if (isset($hint))
            <ul>
                <li><span class="icon icon-label icon-bang">{{ $hint }}</span></li>
            </ul>
        @endif
    </h3>
    </header>

    <input type="hidden" name="{{ $role_relationship }}" value="@if(isset($item)){{ implode (',', array_pluck($item->$role_relationship, 'id')) }}@endif">
    <div class="table_container">
        <table data-behavior="sortable" data-hidden-field="{{ $role_relationship }}">
            <thead>
                <tr>
                    @resourceView(camel_case($role_relationship), 'browser_insert', ['headers_only' => true, 'element_role' => $role_relationship])
                </tr>
            </thead>

            <tbody data-media-bucket="{{ $role_relationship }}" data-media-template="{{ moduleRoute($module_name, $routePrefix, 'insert', ['with_multiple' => $with_multiple]) }}" data-media-item=".media-row">

                @if(isset($item))
                    @resourceView(camel_case($role_relationship), 'browser_insert', ['items' => $item->$role_relationship, 'element_role' => $role_relationship])
                @endif

            </tbody>
        </table>
    </div>

    <footer data-media-bt>
        <button type="button" class="btn btn-small btn-border" data-media-bt-trigger>Attach {{ $role_relationship_name or $role_relationship }}</button>
    </footer>
</section>
