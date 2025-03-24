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

    public static function getCrops(): array
    {
        return [];
    }

    /**
     * This string should contain no special characters or spaces.
     *
     * It will be used as the database identifier for the block, it should not change dynamically nor should it overlap
     * with an existing block.
     */
    public static function getBlockIdentifier(): string
    {
        return Str::slug(static::getBlockGroup() . '-' . static::getBlockName());
    }

    public static function getBlockName(): string
    {
        return Str::afterLast(static::class, '\\');
    }

    /**
     * @return Collection<A17Block>
     */
    public function repeater(string $repeaterName): Collection
    {
        $baseList = collect($this->renderData->children)
            ->where('name', $repeaterName);

        if ($baseList->isEmpty()) {
            $baseList = collect($this->renderData->children)
                ->where('name', 'dynamic-repeater-' . $repeaterName);
        }

        return $baseList;
    }

    /**
     * You can use this method to use a form field to get the title of the block in the used blocks list.
     *
     * By default this will be prefixed with getBlockTitle, you can disable that by returning true in shouldHidePrefix.
     */
    public static function getBlockTitleField(): ?string
    {
        return null;
    }

    /**
     * If the prefix should be hidden when using getBlockTitleField.
     */
    public static function shouldHidePrefix(): bool
    {
        return false;
    }

    public static function getBlockTitle(): string
    {
        return Str::replace('Block', '', Str::afterLast(static::class, '\\'));
    }

    public static function getBlockGroup(): string
    {
        return Str::slug(Str::before(static::class, '\\'));
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
