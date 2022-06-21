<?php

namespace A17\Twill\Models;

use Illuminate\Database\Eloquent\Builder;

interface ModelInterface
{
    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePublished(Builder $query): Builder;

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePublishedInListings(Builder $query): Builder;

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeVisible(Builder $query): Builder;

    /**
     * @param mixed $value
     *
     * @return void
     */
    public function setPublishStartDateAttribute($value): void;

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeDraft(Builder $query): Builder;

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeOnlyTrashed(Builder $query): Builder;

    /**
     * @return array
     */
    public function getTranslatedAttributes(): array;
}
