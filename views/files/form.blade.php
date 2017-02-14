@if ($isBulkUpdate && $currentUser->can('edit'))
    <div class="input">
        <h2>Bulk update</h2>
    </div>
@else
    <input type="hidden" name="id" value="{{ $file->id }}">
    <input type="hidden" name="resourceName" value="{{ $file->filename }}">
    <div class="input">
        <p>{{ $file->filename }} ({{ $file->size }})</p>
        <br>
        <a href="{{ FileService::getUrl($file->uuid) }}" download class="btn btn-small btn-light">Download original</a>
        @if ($currentUser->can('edit') && $file->canDeleteSafely())
        <a class="btn btn-small btn-light-border" data-behavior="delete" data-delete-confirm="Do you really want to delete this {{ strtolower($modelName) }} ?" data-delete-reload="true" data-delete-id="{{ $file->id }}" data-delete-url="{{ moduleRoute($moduleName, $routePrefix, 'destroy', $file->id) }}" href="#" rel="nofollow">Delete</a>
        @endif
    </div>
@endif

@if ($currentUser->can('edit'))
<div class="input">
    <label for="tags">Tags</label>
    <select id="tags" data-behavior="selector" name="tags[]" multiple="multiple" data-selector-ajax-url="{{ route('admin.file-library.files.tags') }}" data-placeholder="Add tags">
        @foreach($tags as $tag)
            <option value="{{ $tag->name }}" selected>{{ $tag->name }}</option>
        @endforeach
    </select>
</div>
@endif

@unless (!$currentUser->can('edit'))
    <div class="input">
        <input class="btn btn-tiny" type="submit" value="Update">
    </div>
@endunless
