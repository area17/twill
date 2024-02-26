<?php

namespace A17\Twill\Models\Contracts;

use A17\Twill\Models\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @mixin Model
 *
 * @property int $id
 *
 * @todo: Check if we can make it mixin some eloquent methods as well?
 */
interface TwillModelContract
{
    public function scopePublished(Builder $query): Builder;

    public function scopeAccessible(Builder $query): Builder;

    public function scopeOnlyTrashed(Builder $query): Builder;

    public function scopeDraft(Builder $query): Builder;

    public function getTranslatedAttributes(): array;
}
