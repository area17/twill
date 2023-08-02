<?php

namespace A17\Docs;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

class BladeComponentRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        BladeComponentElement::assertInstanceOf($node);

        $renderedChildren = $childRenderer->renderNodes($node->children());

        $element = $node->getElement();
        $attributes = $node->getAttributeString();

        $blade = <<<BLADE
<x-twilldocs::$element$attributes>
###CONTENT###
</x-twilldocs::$element>
BLADE;

        return Str::replace('###CONTENT###', $renderedChildren, Blade::render($blade));
    }
}
