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
    public static $ROUTES;

    public $baseDir;

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Change usages of admin. routes', [
                new CodeSample(
                    'route("admin.blogs");',
                    'route("twill.blogs");'
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
        $isRouteCall = false;
        if ($node instanceof Node\Expr\StaticCall) {
            $isRouteCall = $node->name->name === 'route';
        } elseif ($node instanceof Node\Expr\FuncCall) {
            if ($node->name->parts ?? false) {
                $isRouteCall = $node->name->parts[0] === 'route';
            }
        }

        if ($isRouteCall) {
            $routes = $this->loadRoutes();

            if ($node->getArgs()[0] ?? false) {
                /** @var \PhpParser\Node\Arg $arg */
                $arg = $node->getArgs()[0];
                if (array_key_exists($arg->value->value, $routes)) {
                    $node->args[0] = new Node\Arg(BuilderHelpers::normalizeValue($routes[$arg->value->value]));
                    return $node;
                }
            }
        }

        return null;
    }

    private function loadRoutes(): ?array
    {
        if (self::$ROUTES === null) {
            // Get all twill routes so we can process them properly.
            $this->getLaravel();
            $routes = Route::getRoutes();

            $twillRouteList = [];
            foreach ($routes->getRoutes() as $route) {
                if (Str::startsWith($route->getName(), 'twill.')) {
                    $twillRouteList[Str::replaceFirst('twill.', 'admin.', $route->getName())] = $route->getName();
                }
            }

            self::$ROUTES = $twillRouteList;
        }

        return self::$ROUTES;
    }
}
