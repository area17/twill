<?php

namespace A17\Twill\Services\Forms\Fields;

use A17\Twill\Services\Forms\Fields\Traits\hasFieldNote;
use A17\Twill\Services\Forms\Fields\Traits\hasMax;
use A17\Twill\Services\Forms\Fields\Traits\isTranslatable;
use Illuminate\Support\Str;

class Browser extends BaseFormField
{
    use isTranslatable;
    use hasMax;
    use hasFieldNote;

    protected ?string $moduleName = null;
    protected array $modules = [];
    protected array $endpoints = [];
    protected array $params = [];
    protected ?string $browserNote = null;
    protected ?string $itemLabel = null;
    protected bool $buttonOnTop = false;
    protected bool $wide = false;
    protected bool $sortable = false;
    protected ?string $routePrefix = null;
    protected ?string $connectedBrowserField = null;

    public static function make(): static
    {
        $instance = new self(
            component: \A17\Twill\View\Components\Browser::class,
            mandatoryProperties: ['label']
        );
        $instance->max = 1;

        return $instance;
    }

    public function params(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function endpoints(array $endpoints): self
    {
        $this->endpoints = $endpoints;

        return $this;
    }

    public function browserNote(string $browserNote): self
    {
        $this->browserNote = $browserNote;

        return $this;
    }

    public function itemLabel(string $itemLabel): self
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
    public function modulesCustom(array $modules): self
    {
        $this->modules = $modules;
        return $this;
    }

    public function buttonOnTop(bool $buttonOnTop = true): self
    {
        $this->buttonOnTop = $buttonOnTop;

        return $this;
    }

    public function wide(bool $wide = true): self
    {
        $this->wide = $wide;

        return $this;
    }

    public function sortable(bool $sortable = true): self
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function routePrefix(string $routePrefix): self
    {
        $this->routePrefix = $routePrefix;

        return $this;
    }

    public function connectedBrowserField(string $connectedBrowserField): self
    {
        $this->connectedBrowserField = $connectedBrowserField;

        return $this;
    }

    public function modules(array $modules): self
    {
        if (count($modules) === 1) {
            $this->moduleName = getModuleNameByModel(array_pop($modules));

            if (!$this->label) {
                $this->label = Str::headline($this->moduleName);
            }

            if (!$this->name) {
                $this->name = Str::snake($this->moduleName);
            }
        } else {
            foreach ($modules as $module) {
                $this->modules[] = [
                    'name' => getModuleNameByModel($module),
                ];
            }
        }

        return $this;
    }

    protected function getAdditionalConstructorArguments(): array
    {
        if (!$this->name && !$this->moduleName) {
            throw new \InvalidArgumentException(
                'Browser field is missing name field. Use ->name when using more than 1 module.'
            );
        }

        return ['name' => $this->name ?? $this->moduleName];
    }

}
