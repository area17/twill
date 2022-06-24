<?php

namespace A17\Twill\Services\Listings;

use A17\Twill\Exceptions\ColumnMissingPropertyException;
use A17\Twill\Models\Contracts\TwillModelContract;
use Closure;
use Illuminate\Support\Str;

abstract class TableColumn
{
    final protected function __construct(
        protected ?string $key = null,
        protected ?string $field = null,
        protected ?string $title = null,
        protected ?string $sortKey = null,
        protected bool $sortable = false,
        protected bool $defaultSort = false,
        protected bool $optional = false,
        protected bool $visible = true,
        protected bool $html = false,
        protected Closure|string|null $link = null,
        protected ?Closure $render = null
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

    /**
     * Set the field name to be used for this column. This is the field that will be used to query the database.
     *
     * If no title is set, it will also update the title.
     */
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

    /**
     * Sets the title of the column.
     */
    public function title(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * When enabled the column is sortable by clicking on the header.
     */
    public function sortable(bool $sortable = true): self
    {
        $this->sortable = $sortable;
        return $this;
    }

    /**
     * When enabled this will be the default column the list is sorted by.
     */
    public function sortByDefault(bool $defaultSort = true): self
    {
        $this->defaultSort = $defaultSort;
        return $this;
    }

    public function isDefaultSort(): bool {
        return $this->defaultSort;
    }

    /**
     * Makes the column optional, when set it can be hidden using the gear icon above the listing.
     */
    public function optional(bool $optional = true): self
    {
        $this->optional = $optional;
        return $this;
    }

    /**
     * To be used with ->optional, but it will be hidden by default.
     */
    public function hide(bool $visible = false): self
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * When enabled the content will be rendered as html.
     */
    public function renderHtml(bool $html = true): self
    {
        $this->html = $html;
        return $this;
    }

    /**
     * Links the column content to a fixed url or url via the closure.
     */
    public function linkCell(Closure|string $link): self
    {
        $this->link = $link;
        return $this;
    }

    /**
     * A separate sortKey if different from the field name.
     */
    public function sortKey(?string $sortKey): self {
        $this->sortKey = $sortKey;
        return $this;
    }

    public function getSortKey(): ?string {
        return  $this->sortKey;
    }

    /**
     * Set a custom render function that will receive the model as its function argument.
     *
     * You can use this to display for example a view or formatted date.
     */
    public function customRender(Closure $renderFunction): self
    {
        $this->render = $renderFunction;
        return $this;
    }

    public function toColumnArray(array $visibleColumns = [], bool $sortable = true): array
    {
        $visible = $this->visible;

        if ($this->optional && (empty($visibleColumns) || in_array($this->key, $visibleColumns, true))) {
            $visible = false;
        }

        return [
            'name' => $this->getKey(),
            'label' => $this->title,
            'visible' => $visible,
            'optional' => $this->optional,
            'sortable' => $sortable && $this->sortable,
            'html' => $this->html,
        ];
    }

    public function renderCell(TwillModelContract $model): string
    {
        if ($link = $this->link) {
            if ($link instanceof Closure) {
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

    protected function getRenderValue(TwillModelContract $model): int|bool|string
    {
        if (($renderFunction = $this->render) !== null) {
            return $renderFunction($model);
        }

        return $model->{$this->field} ?? '';
    }

}
