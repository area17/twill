@php
    $with_multiple = $with_multiple ?? false;
    $media_max = $with_multiple ? ($max ?? 1) :1;
    $role_relationship = $relationship ?? $role_relationship;
    $role_relationship_name = $relationship_name ?? $role_relationship_name;
    $module_name = $module_name ?? $role_relationship;
    $belongs_to = $belongs_to ?? null;
    $input_name = $belongs_to ?? $role_relationship;

    if (isset($repeater) && $repeater) {
        if (isset($item)) {
            $item = $item[$moduleName][$repeaterIndex];
        }
        $input_name = $moduleName . '[' . $repeaterIndex . '][' . $role_relationship . ']';
        $role_relationship_repeater = $role_relationship . '_' . $moduleName . '_' . $repeaterIndex;
    }
@endphp

<script>
    var resources_options_{{$role_relationship_repeater or $role_relationship}} = {
      "role": "{{ $role_relationship_repeater or $role_relationship }}",
      "type": "{{ $with_multiple ? 'generic_multiple' : 'generic_single' }}",
      "url": "{{ moduleRoute($module_name, $routePrefix, 'browser', $params ?? [])}}",
      "title": "Attach {{ $role_relationship_name or $role_relationship }}",
      "max": {{ $media_max }}
    }
</script>

<section class="box bucket-medias @if (isset($item) && (count($item->$role_relationship) >= $media_max || ($belongs_to && $item->$belongs_to))) full @endif" id="generic_library_multiple" data-behavior="media_library" data-options="resources_options_{{$role_relationship_repeater or $role_relationship}}">
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

    @php
        $browser_items = collect();
        if (isset($item)) {
            $input_value = $belongs_to ? $item->$belongs_to : implode (',', array_pluck($item->$relationship, 'id'));
            $belongs_to_relationship = $belong_to_field ?? str_singular($role_relationship);
            $belongs_to_items = $belongs_to && $item->$belongs_to_relationship !== null ? [$item->$belongs_to_relationship] : [];
            $browser_items = $belongs_to ? collect($belongs_to_items) : $item->$role_relationship;
        }
    @endphp

    <input type="hidden" name="{{ $input_name }}" value="{{ $input_value or ''}}">
    <div class="table_container">
        <table @unless($belongs_to) data-behavior="sortable" @endunless data-hidden-field="{{ $input_name }}">
            <thead>
                <tr>
                    @resourceView(camel_case($role_relationship), 'browser_insert', ['headers_only' => true, 'element_role' => $role_relationship])
                </tr>
            </thead>

            <tbody data-media-bucket="{{ $role_relationship_repeater or $role_relationship }}" data-media-template="{{ moduleRoute($module_name, $routePrefix, 'insert', ['with_multiple' => $with_multiple]) }}" data-media-item=".media-row">

                @if(isset($item))
                    @resourceView(camel_case($role_relationship), 'browser_insert', ['items' => $browser_items, 'element_role' => $role_relationship])
                @endif

            </tbody>
        </table>
    </div>

    <footer data-media-bt>
        <button type="button" class="btn btn-small btn-border" data-media-bt-trigger>Attach {{ $role_relationship_name or $role_relationship }}</button>
    </footer>
</section>