<?php

namespace A17\Twill\Services\Blocks;

use A17\Twill\Models\Block;
use A17\Twill\Models\Model;

/**
 * DTO for rendering a Block.
 *
 * $model is undefined when in a block editor context.
 * @todo: I guess we can add this by passing the model via the editor preview request? Not blocking.
 */
class RenderData
{
    public function __construct(
        public Block $block,
        public string $editorName,
        public array $children,
        public ?Model $model = null,
        public ?string $parentEditorName = null,
        public bool $inEditor = false,
    ) {
    }

    public function renderChildren(
        string $editorName,
        array $viewMapping = [],
        array $data = [],
        bool $renderChildren = false
    ): string {
        $output = [];
        /** @var \A17\Twill\Services\Blocks\Block $child */
        foreach ($this->children as $child) {
            if ($child->renderData->parentEditorName === $editorName) {
                $output[] = $child->renderView(
                    $viewMapping,
                    $data + ['inEditor' => $this->inEditor],
                    $renderChildren,
                    $this->inEditor
                );
            }
        }

        return join('', $output);
    }
}
