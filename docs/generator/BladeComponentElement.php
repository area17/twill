<?php

namespace A17\Docs;

use Illuminate\Support\Str;
use League\CommonMark\Node\Block\AbstractBlock;

class BladeComponentElement extends AbstractBlock
{
    private string $element;
    private array $properties;

    public function __construct(string $className, array $properties = [])
    {
        parent::__construct();

        $this->element = $className;
        $this->properties = $properties;
    }

    public function getElement(): string
    {
        return $this->element;
    }

    public function getAttributeString(): string
    {
        $string = '';
        foreach ($this->properties as $key => $value) {
            $key = Str::snake($key, '-');

            if (is_array($value)) {
                $value = var_export($value, true);
                $string .= " :$key=\"$value\"";
            } else {
                $string .= " $key=\"$value\"";
            }
        }

        return $string;
    }
}
