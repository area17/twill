<?php

namespace A17\Twill\Models\Behaviors;

trait IsTranslatable
{
    /**
     * @param array
     */
    public $translatedAttributes = [];

    /**
     * Checks if this model is translatable.
     *
     * If no columns/column is provided it will return true if the model itself is translatable.
     */
    public function isTranslatable(null|array|string $columns = null): bool
    {
        // Model must have the trait
        if (! classHasTrait($this, HasTranslation::class)) {
            return false;
        }

        // Model must have the translatedAttributes property
        if (! property_exists($this, 'translatedAttributes')) {
            return false;
        }

        // If it's a check on certain columns
        // They must be present in the translatedAttributes
        if (filled($columns)) {
            return collect($this->getTranslatedAttributes())
                ->intersect(collect($columns))
                ->isNotEmpty();
        }

        // The translatedAttributes property must be filled
        return collect($this->getTranslatedAttributes())->isNotEmpty();
    }
}
