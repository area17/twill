<?php

namespace A17\Twill\Services\Forms;

use A17\Twill\Models\Contracts\TwillModelContract;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;

class Form extends Collection
{
    public ?Fieldsets $fieldsets = null;

    private TwillModelContract $item;
    private ?Form $sideForm = null;

    public function withFieldSets(Fieldsets $fieldsets): self
    {
        $this->fieldsets = $fieldsets;

        return $this;
    }

    public function addFieldset(Fieldset $fieldset): self
    {
        if (!$this->fieldsets) {
            $this->fieldsets = Fieldsets::make();
        }

        $this->fieldsets->add($fieldset);

        return $this;
    }

    public function toFrontend(TwillModelContract $item, ?Form $sideFieldSets = null): self
    {
        $this->item = $item;
        $this->sideForm = $sideFieldSets;

        return $this;
    }

    public function getAdditionalFieldsets(): array
    {
        return $this->fieldsets->map(fn($fieldset): array => [
            'fieldset' => $fieldset->id,
            'label' => $fieldset->title,
        ])->toArray();
    }

    public function formToRenderArray(): array
    {
        $viewWithData = [];

        if ($this->fieldsets) {
            $viewWithData['renderFieldsets'] = $this->fieldsets;
            $viewWithData['additionalFieldsets'] = $this->fieldsets->map(fn($fieldset): array => [
                'fieldset' => $fieldset->id,
                'label' => $fieldset->title,
            ])->toArray();
        }

        $viewWithData['disableContentFieldset'] = $this->isEmpty();

        $viewWithData['renderFields'] = $this;

        return $viewWithData;
    }

    public function hasForm(): bool
    {
        return $this->isNotEmpty() || ($this->fieldsets && $this->fieldsets->isNotEmpty());
    }

    public function hasSideForm(): bool
    {
        return $this->sideForm && $this->sideForm->isNotEmpty();
    }

    public function renderBaseForm(): View
    {
        return view('twill::partials.form.renderer.base_form', $this->formToRenderArray());
    }

    public function renderSideForm(): View
    {
        return view('twill::partials.form.renderer.base_form', $this->sideForm->formToRenderArray());
    }

}
