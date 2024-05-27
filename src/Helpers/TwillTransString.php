<?php

namespace A17\Twill\Helpers;

/**
 * TwillTransString is a wrapper around the trans function.
 *
 * This is made so that translations are resolved at render time rather than call time. This ensures
 * the correct language is being used regardless of where the string is introduced.
 */
class TwillTransString implements \Stringable, \JsonSerializable
{
    public function __construct(
        public string $key,
        public array $replace = []
    ) {
    }

    public function __toString()
    {
        $locale = config('twill.locale', config('twill.fallback_locale', 'en'));
        return (string)trans($this->key, $this->replace, $locale);
    }

    public static function __set_state(array $state)
    {
        return new self($state['key'], $state['replace']);
    }

    public function jsonSerialize(): string
    {
        return (string)$this;
    }
}
