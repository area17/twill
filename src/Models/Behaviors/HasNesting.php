<?php

namespace A17\Twill\Models\Behaviors;

use Kalnoy\Nestedset\NodeTrait;

trait HasNesting
{
    use NodeTrait;

    /**
     * Returns the combined slug for this item including all ancestors.
     *
     * @param string|null $locale
     * @return string
     */
    public function getNestedSlug($locale = null)
    {
        return collect([$this->getAncestorsSlug($locale), $this->getSlug($locale)])
            ->filter()
            ->implode('/');
    }

    /**
     * @return string
     */
    public function getNestedSlugAttribute()
    {
        return $this->getNestedSlug();
    }

    /**
     * Returns the combined slug for all ancestors of this item.
     *
     * @param string|null $locale
     * @return string
     */
    public function getAncestorsSlug($locale = null)
    {
        return collect($this->ancestors ?? [])
            ->map(function ($i) use ($locale) { return $i->getSlug($locale); })
            ->implode('/');
    }

    /**
     * @return string
     */
    public function getAncestorsSlugAttribute()
    {
        return $this->getAncestorsSlug();
    }

    public static function saveTreeFromIds($nodeTree)
    {
        $nodeModels = self::all();
        $nodeArrays = self::flattenTree($nodeTree);

        foreach ($nodeArrays as $nodeArray) {
            $nodeModel = $nodeModels->where('id', $nodeArray['id'])->first();

            if ($nodeArray['parent_id'] === null) {
                if (!$nodeModel->isRoot() || $nodeModel->position !== $nodeArray['position']) {
                    $nodeModel->position = $nodeArray['position'];
                    $nodeModel->saveAsRoot();
                }
            } else {
                if ($nodeModel->position !== $nodeArray['position'] || $nodeModel->parent_id !== $nodeArray['parent_id']) {
                    $nodeModel->position = $nodeArray['position'];
                    $nodeModel->parent_id = $nodeArray['parent_id'];
                    $nodeModel->save();
                }
            }
        }
    }

    public static function flattenTree(array $nodeTree, int $parentId = null)
    {
        $nodeArrays = [];
        $position = 0;

        foreach ($nodeTree as $node) {
            $nodeArrays[] = [
                'id' => $node['id'],
                'position' => $position++,
                'parent_id' => $parentId,
            ];

            if (count($node['children']) > 0) {
                $childArrays = self::flattenTree($node['children'], $node['id']);
                $nodeArrays = array_merge($nodeArrays, $childArrays);
            }
        }

        return $nodeArrays;
    }
}
