<?php

namespace A17\Twill\Services\Settings;

use Illuminate\Support\Str;

/**
 * @todo: Add permissions.
 */
class SettingsGroup
{
    private string $name;

    private string $label;

    private ?string $description = null;

    private bool $doNotAutoRegisterMenu = false;

    public static function make(): self
    {
        return new self();
    }

    public function name(string $name): self
    {
        if (! isset($this->label)) {
            $this->label = Str::title($name);
        }

        $this->name = Str::slug($name);

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function getDescription(): ?string
    {
        return $this->description;
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
