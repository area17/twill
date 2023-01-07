<?php

namespace A17\Twill\Services\Forms;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Services\Blocks\Block;
use A17\Twill\Services\Forms\Fields\Repeater;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InlineRepeater implements CanHaveSubfields
{
    protected function __construct(
        public ?string $name = null,
        public ?Collection $fields = null,
        public ?string $label = null
    ) {
    }

    public static function make(): self
    {
        return new self();
    }

    /**
     * Set the label of the repeater.
     */
    public function label(string $label): static
    {
        $this->label = $label;

        if (! $this->name) {
            $this->name(Str::slug($label));
        }

        return $this;
    }

    /**
     * Set the name of the repeater.
     *
     * NOTE: You cannot have the same repeater twice in a form or page.
     * NOTE: If you have a repeater with a this name already, that one will be used instead.
     */
    public function name(string $name): self
    {
        $this->name = $name;

        if (! $this->label) {
            $this->label(Str::title($name));
        }

        return $this;
    }

    /**
     * Set the form fields of the repeater.
     */
    public function fields(array $fields): self
    {
        $this->fields = collect($fields);

        return $this;
    }

    public function renderForm(): View
    {
        return view('twill::partials.form.renderer.block_form', [
            'fields' => Form::make($this->fields)->renderForBlocks()
        ]);
    }

    public function getRenderName(): string
    {
        return 'dynamic-repeater-' . $this->name;
    }

    public function asBlock(): Block
    {
        $repeaterBlock = new Block(
            file: null,
            type: Block::TYPE_REPEATER,
            source: 'dynamic',
            name: $this->getRenderName(),
            inlineRepeater: $this
        );
        $repeaterBlock->title = $this->label;
        $repeaterBlock->component = $this->getRenderName();
        $repeaterBlock->selectTrigger = $this->label;
        $repeaterBlock->compiled = false;
        $repeaterBlock->trigger = $this->label;
        $repeaterBlock->group = 'dynamic';

        return $repeaterBlock;
    }

    /**
     * Not to be called manually. This will register the dynamic repeater.
     */
    public function register(): void
    {
        TwillBlocks::registerDynamicRepeater($this->name, $this);
    }

    public function render(): View
    {
        $repeater = Repeater::make()->name($this->name)->type($this->getRenderName());
        $repeater->renderForBlocks = $this->renderForBlocks ?? false;
        return $repeater->render();
    }

    public function registerDynamicRepeaters(): void
    {
        foreach ($this->fields as $field) {
            if ($field instanceof self) {
                $field->register();
            }
            if ($field instanceof CanHaveSubfields) {
                $field->registerDynamicRepeaters();
            }
        }
    }
}
