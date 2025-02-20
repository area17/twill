<?php

namespace A17\Twill\Models\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property \Carbon\Carbon $publish_start_date
 * @property \Carbon\Carbon $publish_end_date
 */
interface TwillSchedulableModel
{
    public function scopePublishedInListings(Builder $query): Builder;

    public function scopeVisible(Builder $query): Builder;

    public function setPublishStartDateAttribute($value): void;
}
