<td>
    <a class="icon icon-edit"  href="{{ moduleRoute($moduleName, $routePrefix, 'edit', array_merge (isset($parent_id) ? [$parent_id] : [], [$item->id])) }}" rel="nofollow" title="Edit">Edit</a>
    @if($show_locale_edit_links)
        @foreach(getLocales() as $locale)
            @unless($loop->first && $loop->last)
                <a href="{{ moduleRoute($moduleName, $routePrefix, 'edit', array_merge (isset($parent_id) ? [$parent_id] : [], [$item->id, 'locale' => $locale])) }}" rel="nofollow" title="Edit in {{ $locale }}">
                    <span class="lang_tag">{{ strtoupper($locale) }}</span>
                </a>
            @endunless
        @endforeach
    @endif
</td>
