<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Exceptions\ColumnMissingPropertyException;
use A17\Twill\Services\Listings\TableColumn;
use Illuminate\Database\Eloquent\Model;
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

    /**
     * Set the relation that should be used.
     */
    public function relation(string $relation): self
    {
        $this->relation = $relation;
        return $this;
    }

    protected function getRenderValue(Model $model): string
    {
        if (null === $this->relation) {
            throw new ColumnMissingPropertyException('Relation column missing relation value: ' . $this->field);
        }

        // @todo: I feel this can be optimized.
        /** @var \Illuminate\Database\Eloquent\Collection $relation */
        $relation = $model->{$this->relation}()->get();

        return $relation->pluck($this->field)->join(', ');
    }
}
