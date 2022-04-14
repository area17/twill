<?php

namespace A17\Twill\Models;

use Kalnoy\Nestedset\Collection as BaseNestedsetCollection;

class NestedsetCollection extends BaseNestedsetCollection
{
    /**
     * Build a tree from a list of nodes. Each item will have set children relation.
     *
     * To successfully build tree "id", "_lft" and "parent_id" keys must present.
     *
     * If `$root` is provided, the tree will contain only descendants of that node.
     *
     * @param mixed $root
     *
     * @return Collection
     */
    public function toTree($root = false)
    {
        if ($this->isEmpty()) {
            return new static();
        }

        $this->linkNodes();

        $items = [];

        $root = $this->getRootNodeId($root);

        $ids = collect($this->items)->pluck('id');

        /** @var Model|NodeTrait $node */
        foreach ($this->items as $node) {
            if ($node->getParentId() == $root) {
                $items[] = $node;
            } elseif (!$ids->has($node->getParentId())) {
                $items[] = $node;
            }
        }

        return new static($items);
    }
}
