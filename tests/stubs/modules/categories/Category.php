<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Model;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use HasBlocks,
        HasTranslation,
        HasSlug,
        HasPosition,
        NodeTrait,
        HasRevisions;

    protected $fillable = ['published', 'title', 'description', 'position'];

    // uncomment and modify this as needed if you use the HasTranslation trait
    public $translatedAttributes = ['title', 'active'];

    // uncomment and modify this as needed if you use the HasSlug trait
    public $slugAttributes = ['title'];

    // add checkbox fields names here (published toggle is itself a checkbox)
    public $checkboxes = ['published'];

    public static function saveTreeFromIds($nodesArray)
    {
        $parentNodes = self::find(array_pluck($nodesArray, 'id'));

        if (is_array($nodesArray)) {
            $position = 1;
            foreach ($nodesArray as $nodeArray) {
                $node = $parentNodes->where('id', $nodeArray['id'])->first();
                $node->position = $position++;
                $node->saveAsRoot();
            }
        }

        $parentNodes = self::find(array_pluck($nodesArray, 'id'));

        self::rebuildTree($nodesArray, $parentNodes);
    }

    public static function rebuildTree($nodesArray, $parentNodes)
    {
        if (is_array($nodesArray)) {
            foreach ($nodesArray as $nodeArray) {
                $parent = $parentNodes->where('id', $nodeArray['id'])->first();
                if (
                    isset($nodeArray['children']) &&
                    is_array($nodeArray['children'])
                ) {
                    $position = 1;
                    $nodes = self::find(
                        array_pluck($nodeArray['children'], 'id')
                    );
                    foreach ($nodeArray['children'] as $child) {
                        //append the children to their (old/new)parents
                        $descendant = $nodes
                            ->where('id', $child['id'])
                            ->first();
                        $descendant->position = $position++;
                        $descendant->parent_id = $parent->id;
                        $descendant->save();
                        self::rebuildTree($nodeArray['children'], $nodes);
                    }
                }
            }
        }
    }
}
