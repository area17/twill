<?php

namespace A17\Twill\Services\Forms;

use A17\Twill\Services\Forms\Contracts\CanHaveSubfields;
use A17\Twill\Services\Forms\Traits\HasSubFields;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class Form extends Collection implements CanHaveSubfields
{
    use HasSubFields;

    public ?Fieldsets $fieldsets = null;

    private ?Form $sideForm = null;
    private bool $isCreate = false;
    private bool $forBlocks = false;

    public function withFieldSets(Fieldsets $fieldsets): static
    {
        $this->fieldsets = $fieldsets;

        return $this;
    }

    public function addFieldset(Fieldset $fieldset): static
    {
        if (! $this->fieldsets) {
            $this->fieldsets = Fieldsets::make();
        }

        $this->fieldsets->add($fieldset);

        return $this;
    }

    public function toFrontend(?Form $sideFieldSets = null, bool $isCreate = false): static
    {
        $this->sideForm = $sideFieldSets;

        $this->isCreate = $isCreate;

        return $this;
    }

    public function getAdditionalFieldsets(): array
    {
        if (! $this->fieldsets) {
            return [];
        }

        return $this->fieldsets->map(fn($fieldset): array => [
            'fieldset' => $fieldset->id,
            'label' => $fieldset->title,
        ])->toArray();
    }

    public function hasFieldsInBaseFieldset(): bool
    {
        return ! $this->isEmpty();
    }

    public function formToRenderArray(): array
    {
        $viewWithData = ['isCreate' => $this->isCreate];

        if ($this->fieldsets) {
            $viewWithData['renderFieldsets'] = $this->fieldsets;
            $viewWithData['additionalFieldsets'] = $this->fieldsets->map(fn($fieldset): array => [
                'fieldset' => $fieldset->id,
                'label' => $fieldset->title,
            ])->toArray();
        }

        $viewWithData['disableContentFieldset'] = ! $this->hasFieldsInBaseFieldset();

        $viewWithData['renderFields'] = $this;

        return $viewWithData;
    }

    public function forBlocks(): bool
    {
        return $this->forBlocks;
    }

    public function renderForBlocks(bool $renderForBlocks = true): static
    {
        $this->forBlocks = $renderForBlocks;

        return $this;
    }

    public function hasForm(): bool
    {
        return $this->isNotEmpty() || ($this->fieldsets && $this->fieldsets->isNotEmpty());
    }

    public function hasSideForm(): bool
    {
        return $this->sideForm && ($this->sideForm->isNotEmpty() || ($this->sideForm->fieldsets && $this->sideForm->fieldsets->isNotEmpty()));
    }

    public function renderBaseForm(): View
    {
        return view('twill::partials.form.renderer.base_form', $this->formToRenderArray());
    }

    public function renderSideForm(): View
    {
        return view('twill::partials.form.renderer.base_form', $this->sideForm->formToRenderArray());
    }

    public function registerDynamicRepeaters(): void
    {
        $this->registerDynamicRepeatersFor($this);
        $this->fieldsets?->registerDynamicRepeaters();
    }
}
