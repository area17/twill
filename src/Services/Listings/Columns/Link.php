<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Listings\TableColumn;
use Closure;

class Link extends TableColumn
{
    protected ?Closure $url = null;

    protected Closure|string|null $content = null;

    protected bool $targetBlank = false;

    public function url(Closure $urlFunction): static
    {
        $this->url = $urlFunction;
        return $this;
    }

    public function content(Closure|string $contentFunction): static
    {
        $this->content = $contentFunction;
        return $this;
    }

    public function shouldOpenInNewWindow(bool $shouldOpenInNewWindow = true): static
    {
        $this->targetBlank = $shouldOpenInNewWindow;
        return $this;
    }

    protected function getRenderValue(TwillModelContract $model): string
    {
        $url = null;
        if (($urlFunction = $this->url) !== null) {
            $url = $urlFunction($model);
        }

        $content = null;
        if (($contentFunction = $this->content) !== null) {
            if (is_string($this->content)) {
                $content = $this->content;
            } else {
                $content = $contentFunction($model);
            }
        }

        if ($url === null) {
            return $content;
        }

        return
            '<a href="' . $url . '"' . ($this->targetBlank ? ' target="_blank"' : '') . '>'
            . ($content ?? $url)
            . '</a>';
    }
}
