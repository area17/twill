<?php

namespace A17\Twill\Rector;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameViews extends LaravelAwareRectorRule
{
    public static $ROUTES;

    public $baseDir;

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change usages of admin. views', [
                new CodeSample(
                    'view("admin.blocks.text");',
                    'view("twill.blocks.text");'
                ),
            ]
        );
    }

    /**
     * @return class-string[]
     */
    public function getNodeTypes(): array
    {
        return [Node\Expr\FuncCall::class, Node\Expr\StaticCall::class];
    }

    public function refactor(Node $node): ?\PhpParser\Node
    {
        $isViewCall = false;
        if ($node instanceof Node\Expr\StaticCall) {
            if ($node->name->name === 'make') {
                $isViewCall = $node->class->getLast() === 'View';
            }
        } elseif ($node instanceof Node\Expr\FuncCall) {
            if ($node->name->parts ?? false) {
                $isViewCall = $node->name->parts[0] === 'view';
            }
        }

        if ($isViewCall && $node->getArgs()[0] ?? false) {
            /** @var \PhpParser\Node\Arg $arg */
            $arg = $node->getArgs()[0];
            if (Str::startsWith($arg->value->value, 'admin.')) {
                $node->args[0] = new Node\Arg(
                    BuilderHelpers::normalizeValue(Str::replaceFirst('admin.', 'twill.', $arg->value->value))
                );
                return $node;
            }
        }

        return null;
    }
}
