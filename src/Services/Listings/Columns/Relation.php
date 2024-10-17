<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Exceptions\ColumnMissingPropertyException;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Listings\TableColumn;
use Illuminate\Support\Str;

class Relation extends TableColumn
{
    private ?string $relation = null;

    public function getKey(): string
    {
        if ($this->key === null) {
            throw new ColumnMissingPropertyException();
        }

        return $this->relation . Str::studly($this->field);
    }

    public function getSortKey(): string
    {
        if (null === $this->sortKey) {
            return $this->relation . ucfirst($this->field);
        }

        return $this->sortKey;
    }

    /**
     * Set the relation that should be used.
     */
    public function relation(string $relation): static
    {
        $this->relation = $relation;
        return $this;
    }

    protected function getRenderValue(TwillModelContract $model): string
    {
        if (null === $this->relation) {
            throw new ColumnMissingPropertyException('Relation column missing relation value: ' . $this->field);
        }

        /** @var \Illuminate\Database\Eloquent\Collection $relation */
        $model->loadMissing($this->relation);
        $relation = collect($model->getRelation($this->relation));

        return $relation->pluck($this->field)->join(', ');
    }
}
