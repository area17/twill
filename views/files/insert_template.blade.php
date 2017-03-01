@foreach($files as $id => $file)
    @php
        $inputName = "files[" . $file_role . "][" . $locale . "][id][]";

        if (isset($repeater) && $repeater) {
            $inputName = "{$moduleName}[{$repeaterIndex}][files][" . $file_role . "][" . $locale . "][id][]";
        }

        $new_row_class = isset($new_row) && $new_row ? 'media-row-new' : '';
    @endphp
    <section class="box media-row {{ $new_row_class }}" id="media-box-{{ $id }}" data-id="{{ $id }}">
        <header class="header_small">
            <ul>
                <li><a href="{{ FileService::getUrl($file->uuid) }}" download><span class="icon icon-download"></span>Download original</a></li>
                <li><a href="#" data-media-remove-trigger><span class="icon icon-remove"></span>Detach</a></li>
            </ul>
        </header>
        <input id="{{ $inputName }}" name="{{ $inputName }}" type="hidden" value="{{ $id }}" />
        <p><span class="icon icon-label icon-duplicate">{{ $file->filename }}</span></p>
    </section>
@endforeach
