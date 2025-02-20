<?php

namespace App\Models;

use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Models\Behaviors\HasFiles;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasPosition;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Models\Model;
use A17\Twill\Repositories\BlockRepository;

class Article extends Model implements Sortable
{
    use HasBlocks;
    use HasSlug;
    use HasMedias;
    use HasFiles;
    use HasRevisions;
    use HasPosition;

    // #region constants
    public const DEFAULT_TEMPLATE = 'full_article';

    public const AVAILABLE_TEMPLATES = [
        [
            'value' => 'full_article',
            'label' => 'Full Article',
            'block_selection' => ['article-header', 'article-paragraph', 'article-references'],
        ],
        [
            'value' => 'linked_article',
            'label' => 'Linked Article',
            'block_selection' => ['article-header', 'linked-article'],
        ],
        [
            'value' => 'empty',
            'label' => 'Empty',
            'block_selection' => [],
        ],
    ];

    // #endregion constants

    public const AVAILABLE_BLOCKS = ['article-header', 'article-paragraph', 'article-references', 'linked-article'];

    // #region fillable
    protected $fillable = [
        'published',
        'title',
        'description',
        'position',
        'template',
    ];

    // #endregion fillable

    public $slugAttributes = [
        'title',
    ];

    // #region accessor
    public function getTemplateLabelAttribute()
    {
        $template = collect(static::AVAILABLE_TEMPLATES)->firstWhere('value', $this->template);

        return $template['label'] ?? '';
    }

    // #endregion accessor

    // #region prefill
    public function getTemplateBlockSelectionAttribute()
    {
        $template = collect(static::AVAILABLE_TEMPLATES)->firstWhere('value', $this->template);

        return $template['block_selection'] ?? [];
    }

    public function prefillBlockSelection()
    {
        $i = 1;

        foreach ($this->template_block_selection as $blockType) {
            app(BlockRepository::class)->create([
                'blockable_id' => $this->id,
                'blockable_type' => static::class,
                'position' => $i++,
                'content' => '{}',
                'type' => $blockType,
            ]);
        }
    }

    // #endregion prefill
}
