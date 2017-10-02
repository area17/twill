@php
    $with_multiple = $with_multiple ?? false;
    $files_max = $with_multiple ? ($max ?? 1) : 1;
@endphp

@foreach(getLocales() as $locale)
    <div class="field_with_lang" data-lang="{{ $locale }}">
        @php
            $file_bucket_identifier = $file_role . '_' . str_replace('-', '_', $locale);

            if (isset($repeater) && $repeater) {
                // make a copy and reset for each locale otherwise we will have the original form fields
                // only for the first locale
                $form_fields_copy = $form_fields;
                $form_fields = $form_fields[$moduleName][$repeaterIndex] ?? null;
                $file_bucket_identifier_repeater = $file_bucket_identifier . '_' . $moduleName . '_' . $repeaterIndex;
            }

            $bucket_full_class = isset($form_fields['files']) && isset($form_fields['files'][$file_role][$locale]) && count($form_fields['files'][$file_role][$locale]) >= $files_max ? 'full' : '';
        @endphp
        <script>
            var file_library_options_{{ $file_bucket_identifier_repeater or $file_bucket_identifier }} = {
              "role": "{{ $file_bucket_identifier_repeater or $file_bucket_identifier }}",
              "type": "{{ $with_multiple ? 'media_multiple' : 'media_single' }}",
              "url": "{{ route('admin.file-library.files.index') }}",
              "title": "Add {{ $file_role_name or $file_role }} {{ $with_multiple ? 'files' : 'file' }}",
              "max": {{ $files_max }}
            }
        </script>
        <section class="box {{ $bucket_full_class }}" data-behavior="media_library" data-options="file_library_options_{{ $file_bucket_identifier_repeater or $file_bucket_identifier }}">
            <header class="header_small">
                <h3>
                    <b>{{ isset($file_role_name) ? ucfirst($file_role_name) : ucfirst($file_role) }} {{ $with_multiple ? 'files' : 'file' }}</b>
                    @if (!empty($required))
                        <label style="display: inline;"><abbr title="required">*</abbr></label>
                    @endif
                    @unless($loop->first && $loop->last)
                        <span class="lang_tag" data-behavior="lang_toggle" style="font-family: Arial,sans-serif; padding: 0 6px 0 6px; color: #fff; font-size: 9px; letter-spacing: 0.05em; border-radius: 2px; background: #b2b2b2; display: inline-block; vertical-align: text-bottom; height: 18px; line-height: 18px; white-space: nowrap; margin-left: 5px; cursor: pointer; text-transform: uppercase;">
                            {{ strtoupper($locale) }}
                        </span>
                    @endunless
                    @if (isset($hint))
                        <ul>
                            <li><span class="icon icon-label icon-bang">{{ $hint }}</span></li>
                        </ul>
                    @endif
                </h3>
            </header>
            <div data-media-bucket="{{ $file_bucket_identifier_repeater or $file_bucket_identifier }}" data-media-template="{{ moduleRoute($moduleName, $routePrefix, 'file', [
                'with_multiple' => $with_multiple,
                'file_role' => $file_role,
                'locale' => $locale,
                'repeater' => $repeater ?? false,
                'repeater_index' => $repeaterIndex ?? null
            ]) }}" data-media-item=".media-row" data-behavior="{{ $with_multiple ? 'sortable_box' : ''}}">
                @if (isset($form_fields['files']) && isset($form_fields['files'][$file_role][$locale]))
                    @include('cms-toolkit::files.insert_template', [
                        'files' => $form_fields['files'][$file_role][$locale],
                        'with_multiple' => $with_multiple,
                        'locale' => $locale,
                        'repeater' => $repeater ?? false,
                        'repeater_index' => $repeaterIndex ?? null
                    ])
                @endif
            </div>
            <footer data-media-bt>
                <button type="button" class="btn btn-small btn-border" data-media-bt-trigger>Add {{ $file_role_name or $file_role }} {{ $with_multiple ? 'files' : 'file' }}</button>
            </footer>
        </section>
    </div>
    @php
        if (isset($form_fields_copy)) {
            $form_fields = $form_fields_copy;
        }
    @endphp
@endforeach
