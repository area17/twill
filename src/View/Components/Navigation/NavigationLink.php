<?php

namespace A17\Twill\View\Components\Navigation;

use A17\Twill\Facades\TwillRoutes;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Stringable;

class NavigationLink extends Component
{
    private ?string $route = null;

    private bool $selfAsFirstChild = true;

    private bool $targetBlank = false;

    /**
     * @var NavigationLink[]
     */
    private array $children = [];

    private ?string $href = null;

    private Stringable|string $title;

    private array $customAttributes = [];

    private array $routeArguments = [];

    private ?Closure $onlyWhenFunction = null;

    private bool $isModuleRoute = false;

    private ?string $module = null;
    private ?string $moduleAction = null;

    public static function make(): self
    {
        return new self();
    }

    public function onlyWhen(Closure $closure): self
    {
        $this->onlyWhenFunction = $closure;

        return $this;
    }

    public function withAttributes(array $attributes): self
    {
        $this->customAttributes = $attributes;

        return $this;
    }

    public function title(Stringable|string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function forRoute(string $route, array $routeArguments = []): self
    {
        $this->route = $route;
        $this->routeArguments = $routeArguments;

        return $this;
    }

    public function getRouteArguments(): array
    {
        return $this->routeArguments;
    }

    public function shouldShow(): bool
    {
        if ($this->onlyWhenFunction) {
            $closure = $this->onlyWhenFunction;

            return $closure();
        }
        // If there is no closure we should just render it.
        return true;
    }

    public function doNotAddSelfAsFirstChild(bool $doNotAddSelfAsFirstChild = true): self
    {
        $this->selfAsFirstChild = ! $doNotAddSelfAsFirstChild;

        return $this;
    }

    public function forModule(string $module, ?string $action = null): self
    {
        if (! isset($this->title)) {
            $this->title(Str::title($module));
        }

        $this->module = $module;
        $this->moduleAction = $action;

        $this->isModuleRoute = true;

        return $this;
    }

    public function forSingleton(string $module): self
    {
        if (! isset($this->title)) {
            $this->title(Str::title($module));
        }

        $this->route = 'twill.' . $module;

        return $this;
    }

    public function toExternalUrl(string $href): self
    {
        $this->href = $href;

        return $this;
    }

    /**
     * @param \A17\Twill\View\Components\Navigation\NavigationLink[] $links
     */
    public function setChildren(array $links): self
    {
        $this->children = $links;

        return $this;
    }

    public function shouldOpenInNewWindow(bool $shouldOpenInNewWindow = true): self
    {
        $this->targetBlank = $shouldOpenInNewWindow;

        return $this;
    }

    /**
     * @return NavigationLink[]
     */
    public function getChildren(): array
    {
        $fullList = [];

        if ($this->selfAsFirstChild && $this->children !== []) {
            $cloneOfSelf = clone $this;
            // The clone we modify so we do not get unintended behaviors (such as infinite depth).
            $cloneOfSelf->setChildren([]);
            $cloneOfSelf->doNotAddSelfAsFirstChild();
            $fullList[] = $cloneOfSelf;
        }

        $fullList = array_merge($fullList, $this->children);

        return array_filter($fullList, fn(NavigationLink $link) => $link->shouldShow());
    }

    protected function getModuleRoute(string $moduleName, ?string $action = null): string
    {
        // There are some exceptions which not convert properly to plural if already in plural mode. If it is one of
        // these, we skip.
        $exceptions = ['menus'];

        if (in_array($moduleName, $exceptions)) {
            $routeMatcher = $moduleName;
        } else {
            $routeMatcher = Str::plural($moduleName);
        }

        return 'twill.' . TwillRoutes::getModuleRouteFromRegistry(
            Str::camel($routeMatcher)
        ) . '.' . ($action ?? 'index');
    }

    protected function getRoute(): ?string
    {
        if ($this->route) {
            return $this->route;
        }

        if ($this->isModuleRoute) {
            return $this->getModuleRoute($this->module, $this->moduleAction ?? 'index');
        }

        return null;
    }

    protected function getHref(): string
    {
        if ($this->getRoute() && ($this->isModuleRoute || $this->route)) {
            return route($this->getRoute(), $this->routeArguments);
        }
        // Could also return the route.
        return $this->href ?? '#';
    }

    public function isActive(): bool
    {
        if ($this->hasActiveChild()) {
            return true;
        }

        $currentRoute = request()?->route();

        if ($currentRoute->getName() === $this->getRoute()) {
            return $currentRoute->parameters() === $this->routeArguments;
        }

        // Check if it maybe is a edit route of a model.
        if ($this->isModuleRoute) {
            // Attempt to handle nested items.
            $parent = null;
            foreach (array_keys($currentRoute->parameters()) as $singularModuleName) {
                $moduleName = Str::plural($singularModuleName);
                if ($moduleName === $this->module || ($parent . $moduleName) === $this->module) {
                    return true;
                }
                $parent .= $moduleName;
            }

            $baseRoute = Str::beforeLast($currentRoute->getName(), '.');
            $linkRoute = Str::beforeLast($this->getRoute(), '.');

            return $baseRoute === $linkRoute;
        }

        return false;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    protected function hasActiveChild(): bool
    {
        foreach ($this->children as $navigationLink) {
            if ($navigationLink->isActive()) {
                return true;
            }
        }

        return false;
    }

    protected function isTargetBlank(): bool
    {
        return $this->targetBlank;
    }

    public function render(string $class = 'header__item'): View
    {
        return view('twill::partials.navigation.navigation_link', [
            'class' => $class,
            'is_active' => $this->isActive(),
            'title' => $this->getTitle(),
            'href' => $this->getHref(),
            'target_blank' => $this->isTargetBlank(),
            'attributes' => $this->customAttributes,
        ]);
    }
}
