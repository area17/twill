<?php

namespace A17\Twill\Models\Behaviors;

use Exception;
use Illuminate\Database\Eloquent\Builder;

trait HasPosition
{
    protected static function bootHasPosition()
    {
        static::creating(function ($model) {
            $model->setToLastPosition();
        });
    }

    protected function setToLastPosition()
    {
        $this->position = $this->getCurrentLastPosition() + 1;
    }

    protected function getCurrentLastPosition()
    {
        return (int) static::max("{$this->getTable()}.position");
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy("{$this->getTable()}.position");
    }

    /**
     * @param  array  $ids
     * @param  int  $startOrder
     * @return void
     */
    public static function setNewOrder($ids, $startOrder = 1)
    {
        if (! is_array($ids)) {
            throw new Exception('You must pass an array to setNewOrder');
        }

        foreach ($ids as $id) {
            $model = static::find($id);
            $model->position = $startOrder++;
            $model->save();
        }
    }
}
