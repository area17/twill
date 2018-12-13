<?php

namespace A17\Twill\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class RelatedItem extends BaseModel
{
    protected $guarded = [];

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    public function related()
    {
        return $this->morphTo('related');
    }

    public function subject()
    {
        return $this->morphTo('subject');
    }

    public function getTable()
    {
        return 'related';
    }
}
