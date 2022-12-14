<?php

namespace A17\Twill\Services\Forms;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;

class BladePartial
{
    protected function __construct(
        protected ?string $view = null,
    ) {
    }

    public static function make(): self
    {
        return new self();
    }

    public function view(string $view): static
    {
        $this->view = $view;

        return $this;
    }

    public function render(): ?View
    {
        if (! $this->view) {
            return null;
        }

        $form = ViewFacade::shared("form");

        return ViewFacade::make($this->view, [
            'item' => $form['item'] ?? null,
            'form_fields' => $form['form_fields'] ?? [],
            'formModuleName' => $form['moduleName'] ?? null,
            'routePrefix' => $form['routePrefix'] ?? null,
        ]);
    }
}
