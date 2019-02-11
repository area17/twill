<?php

namespace A17\Twill\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Feature extends BaseModel
{
    protected $fillable = [
        'featured_id',
        'featured_type',
        'position',
        'bucket_key',
        'starred',
    ];

    public function featured()
    {
        return $this->morphTo();
    }

    public function scopeForBucket($query, $bucketKey)
    {
        return $query->where('bucket_key', $bucketKey)->get()->map(function ($feature) {
            return $feature->featured;
        })->filter();
    }

}
