<?php

namespace A17\Twill\Services\Breadcrumbs;

use Illuminate\Support\Str;

class NestedBreadcrumbs extends Breadcrumbs
{
    private ?string $parentLabel = null;
    private string $parentModule;
    private string $module;
    private string $parentRepository;
    private int $activeParentId;
    private string $titleKey;
    private string $label;

    public function parentLabel(string $parentLabel): self
    {
        $this->parentLabel = $parentLabel;

        return $this;
    }

    public function forParent(
        string $parentModule,
        string $module,
        int $activeParentId,
        string $repository,
        ?string $titleKey = 'title'
    ): self {
        $this->module = $module;
        $this->parentModule = $parentModule;
        $this->parentRepository = $repository;
        $this->activeParentId = $activeParentId;
        $this->titleKey = $titleKey;

        if (!$this->parentLabel) {
            $this->parentLabel(Str::title($parentModule));
        }

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    private function getActiveParentTitle(): string
    {
        return once(function () {
            return app($this->parentRepository)->getById($this->activeParentId)->{$this->titleKey};
        });
    }

    public function toArray(): array
    {
        return [
            BreadcrumbItem::make()->label($this->parentLabel)
                ->displayOnForm()
                ->displayOnListing()
                ->url(moduleRoute($this->parentModule, '', 'index')),
            BreadcrumbItem::make()->label($this->getActiveParentTitle())
                ->displayOnForm()
                ->displayOnListing()
                ->url(moduleRoute($this->parentModule, '', 'edit', $this->activeParentId)),
            BreadcrumbItem::make()->label($this->label)
                ->displayOnListing(),
            BreadcrumbItem::make()->label($this->label)
                ->displayOnForm()
                ->url(moduleRoute($this->module, '', 'index')),
            BreadcrumbItem::make()->label('Edit')
                ->displayOnForm(),
        ];
    }
}
