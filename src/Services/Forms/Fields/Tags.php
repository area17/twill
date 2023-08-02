<?php

namespace A17\Twill\Services\Forms\Fields;

class Tags extends BaseFormField
{
    public static function make(): static
    {
        $instance = new self(\A17\Twill\View\Components\Fields\Tags::class);
        $instance->name('tags');

        return $instance;
    }
}
