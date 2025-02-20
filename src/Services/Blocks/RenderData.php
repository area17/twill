<?php

namespace A17\Twill\Services\Blocks;

use A17\Twill\Models\Block;
use A17\Twill\Models\Contracts\TwillModelContract;

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
        public ?TwillModelContract $model = null,
        public ?string $parentEditorName = null,
        public bool $inEditor = false,
    ) {
    }

    public function renderChildren(
        string $editorName,
        array $viewMapping = [],
        array $data = [],
    ): string {
        $output = [];
        /** @var \A17\Twill\Services\Blocks\Block $child */
        foreach ($this->children as $child) {
            if ($child->renderData->parentEditorName === $editorName) {
                $output[] = $child->renderView(
                    $viewMapping,
                    $data + ['inEditor' => $this->inEditor],
                    $this->inEditor
                );
            }
        }

        return implode('', $output);
    }

    public function getChildrenFor(
        string $editorName,
        array $viewMapping = [],
        array $data = [],
    ): array {
        $output = [];
        /** @var \A17\Twill\Services\Blocks\Block $child */
        foreach ($this->children as $child) {
            if ($child->renderData->parentEditorName === $editorName) {
                $output[] = $child->renderView(
                    $viewMapping,
                    $data + ['inEditor' => $this->inEditor],
                    $this->inEditor
                );
            }
        }

        return $output;
    }
}
