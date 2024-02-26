<?php

namespace A17\Twill\Services\Breadcrumbs;

class Breadcrumbs
{
    /**
     * @var BreadcrumbItem[]
     */
    public array $items = [];

    final public function __construct()
    {
    }

    /**
     * @param  BreadcrumbItem[]  $items
     */
    public static function make(array $items = []): static
    {
        $instance = new static();
        $instance->items = $items;

        return $instance;
    }

    /**
     * @return BreadcrumbItem[]
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @return BreadcrumbItem[]
     */
    public function getListingBreadcrumbs(): array
    {
        return array_filter($this->toArray(), fn (BreadcrumbItem $item) => $item->shouldDisplayOnListing());
    }

    /**
     * @return BreadcrumbItem[]
     */
    public function getFormBreadcrumbs(): array
    {
        return array_filter($this->toArray(), fn (BreadcrumbItem $item) => $item->shouldDisplayOnForm());
    }
}
