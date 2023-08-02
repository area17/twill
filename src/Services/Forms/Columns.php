<?php

namespace A17\Twill\Services\Forms;

use A17\Twill\Services\Forms\Contracts\CanHaveSubfields;
use A17\Twill\Services\Forms\Contracts\CanRenderForBlocks;
use A17\Twill\Services\Forms\Traits\RenderForBlocks;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFacade;

class Columns implements CanHaveSubfields, CanRenderForBlocks
{
    use RenderForBlocks;

    protected function __construct(
        public ?Collection $left = null,
        public ?Collection $middle = null,
        public ?Collection $right = null,
    ) {
    }

    public static function make(): self
    {
        return new self();
    }

    public function left(array $fields): static
    {
        $this->left = collect($fields);

        return $this;
    }

    public function middle(array $fields): static
    {
        $this->middle = collect($fields);

        return $this;
    }

    public function right(array $fields): static
    {
        $this->right = collect($fields);

        return $this;
    }

    public function render(): View
    {
        if ($this->forBlocks()) {
            $this->left?->each(fn(CanRenderForBlocks $field) => $field->renderForBlocks());
            $this->right?->each(fn(CanRenderForBlocks $field) => $field->renderForBlocks());
            $this->middle?->each(fn(CanRenderForBlocks $field) => $field->renderForBlocks());
        }

        return ViewFacade::make('twill::partials.form.utils._columns', [
            'left' => null,
            'middle' => null,
            'right' => null,
            'leftFields' => $this->left,
            'middleFields' => $this->middle,
            'rightFields' => $this->right,
        ]);
    }

    public function registerDynamicRepeaters(): void
    {
        if ($this->left) {
            foreach ($this->left as $field) {
                if ($field instanceof InlineRepeater) {
                    $field->register();
                }
                if ($field instanceof CanHaveSubfields) {
                    $field->registerDynamicRepeaters();
                }
            }
        }

        if ($this->middle) {
            foreach ($this->middle as $field) {
                if ($field instanceof InlineRepeater) {
                    $field->register();
                }
                if ($field instanceof CanHaveSubfields) {
                    $field->registerDynamicRepeaters();
                }
            }
        }

        if ($this->right) {
            foreach ($this->right as $field) {
                if ($field instanceof InlineRepeater) {
                    $field->register();
                }
                if ($field instanceof CanHaveSubfields) {
                    $field->registerDynamicRepeaters();
                }
            }
        }
    }
}
