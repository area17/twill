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
        protected string $defaultSortDirection = 'ASC',
        protected bool $optional = false,
        protected bool $linkToEdit = false,
        protected bool $visible = true,
        protected bool $html = false,
        protected Closure|string|null $link = null,
        protected ?Closure $render = null,
        protected ?Closure $sortFunction = null,
        protected ?string $specificType = null,
        protected bool $shrink = false,
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

    public function shrink(bool $shrink = true): static
    {
        $this->shrink = $shrink;

        return $this;
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

    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Sets the title of the column.
     */
    public function title(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    /**
     * When enabled the column is sortable by clicking on the header.
     */
    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;
        return $this;
    }

    /**
     * When enabled this will be the default column the list is sorted by.
     */
    public function sortByDefault(bool $defaultSort = true, string $direction = 'ASC'): static
    {
        $this->defaultSort = $defaultSort;
        $this->defaultSortDirection = $direction;
        return $this;
    }

    public function isDefaultSort(): bool
    {
        return $this->defaultSort;
    }

    public function getDefaultSortDirection(): string
    {
        if (!in_array($this->defaultSortDirection, ['ASC', 'DESC', 'asc', 'desc'], true)) {
            throw new \Exception('Sort can only be ASC or DESC');
        }
        return $this->defaultSortDirection;
    }

    /**
     * Makes the column optional, when set it can be hidden using the gear icon above the listing.
     */
    public function optional(bool $optional = true): static
    {
        $this->optional = $optional;
        return $this;
    }

    /**
     * To be used with ->optional, but it will be hidden by default.
     */
    public function hide(bool $visible = false): static
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * When enabled the content will be rendered as html.
     */
    public function renderHtml(bool $html = true): static
    {
        $this->html = $html;
        return $this;
    }

    /**
     * Links the column content to a fixed url or url via the closure.
     */
    public function linkCell(Closure|string $link): static
    {
        $this->link = $link;
        return $this;
    }

    public function linkToEdit(bool $linkToEdit = true): static
    {
        $this->linkToEdit = $linkToEdit;

        return $this;
    }

    public function shouldLinkToEdit(): bool
    {
        return $this->linkToEdit;
    }

    /**
     * A separate sortKey if different from the field name.
     */
    public function sortKey(?string $sortKey): static
    {
        $this->sortKey = $sortKey;
        return $this;
    }

    public function getSortKey(): string
    {
        return $this->sortKey ?? $this->field;
    }

    /**
     * An optional closure accepting the QueryBuilder and sort direction to apply
     * when this field is being sorted.
     *
     * If you are using the Relation field, this sort is required to make it work.
     * Please note that when you are having a belongsToMany you have to carefully write your
     * join because otherwise you may end up with duplicate rows.
     */
    public function order(\Closure $sortFunction): static
    {
        $this->sortFunction = $sortFunction;
        return $this;
    }

    public function getOrderFunction(): ?\Closure
    {
        return $this->sortFunction;
    }

    /**
     * Set a custom render function that will receive the model as its function argument.
     *
     * You can use this to display for example a view or formatted date.
     */
    public function customRender(Closure $renderFunction): static
    {
        $this->render = $renderFunction;
        return $this;
    }

    public function toColumnArray(array $visibleColumns = [], bool $sortable = true): array
    {
        $visible = $this->visible;

        if (!empty($visibleColumns) && !in_array($this->key, $visibleColumns, true)) {
            $visible = false;
        }

        return [
            'name' => $this->getKey(),
            'label' => $this->title,
            'shrink' => $this->shrink,
            'visible' => $visible,
            'optional' => $this->optional,
            'sortable' => $sortable && $this->sortable,
            'html' => $this->html,
            // SpecificType corresponds to datatableRow isSpecificColumn to make sure we render the correct type.
            // For example, for images this should be 'thumbnail'
            'specificType' => $this->specificType ?? null,
        ];
    }

    public function renderCell(TwillModelContract $model): string
    {
        if ($link = $this->link) {
            if ($link instanceof Closure) {
                $link = $link($model, $this);
            }

            // Link via the closure can be null so we recheck it and only then use it.
            if ($link) {
                return trim(
                    view('twill::listings.columns.linked-cell', [
                        'slot' => $this->getRenderValue($model),
                        'isEditLink' => $this->linkToEdit,
                        'link' => $link,
                    ])
                );
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
