<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Exceptions\ColumnMissingPropertyException;
use A17\Twill\Models\Model;
use A17\Twill\Services\Listings\TableColumn;

class Relation extends TableColumn
{
    private ?string $relation = null;

    public function relation(string $relation): self
    {
        $this->relation = $relation;
        return $this;
    }

    public function getRenderValue(Model $model): string
    {
        if (null === $this->relation) {
            throw new ColumnMissingPropertyException('Relation column missing relation value: ' . $this->field);
        }

        /** @var \Illuminate\Database\Eloquent\Builder $relation */
        $relation = $model->{$this->relation}();

        return $relation->pluck($this->field)->join(', ');
    }
}
