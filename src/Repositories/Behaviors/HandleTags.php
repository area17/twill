<?php

namespace A17\Twill\Repositories\Behaviors;

trait HandleTags
{
    /**
     * @return void
     * @param mixed[] $fields
     */
    public function afterSaveHandleTags(\A17\Twill\Models\Model $object, array $fields)
    {
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

    protected function filterHandleTags($query, &$scopes)
    {
        $this->addRelationFilterScope($query, $scopes, 'tag_id', 'tags');
    }

    private function getTagsQuery()
    {
        return $this->model->allTags()->orderBy('count', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     * @param mixed[] $ids
     */
    public function getTags(string $query = '', array $ids = [])
    {
        $tagQuery = $this->getTagsQuery();

        if (!empty($query)) {
            $tagQuery->where('slug', 'like', '%' . $query . '%');
        }

        foreach ($ids as $id) {
            $tagQuery->whereHas('tagged', function ($query) use ($id): void {
                $query->where('taggable_id', $id);
            });
        }

        return $tagQuery->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getTagsList()
    {
        return $this->getTagsQuery()->where('count', '>', 0)->select('name', 'id')->get()->map(function ($tag): array {
            return [
                'label' => $tag->name,
                'value' => $tag->id,
            ];
        });
    }

}
