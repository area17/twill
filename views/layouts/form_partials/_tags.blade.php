<div class="input">
    <label for="tags">Tags</label>
    <select id="tags" data-behavior="selector" name="tags[]" multiple="multiple" data-selector-ajax-url="{{ moduleRoute($moduleName, $routePrefix, 'tags') }}" data-placeholder="Add tags">
        @if (isset($item))
            @foreach($item->tags as $tag)
                <option value="{{ $tag->name }}" selected>{{ $tag->name }}</option>
            @endforeach
        @endif
    </select>
</div>
