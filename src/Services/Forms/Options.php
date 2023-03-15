<?php

namespace A17\Twill\Services\Forms;

use Illuminate\Support\Collection;

/**
 * @implements Collection<int, Options>
 */
class Options extends Collection
{
    /**
     * Create a new options collection from the given array.
     *
     * This method accepts both ['value' => 'label'] and Option objects.
     */
    public static function fromArray(array $options): static
    {
        return static::make(collect($options)->map(function ($key, $value) {
            if ($value instanceof Option) {
                return $value;
            }

            return is_array($value)
                ? Option::make(...$value)
                : Option::make($key, $value);
        }));
    }
}
