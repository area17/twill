<?php

namespace A17\Twill\Services\Breadcrumbs;

class Breadcrumbs
{
    /**
     * @var \A17\Twill\Services\Breadcrumbs\BreadcrumbItem[]
     */
    public array $items = [];

    final public function __construct()
    {
    }

    /**
     * @param \A17\Twill\Services\Breadcrumbs\BreadcrumbItem[] $items
     */
    public static function make(array $items = []): static
    {
        $instance = new static();
        $instance->items = $items;

        return $instance;
    }

    /**
     * @return \A17\Twill\Services\Breadcrumbs\BreadcrumbItem[]
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @return \A17\Twill\Services\Breadcrumbs\BreadcrumbItem[]
     */
    public function getListingBreadcrumbs(): array
    {
        return array_filter($this->toArray(), fn(BreadcrumbItem $item) => $item->shouldDisplayOnListing());
    }

    /**
     * @return \A17\Twill\Services\Breadcrumbs\BreadcrumbItem[]
     */
    public function getFormBreadcrumbs(): array
    {
        return array_filter($this->toArray(), fn(BreadcrumbItem $item) => $item->shouldDisplayOnForm());
    }
}
