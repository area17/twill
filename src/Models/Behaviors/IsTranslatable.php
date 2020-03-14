<?php

namespace A17\Twill\Models\Behaviors;

trait IsTranslatable
{
    public function isTranslatable($columns = null)
    {
        // Model must have the trait
        if (!classHasTrait($this, 'A17\Twill\Models\Behaviors\HasTranslation')) {
            return false;
        }

        // Model must have the translatedAttributes property
        if (!property_exists($this, 'translatedAttributes')) {
            return false;
        }

        // If it's a check on certain columns
        // They must be present in the translatedAttributes
        if (filled($columns)) {
            return collect($this->translatedAttributes)
                ->intersect(collect($columns))
                ->isNotEmpty();
        }

        // The translatedAttributes property must be filled
        return collect($this->translatedAttributes)->isNotEmpty();
    }
}
