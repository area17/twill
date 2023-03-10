<?php

namespace A17\Twill\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class Feature extends BaseModel
{
    protected $fillable = [
        'featured_id',
        'featured_type',
        'position',
        'bucket_key',
        'starred',
    ];

    public function featured(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForBucket(Builder $query, string $bucketKey): Builder
    {
        return $query->where('bucket_key', $bucketKey)->with('featured');
    }

    public static function getForBucket(string $bucketKey): Collection
    {
        return self::forBucket($bucketKey)->get()->map(function (Feature $feature) {
            return $feature->featured;
        })->filter();
    }

    public function getTable(): string
    {
        return config('twill.features_table', 'twill_features');
    }
}
