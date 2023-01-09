<?php

namespace A17\Twill\View\Components\Blocks;

use A17\Twill\Models\Block;
use A17\Twill\Services\Blocks\Block as A17Block;
use A17\Twill\Services\Blocks\RenderData;
use A17\Twill\Services\Forms\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\Component;

abstract class TwillBlockComponent extends Component
{
    public ?Block $block = null;
    public ?RenderData $renderData = null;
    public bool $inEditor = false;

    final public function __construct()
    {
    }

    public static function forRendering(Block $block, RenderData $renderData, bool $inEditor): static
    {
        $instance = new static();

        $instance->block = $block;
        $instance->renderData = $renderData;
        $instance->inEditor = $inEditor;

        return $instance;
    }

    public function image(string $role, string $crop = 'default', array $params = []): ?string
    {
        return $this->block->image($role, $crop, $params);
    }

    public function input(string $fieldName): mixed
    {
        return $this->block->input($fieldName);
    }

    public function translatedInput(string $fieldName): mixed
    {
        return $this->block->translatedInput($fieldName);
    }

    /**
     * @return Collection<A17Block>
     */
    public function repeater(string $repeaterName): Collection
    {
        return collect($this->renderData->children)->where('name', $repeaterName);
    }

    /**
     * The $block argument is optional as there may not be a block yet.
     * You will have to write your own condition if you want to utilize data from the block.
     */
    public static function getBlockTitle(?Block $block = null): string
    {
        return Str::replace('Block', '', Str::afterLast(static::class, '\\'));
    }

    public static function getBlockGroup(): string
    {
        return 'app';
    }

    public static function getBlockIcon(): string
    {
        return 'text';
    }

    public function getValidationRules(): array
    {
        return [];
    }

    public function getTranslatableValidationRules(): array
    {
        return [];
    }

    abstract public function getForm(): Form;

    final public function renderForm(): View
    {
        return view('twill::partials.form.renderer.block_form', [
            'fields' => $this->getForm()->renderForBlocks()
        ]);
    }
}
