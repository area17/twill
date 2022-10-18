<?php

namespace A17\Twill\Services\Breadcrumbs;

use Illuminate\Contracts\Support\Arrayable;

class BreadcrumbItem implements Arrayable
{
    public const ON_LISTING = 'on_listing';
    public const ON_FORM = 'on_form';

    private string $label;
    private ?string $url = null;
    private ?array $displayOn = null;

    public static function make(): self
    {
        return new self();
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function displayOnForm(): self
    {
        $this->displayOn[self::ON_FORM] = true;

        return $this;
    }

    public function displayOnListing(): self
    {
        $this->displayOn[self::ON_LISTING] = true;

        return $this;
    }

    public function shouldDisplayOnForm()
    {
        return $this->displayOn[self::ON_FORM] ?? false;
    }

    public function shouldDisplayOnListing()
    {
        return $this->displayOn[self::ON_LISTING] ?? false;
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'url' => $this->url,
        ];
    }
}
