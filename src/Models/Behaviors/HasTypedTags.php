<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\Tag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTypedTags
{
    /**
     * {@inheritdoc}
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(
            static::$tagsModel,
            'taggable',
            config('twill.tagged_table', 'tagged'),
            'taggable_id',
            'tag_id'
        )->where('tag_type', Tag::class);
    }
}
