<?php

namespace A17\Twill\View\Components\Navigation;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class NavigationLink extends Component
{
    private ?string $route = null;

    private bool $selfAsFirstChild = false;

    private bool $targetBlank = false;

    /**
     * @var array<int, NavigationLink>
     */
    private array $children = [];

    private ?string $href = null;

    private string $title;

    private array $customAttributes = [];

    private array $routeArguments = [];

    private ?Closure $onlyWhenFunction = null;

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

    public function title(string $title): self
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

    public function addAsFirstChild(bool $selfAsFirstChild = true): self
    {
        $this->selfAsFirstChild = $selfAsFirstChild;

        return $this;
    }

    public function forModule(string $module, ?string $action = null): self
    {
        if (!isset($this->title)) {
            $this->title(Str::title($module));
        }

        $this->route = $this->getModuleRoute($module, $action ?? 'index');

        return $this;
    }

    public function toExternalUrl(string $href): self
    {
        $this->href = $href;

        return $this;
    }

    /**
     * @param array<int, NavigationLink> $links
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
     * @return array<int, NavigationLink>
     */
    public function getChildren(): array
    {
        $fullList = [];
        $cloneOfSelf = clone $this;

        if ($this->selfAsFirstChild) {
            // The clone we modify so we do not get unintended behaviors (such as infinite depth).
            $cloneOfSelf->setChildren([]);
            $cloneOfSelf->addAsFirstChild(false);
            $fullList[] = $cloneOfSelf;
        }

        $fullList = array_merge($fullList, $this->children);

        return array_filter($fullList, fn(NavigationLink $link) => $link->shouldShow());
    }

    protected function getModuleRoute(string $moduleName, ?string $action = null): string
    {
        return 'twill.' . \A17\Twill\TwillRoutes::$registry[Str::camel($moduleName)] . '.' . ($action ?? 'index');
    }

    protected function getHref(): string
    {
        if ($this->route) {
            return route($this->route, $this->routeArguments);
        }
        // Could also return the route.
        return $this->href ?? '#';
    }

    public function isActive(): bool
    {
        if (request()->route()->getName() === $this->route) {
            return request()->route()->parameters() === $this->routeArguments;
        }

        return $this->hasActiveChild();
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
            'title' => $this->title,
            'href' => $this->getHref(),
            'target_blank' => $this->isTargetBlank(),
            'attributes' => $this->customAttributes,
        ]);
    }
}
