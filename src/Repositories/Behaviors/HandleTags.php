<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait HandleTags
{
    public function afterSaveHandleTags(TwillModelContract $object, array $fields): void
    {
        if (isset($fields['tags']) && is_array($fields['tags'])) {
            $fields['tags'] = implode(',', $fields['tags']);
        }

        if (preg_match("/\p{Han}+/u", $fields['tags'] ?? '')) {
            $object->setSlugGenerator(function ($slug) {
                return mb_strtolower(
                    trim(preg_replace('/([?]|\p{P}|\s)+/u', '-', $slug))
                );
            });
        } else {
            $object->setSlugGenerator('Illuminate\Support\Str::slug');
        }

        if (!isset($fields['bulk_tags']) && !isset($fields['previous_common_tags'])) {
            if (!$this->shouldIgnoreFieldBeforeSave('tags')) {
                $object->setTags($fields['tags'] ?? []);
            }
        } elseif (!$this->shouldIgnoreFieldBeforeSave('bulk_tags')) {
            $previousCommonTags = $fields['previous_common_tags']->pluck('name')->toArray();
            if (!empty($previousCommonTags) && !empty($difference = array_diff($previousCommonTags, $fields['bulk_tags'] ?? []))) {
                $object->untag($difference);
            }
            $object->tag($fields['bulk_tags'] ?? []);
        }
    }

    private function getTagsQuery(): Builder
    {
        return $this->model->allTags()->orderBy('count', 'desc');
    }

    public function getTags(?string $query = null, array $ids = []): Collection
    {
        $tagQuery = $this->getTagsQuery();

        if (!empty($query)) {
            $tagQuery->where('slug', getLikeOperator(), '%' . $query . '%');
        }

        foreach ($ids as $id) {
            $tagQuery->whereHas('tagged', function ($query) use ($id) {
                $query->where('taggable_id', $id);
            });
        }

        return $tagQuery->get();
    }

    public function getTagsList(): \Illuminate\Support\Collection
    {
        return $this->getTagsQuery()->where('count', '>', 0)->select('name', 'id')->get()->map(function ($tag) {
            return [
                'label' => $tag->name,
                'value' => $tag->id,
            ];
        });
    }
}
