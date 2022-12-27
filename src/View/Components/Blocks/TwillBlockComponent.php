<?php

namespace A17\Twill\View\Components\Blocks;

use A17\Twill\Models\Block;
use A17\Twill\Services\Forms\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

abstract class TwillBlockComponent extends Component
{
    public ?Block $block = null;

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
