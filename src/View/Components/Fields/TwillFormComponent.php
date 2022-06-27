<?php

namespace A17\Twill\View\Components\Fields;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\Component;

/**
 * The constructor of our components does grow quite large, but the benefit of this is that it is clear what is
 * in, needs to be in, and it provides autocomplete on modern code editors.
 */
abstract class TwillFormComponent extends Component
{
    public ?Model $item;

    public array $form_fields;

    public ?string $formModuleName;

    public ?string $routePrefix;

    public function __construct(
        public string $name,
        public string $label,
        public ?string $note = '',
        public bool $inModal = false,
        public bool $readOnly = false,
        public bool $renderForBlocks = false,
        public bool $renderForModal = false,
        public bool $disabled = false,
        public bool $required = false,
        public bool $translated = false,
        public mixed $default = null
    ) {
        // This can be null. In that case the field might be used outside of a form and we have no shared $form.
        $form = ViewFacade::shared("form");
        $this->item = $form['item'] ?? null;
        $this->form_fields = $form['form_fields'] ?? [];
        $this->formModuleName = $form['moduleName'] ?? null;
        $this->routePrefix = $form['routePrefix'] ?? null;

        $shared = ViewFacade::shared('TwillUntilConsumed', []);
        foreach ($shared as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function formFieldName(bool $asAttributes = false, ?string $customName = null): string
    {
        $name = $customName ?? $this->name;
        if ($this->renderForBlocks) {
            if ($asAttributes) {
                return "name: fieldName('$name')";
            }

            return ":name=\"fieldName('$name')\"";
        }

        if ($asAttributes) {
            return "name: '$name'";
        }

        return "name=\"$name\"";
    }

    abstract public function render(): View;
}
