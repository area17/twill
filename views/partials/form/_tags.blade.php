<a17-vselect
    label="Tags"
    name="tags"
    :multiple="true"
    :selected="{{ json_encode($item->tags->map(function ($tag) { return $tag->name; })) }}"
    :searchable="true"
    :taggable="true"
    :pushTags="true"
    size="small"
    :endpoint="{{ $endpoint }}"
></a17-vselect>
