<?php

namespace A17\Twill\Models\Contracts;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property Carbon $publish_start_date
 * @property Carbon $publish_end_date
 */
interface TwillSchedulableModel
{
    public function scopePublishedInListings(Builder $query): Builder;

    public function scopeVisible(Builder $query): Builder;

    public function setPublishStartDateAttribute($value): void;
}
