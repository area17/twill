<?php

namespace A17\Twill\Models\Behaviors;

trait HasDates
{
    /**
     * Get the attributes that should be converted to dates.
     *
     * @return array
     */
    public function getDates()
    {
        $dates = parent::getDates();

        // If it's not Laravel 10, stick to the current implementation
        if(app()->version() < '10') {
            return $dates;
        }

        // If we have a $dates property, get the dates from it
        if (property_exists($this, 'dates')) {
            $dates = array_merge($dates, $this->dates);
        }

        // If we don't have a $casts or it's empty, return the dates we have now
        if (!property_exists($this, 'casts') || $this->casts === []) {
            return $dates;
        }

        // Filter the ones that are casted as dates
        $casts = array_filter($this->casts, function($k) {
            return $k === 'date' || $k === 'datetime';
        });

        // Merge the key with our dates
        $dates = array_merge($dates, array_keys($casts));

        // Don't return duplicates
        return array_unique(array_values($dates));
    }
}
