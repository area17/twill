<?php

namespace A17\Twill\Services\Listings;

use A17\Twill\Exceptions\ColumnMissingPropertyException;
use A17\Twill\Models\Model;
use Illuminate\Support\Str;

abstract class TableColumn
{
    protected function __construct(
        protected ?string $key = null,
        protected ?string $field = null,
        protected ?string $title = null,
        protected bool $sortable = false,
        protected bool $defaultSort = false,
        protected bool $optional = false,
        protected bool $visible = true,
        protected bool $html = false,
        protected \Closure|string|null $link = null,
        protected ?\Closure $render = null
    ) {
    }

    public static function make(): static
    {
        return new static();
    }

    public function getKey(): string
    {
        if ($this->key === null) {
            throw new ColumnMissingPropertyException();
        }
        return $this->key;
    }

    public function field(string $field): static
    {
        $this->field = $field;
        if (!$this->key) {
            $this->key = $field;
        }
        if (!$this->title) {
            $this->title = Str::headline($field);
        }
        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function sortable(bool $sortable = true): self
    {
        $this->sortable = $sortable;
        return $this;
    }

    public function sortByDefault(bool $defaultSort = true): self
    {
        $this->defaultSort = $defaultSort;
        return $this;
    }

    public function optional(bool $optional = true): self
    {
        $this->optional = $optional;
        return $this;
    }

    public function hide(bool $visible = false): self
    {
        $this->visible = $visible;
        return $this;
    }

    public function renderHtml(bool $html = true): self
    {
        $this->html = $html;
        return $this;
    }

    public function linkCell(\Closure|string $link): self
    {
        $this->link = $link;
        return $this;
    }

    public function customRender(\Closure $renderFunction): self
    {
        $this->render = $renderFunction;
        return $this;
    }

    public function toColumnArray(array $visibleColumns = [], bool $sortable = true): array
    {
        $visible = true;

        if ($this->optional && (empty($visibleColumns) || in_array($this->key, $visibleColumns, true))) {
            $visible = false;
        }

        return [
            'name' => $this->key,
            'label' => $this->title,
            'visible' => $visible,
            'optional' => $this->optional,
            'sortable' => $sortable && $this->sortable,
            'html' => $this->html,
        ];
    }

    public function renderCell(Model $model): string
    {
        if ($link = $this->link) {
            if ($link instanceof \Closure) {
                $link = $link($model);
            }
            // Link via the closure can be null so we recheck it and only then use it.
            if ($link) {
                return view('twill::listings.columns.linked-cell', [
                    'slot' => $this->getRenderValue($model),
                    'link' => $link,
                ]);
            }
        }
        return $this->getRenderValue($model);
    }

    protected function getRenderValue(Model $model): string
    {
        if ($renderFunction = $this->render) {
            return $renderFunction($model);
        }

        return $model->{$this->field};
    }

}
