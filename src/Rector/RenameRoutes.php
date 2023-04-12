<?php

namespace A17\Twill\Rector;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameRoutes extends LaravelAwareRectorRule
{
    protected array $routes;

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change usages of admin. routes',
            [
                new CodeSample(
                    'route("admin.blogs");',
                    'route("twill.blogs");'
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
            && $node->name->name !== 'route'
        ) {
            return null;
        }

        if (
            $node instanceof Node\Expr\FuncCall
            && $node->name->getLast() !== 'route'
        ) {
            return null;
        }

        $this->routes ??= $this->loadRoutes();
        if (
            ! ($arg = $node->getArgs()[0] ?? null)
            || ! property_exists($arg->value, 'value')
            || ! array_key_exists($arg->value->value, $this->routes)
        ) {
            return null;
        }

        $node->args[0] = new Node\Arg(BuilderHelpers::normalizeValue(
            $this->routes[$arg->value->value]
        ));

        return $node;
    }

    private function loadRoutes(): array
    {
        foreach (Route::getRoutes()->getRoutes() as $route) {
            if (Str::startsWith($route->getName(), 'twill.')) {
                $legacyName = Str::replaceFirst('twill.', 'admin.', $route->getName());
                $routes[$legacyName] = $route->getName();
            }
        }

        return $routes ?? [];
    }
}
