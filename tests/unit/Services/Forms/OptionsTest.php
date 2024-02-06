<?php

namespace A17\Twill\Tests\Unit\Services\Forms;

use A17\Twill\Services\Forms\Option;
use A17\Twill\Services\Forms\Options;
use A17\Twill\Tests\Unit\TestCase;

class OptionsTest extends TestCase
{
    public static function inputs(): array
    {
        return [
            'array of objects' => [
                [
                    Option::make('foo', 'Foo label'),
                    Option::make('bar', 'Bar label', false),
                ],
                false,
            ],

            'array of key-value pairs' => [
                [
                    'foo' => 'Foo label',
                    'bar' => 'Bar label',
                ],
                true,
            ],

            'array of arrays' => [
                [
                    [
                        'value' => 'foo',
                        'label' => 'Foo label',
                    ],
                    [
                        'value' => 'bar',
                        'label' => 'Bar label',
                        'selectable' => false,
                    ],
                ],
                false,
            ],
        ];
    }

    /**
     * @dataProvider inputs
     */
    public function testFromArray(array $options, bool $barShouldBeSelectable): void
    {
        $options = Options::fromArray($options);

        $this->assertEquals([
            [
                'value' => 'foo',
                'label' => 'Foo label',
                'selectable' => true,
            ],
            [
                'value' => 'bar',
                'label' => 'Bar label',
                'selectable' => $barShouldBeSelectable,
            ],
        ], $options->toArray());
    }
}
