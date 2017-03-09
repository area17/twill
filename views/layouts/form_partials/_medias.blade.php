@php
    $with_multiple = $with_multiple ?? false;
    $with_background_position = $with_background_position ?? false;
    $with_crop = $with_crop ?? true;
    $media_max = $with_multiple ? ($max ?? 1) :1;

    if (isset($repeater) && $repeater) {
        $form_fields = $form_fields[$moduleName][$repeaterIndex] ?? null;
        $media_role_repeater = $media_role . '_' . $moduleName . '_' . $repeaterIndex;
    }

    $media_class = isset($form_fields['medias']) && isset($form_fields['medias'][$media_role]['images']) && count($form_fields['medias'][$media_role]['images']) >= $media_max ? 'full' : '';
@endphp

<script>
    var media_library_options_{{ $media_role_repeater or $media_role }} = {
      "role": "{{ $media_role_repeater or $media_role }}",
      "backend_role" : "{{ $media_role }}",
      "type": "{{ $with_multiple ? 'media_multiple' : 'media_single' }}",
      "url": "{{ route('admin.media-library.medias.index') }}",
      "title": "Attach {{ $media_role_name or $media_role }} {{ $with_multiple ? 'images' : 'image' }}",
      "max": {{ $media_max }}
    }
</script>

<section id="media_library" class="box {{ $media_class }} box-background" data-behavior="media_library" data-options="media_library_options_{{ $media_role_repeater or $media_role }}">
    <header class="header_small">
        <h3>
        <b>{{ isset($media_role_name) ? ucfirst($media_role_name) : ucfirst($media_role) }} {{ $with_multiple ? 'images' : 'image' }}</b>
        @if (!empty($required))
            <label style="display: inline;"><abbr title="required">*</abbr></label>
        @endif
        @if (isset($hint))
            <ul>
                <li><span class="icon icon-label icon-bang">{{ $hint }}</span></li>
            </ul>
        @endif
        </h3>
    </header>

    <div data-media-bucket="{{ $media_role_repeater or $media_role }}" data-media-template="{{ moduleRoute($moduleName, $routePrefix, 'media', [
        'with_crop' => $with_crop,
        'with_multiple' => $with_multiple,
        'with_background_position' => $with_background_position,
        'repeater' => $repeater ?? false,
        'repeater_index' => $repeaterIndex ?? null
    ]) }}" data-media-item=".media-row" data-behavior="{{ $with_multiple ? 'sortable_box' : ''}}">
        @if (isset($form_fields['medias']) && isset($form_fields['medias'][$media_role]['images']))
            @include('cms-toolkit::medias.insert_template', [
                'images' => $form_fields['medias'][$media_role]['images'],
                'crops' => $form_fields['medias'][$media_role]['crops'],
                'with_crop' => $with_crop,
                'with_multiple' => $with_multiple,
                'with_background_position' => $with_background_position,
                'backend_role' => $media_role,
                'repeater' => $repeater ?? false,
                'repeater_index' => $repeaterIndex ?? null
            ])
        @endif
    </div>
    <footer data-media-bt>
        <button type="button" class="btn btn-small btn-border" data-media-bt-trigger>Attach {{ $media_role_name or $media_role }} {{ $with_multiple ? 'images' : 'image' }}</button>
    </footer>
</section>
