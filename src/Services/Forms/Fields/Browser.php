<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\CanHaveButtonOnTop;
use A17\Twill\Services\Forms\Fields\Traits\HasFieldNote;
use A17\Twill\Services\Forms\Fields\Traits\HasMax;
use A17\Twill\Services\Forms\Fields\Traits\IsTranslatable;
use Illuminate\Support\Str;

class Browser extends BaseFormField
{
    use IsTranslatable;
    use HasMax;
    use HasFieldNote;
    use CanHaveButtonOnTop;

    protected ?string $moduleName = null;

    protected array $modules = [];

    protected array $endpoints = [];

    protected array $params = [];

    protected ?string $browserNote = null;

    protected ?string $itemLabel = null;

    protected bool $wide = false;

    protected bool $sortable = true;

    protected ?string $routePrefix = null;

    protected ?string $connectedBrowserField = null;

    public static function make(): static
    {
        $instance = new self(
            component: \A17\Twill\View\Components\Fields\Browser::class,
            mandatoryProperties: ['label']
        );
        $instance->max = 1;

        return $instance;
    }

    /**
     * Additional parameters to pass to the module route.
     */
    public function params(array $params): static
    {
        $this->params = $params;

        return $this;
    }

    /**
     * A list of custom endpoints to use.
     */
    public function endpoints(array $endpoints): static
    {
        $this->endpoints = $endpoints;

        return $this;
    }

    /**
     * A note to display inside the browser.
     */
    public function browserNote(string $browserNote): static
    {
        $this->browserNote = $browserNote;

        return $this;
    }

    /**
     * The label to display for items, defaults to the field label.
     */
    public function itemLabel(string $itemLabel): static
    {
        $this->itemLabel = $itemLabel;

        return $this;
    }

    /**
     * For more control over the modules.
     *
     * Cannot be used together with modules.
     *
     * Provide an array with: label, name, routePrefix, params
     */
    public function modulesCustom(array $modules): static
    {
        $this->modules = $modules;

        return $this;
    }

    /**
     * Makes the modal window use the full width.
     */
    public function wide(bool $wide = true): static
    {
        $this->wide = $wide;

        return $this;
    }

    /**
     * Makes the columns in the browser sortable.
     */
    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * Set a custom route prefix if needed.
     */
    public function routePrefix(string $routePrefix): static
    {
        $this->routePrefix = $routePrefix;

        return $this;
    }

    /**
     * Conditionally show this field based on another browser field.
     */
    public function connectedBrowserField(string $connectedBrowserField): static
    {
        $this->connectedBrowserField = $connectedBrowserField;

        return $this;
    }

    /**
     * A list of modules that can be be selected in the browser modal.
     */
    public function modules(array $modules): static
    {
        if (count($modules) === 1 && ! isset($modules[0])) {
            $this->moduleName = getModuleNameByModel(array_pop($modules));

            if (! $this->label) {
                $this->label = Str::headline($this->moduleName);
            }

            if (! $this->name) {
                $this->name = Str::snake($this->moduleName);
            }
        } else {
            foreach ($modules as $module) {
                if (isset($module['name'])) {
                    $this->modules[] = [
                        'name' => getModuleNameByModel($module['name']),
                        'label' => $module['label']
                    ];
                } else {
                    $this->modules[] = [
                        'name' => getModuleNameByModel($module),
                    ];
                }
            }
        }

        return $this;
    }

    protected function getAdditionalConstructorArguments(): array
    {
        if (! $this->name && ! $this->moduleName) {
            throw new \InvalidArgumentException(
                'Browser field is missing name field. Use ->name when using more than 1 module.'
            );
        }

        return ['name' => $this->name ?? $this->moduleName, 'endpoints' => $this->endpoints];
    }
}
