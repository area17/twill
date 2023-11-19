<?php

namespace A17\Twill\Services\Settings;

use A17\Twill\Models\AppSetting;
use Closure;
use Illuminate\Support\Str;

class SettingsGroup
{
    private bool $booted = false;

    private string $name;

    private string $label;

    private bool $doNotAutoRegisterMenu = false;

    private ?Closure $availableWhen = null;

    private AppSetting $appSetting;

    public static function make(): self
    {
        return new self();
    }

    public function name(string $name): self
    {
        if (!isset($this->label)) {
            $this->label = Str::title($name);
        }

        $this->name = Str::slug($name);

        return $this;
    }

    public function getName(): string
    {
        return Str::slug($this->name);
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getRoute(): string
    {
        return 'twill.app.settings.page';
    }

    public function getHref(): string
    {
        return route($this->getRoute(), ['group' => $this->name]);
    }

    public function hasSection(string $sectionName): bool
    {
        return collect($this->getSettingsModel()->getFormBlocks())->first(function (string $name) use ($sectionName) {
                return $name === $sectionName;
        }) !== null;
    }

    public function getSettingsModel(): AppSetting
    {
        return $this->appSetting ??= AppSetting::firstOrCreate([
            'name' => $this->getName(),
        ]);
    }

    /**
     * There is no need to manually call this method.
     */
    public function boot(): void
    {
        if (!$this->booted) {
            $this->booted = true;

            $this->ensureModelExists();
        }
    }

    public function availableWhen(Closure $closure): self
    {
        $this->availableWhen = $closure;

        return $this;
    }

    public function isAvailable(): bool
    {
        if ($closure = $this->availableWhen) {
            return $closure();
        }

        return true;
    }

    protected function ensureModelExists(): void
    {
        $settingsModel = $this->getSettingsModel();
        // Ensure all the base blocks are there.
        foreach ($settingsModel->getFormBlocks() as $name) {
            $this->createBlockIfNotExisting($name, $settingsModel);
        }
    }

    protected function createBlockIfNotExisting(string $name, AppSetting $model): void
    {
        if (!$model->blocks()->where('editor_name', '=', $name)->exists()) {
            $model->blocks()->create([
                'editor_name' => $name,
                'type' => 'appSettings.' . $this->getName() . '.' . $name,
                'content' => [],
                'position' => 1,
            ]);
        }
    }

    /**
     * When this is set you will have to manually register the menu item in twill-navigation.php
     * otherwise this will be automatically embedded into the settings section in the main navigation.
     */
    public function doNotAutoRegisterMenu(bool $doNotAutoRegister = true): self
    {
        $this->doNotAutoRegisterMenu = $doNotAutoRegister;

        return $this;
    }

    public function shouldNotAutoRegisterInMenu(): bool
    {
        return $this->doNotAutoRegisterMenu;
    }
}
