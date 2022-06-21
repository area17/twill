<?php

namespace A17\Twill\Models\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @property-read int $id
 */
interface TwillModelContract
{
    public function scopePublished(Builder $query): Builder;

    public function scopeAccessible(Builder $query): Builder;

    public function scopeOnlyTrashed(Builder $query): Builder;

    public function scopeDraft(Builder $query): Builder;

    public function getTranslatedAttributes(): array;
}
