<?php

namespace A17\Twill\Services\Forms;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Services\Blocks\Block;
use A17\Twill\Services\Forms\Contracts\CanHaveSubfields;
use A17\Twill\Services\Forms\Contracts\CanRenderForBlocks;
use A17\Twill\Services\Forms\Fields\Repeater;
use A17\Twill\Services\Forms\Traits\RenderForBlocks;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InlineRepeater implements CanHaveSubfields, CanRenderForBlocks
{
    use RenderForBlocks;

    protected function __construct(
        private ?string $name = null,
        private ?string $trigger = null,
        private ?string $selectTrigger = null,
        private ?Collection $fields = null,
        private ?string $label = null,
        private bool $allowCreate = true,
        private ?string $relation = null,
        private ?bool $allowBrowse = false,
        private ?array $browser = null,
    ) {
    }

    public function triggerText(string $trigger): static
    {
        $this->trigger = $trigger;

        return $this;
    }

    public function selectTriggerText(string $selectTrigger): static
    {
        $this->selectTrigger = $selectTrigger;

        return $this;
    }

    public static function make(): self
    {
        $self = new self();
        $self->fields = collect();
        return $self;
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
     * Only to be used when you are referring to other models. Not for json repeaters.
     */
    public function disableCreate(bool $disableCreate = true): static
    {
        $this->allowCreate = ! $disableCreate;

        return $this;
    }

    /**
     * The name of the module to use for selecting existing records. Not for json repeaters.
     */
    public function relation(string $relation): static
    {
        if (str_contains($relation, '\\')) {
            $relation = getModuleNameByModel($relation);
        }
        $this->relation = $relation;

        $this->browser = [
            'label' => Str::title($relation),
            'name' => $relation,
        ];

        return $this;
    }

    public function allowBrowser(bool $allowBrowse = true): static
    {
        $this->allowBrowse = $allowBrowse;
        return $this;
    }

    /**
     * Set the name of the repeater.
     *
     * NOTE: You cannot have the same repeater twice in a form or page.
     * NOTE: If you have a repeater with a this name already, that one will be used instead.
     */
    public function name(string $name): static
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
    public function fields(array $fields): static
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
        $repeaterBlock->compiled = false;
        $repeaterBlock->trigger = $this->trigger ?? 'Add ' . $this->label;
        $repeaterBlock->selectTrigger = $this->selectTrigger ?? 'Select ' . $this->label;
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
        $repeater = Repeater::make()
            ->name($this->name)
            ->type($this->getRenderName())
            ->allowCreate($this->allowCreate)
            ->relation($this->relation ?? null)
            ->browserModule($this->allowBrowse ? $this->browser : null);

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
