<?php

namespace A17\Twill\Models\Behaviors;

trait HasPosition
{

    protected static function bootHasPosition()
    {
        static::creating(function ($model): void {
            $model->setToLastPosition();
        });
    }

    protected function setToLastPosition()
    {
        $this->position = $this->getCurrentLastPosition() + 1;
    }

    protected function getCurrentLastPosition()
    {
        return ((int) static::max(sprintf('%s.position', $this->getTable())));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->orderBy(sprintf('%s.position', $this->getTable()));
    }

    /**
     * @return void
     * @param mixed[] $ids
     */
    public static function setNewOrder(array $ids, int $startOrder = 1)
    {
        if (!is_array($ids)) {
            throw new \Exception('You must pass an array to setNewOrder');
        }

        foreach ($ids as $id) {
            $model = static::find($id);
            $model->position = $startOrder++;
            $model->save();
        }
    }
}
