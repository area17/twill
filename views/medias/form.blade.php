@if ($isBulkUpdate && $currentUser->can('edit'))
    <div class="input">
        <h2>Bulk update</h2>
    </div>
@else
    <input type="hidden" name="id" value="{{ $media->id }}">
    <input id="media_thumbnail" name="thumbnail" type="hidden" value="{{ ImageService::getCmsUrl($media->uuid, ['h' => 130, 'w' => 130, 'fit' => 'crop']) }}">
    <input id="media_original" name="original" type="hidden" value="{{ ImageService::getCmsUrl($media->uuid) }}">

    <div class="input">
        <img src="{{ ImageService::getCmsUrl($media->uuid, ['h' => 60]) }}" class="preview preview_img">
        <br>
        <a href="{{ ImageService::getRawUrl($media->uuid) }}" download class="btn btn-small btn-light">Download original</a>
        @if ($currentUser->can('edit') && $media->canDeleteSafely())
        <a class="btn btn-small btn-light-border" data-behavior="delete" data-delete-confirm="Do you really want to delete this {{ strtolower($modelName) }} ?" data-delete-reload="true" data-delete-id="{{ $media->id }}" data-delete-url="{{ moduleRoute($moduleName, $routePrefix, 'destroy', $media->id) }}" href="#" rel="nofollow">Delete</a>
        @endif
    </div>
    @if ($currentUser->can('edit'))
    <div class="input">
        <label for="media_alt_text">Alt text</label>
        <input id="media_alt_text" name="alt_text" placeholder="Alt text" type="text" value="{{ $media->alt_text }}">
    </div>

    <div class="input">
        <label for="media_caption">Caption</label>
        <input id="media_caption" name="caption" placeholder="Caption" type="text" value="{{ $media->caption }}">
    </div>
    @endif
@endif

@if ($currentUser->can('edit'))
    <div class="input">
        <label for="tags">Tags</label>
        <select id="tags" data-behavior="selector" name="tags[]" multiple="multiple" data-selector-ajax-url="{{ route('admin.media-library.medias.tags') }}" data-placeholder="Add tags">
            @foreach($tags as $tag)
                <option value="{{ $tag->name }}" selected>{{ $tag->name }}</option>
            @endforeach
        </select>
    </div>

    @unless ($isBulkUpdate)
    <div class="input">
        <label for="media_filename">Filename</label>
        <input id="media_filename" name="filename" type="text" readonly value="{{ $media->filename }}">
    </div>

    <div class="input">
        <label for="media_dimensions">Dimensions</label>
        <input id="media_dimensions" name="dimensions" type="text" readonly value="{{ $media->dimensions }}">
    </div>
    @endunless
@endif

@unless (!$currentUser->can('edit'))
    <div class="input">
        <input class="btn btn-tiny" type="submit" value="Update">
    </div>
@endunless
