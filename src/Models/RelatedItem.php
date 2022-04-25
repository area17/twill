<?php

namespace A17\Twill\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class RelatedItem extends BaseModel
{
    /**
     * @var mixed[]
     */
    protected $guarded = [];

    protected $primaryKey;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var bool
     */
    public $timestamps = false;

    public function related(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo('related');
    }

    public function subject(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo('subject');
    }

    public function getTable()
    {
        return config('twill.related_table', 'twill_related');
    }
}
