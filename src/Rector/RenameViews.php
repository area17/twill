<?php

namespace A17\Twill\Rector;

use Illuminate\Support\Str;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameViews extends LaravelAwareRectorRule
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change usages of admin. views',
            [
                new CodeSample(
                    'view("admin.blocks.text");',
                    'view("twill.blocks.text");'
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
            Node\Expr\StaticCall::class,
        ];
    }

    /**
     * @param Node\Expr\FuncCall|Node\Expr\StaticCall $node
     */
    public function refactor(Node $node)
    {
        if (
            $node instanceof Node\Expr\StaticCall
            && ($node->class->getLast() !== 'View' || $node->name->name !== 'make')
        ) {
            return null;
        }

        if (
            $node instanceof Node\Expr\FuncCall
            && $node->name->getLast() !== 'view'
        ) {
            return null;
        }

        if (
            !($arg = $node->getArgs()[0] ?? null)
            || !property_exists($arg->value, 'value')
            || !Str::startsWith($arg->value->value, 'admin.')
        ) {
            return null;
        }

        $node->args[0] = new Node\Arg(BuilderHelpers::normalizeValue(
            Str::replaceFirst('admin.', 'twill.', $arg->value->value),
        ));

        return $node;
    }
}
