<?php

namespace A17\Twill\View\Components;

use Illuminate\Support\Str;

class Browser extends TwillFormComponent
{
    public $moduleName;
    public $modules;
    public $endpoints;
    public $max;
    public $note;
    public $fieldNote;
    public $browserNote;
    public $itemLabel;
    public $buttonOnTop;
    public $wide;
    public $sortable;
    public $endpoint;
    public $routePrefix;
    public $params;

    public function __construct(
        $label,
        $name = null,
        $renderForBlocks = false,
        $renderForModal = false,
        $moduleName = null,
        $modules = [],
        $endpoints = [],
        $endpoint = null,
        $max = 1,
        $note = null,
        $fieldNote = null,
        $browserNote = null,
        $itemLabel = null,
        $buttonOnTop = false,
        $wide = false,
        $sortable = true,
        $routePrefix = null,
        $params = []
    ) {
        $name = $name ?? $moduleName;
        parent::__construct($name, $label, $renderForBlocks, $renderForModal);
        $this->name = $name;
        $this->moduleName = $moduleName;
        $this->modules = $modules;
        $this->endpoints = $endpoints;
        $this->endpoint = $endpoint;
        $this->max = $max;
        $this->note = $note;
        $this->fieldNote = $fieldNote;
        $this->browserNote = $browserNote;
        $this->itemLabel = $itemLabel;
        $this->buttonOnTop = $buttonOnTop;
        $this->wide = $wide;
        $this->sortable = $sortable;
        $this->routePrefix = $routePrefix;
        $this->params = $params;

        $endpointsFromModules = isset($this->modules) ? collect($this->modules)->map(function ($module) {
            return [
                'label' => $module['label'] ?? ucfirst($module['name']),
                'value' => moduleRoute(
                    $module['name'],
                    $module['routePrefix'] ?? null,
                    'browser',
                    $module['params'] ?? [],
                    false
                ),
            ];
        })->toArray() : null;

        $this->endpoints = $this->endpoints ?? $endpointsFromModules ?? [];
        $this->endpoint = $this->endpoint ?? (!empty($endpoints) ? null : moduleRoute(
                $this->moduleName,
                $this->routePrefix,
                'browser',
                $this->params,
                false
            ));

        $this->itemLabel = $this->itemLabel ?? strtolower($this->label);

        $this->note = $this->note ??
            'Add' . ($this->max > 1 ? " up to {$this->max} " . $itemLabel : ' one ' . Str::singular($this->itemLabel));
    }

    public function render()
    {
        return view('twill::partials.form._browser');
    }
}
