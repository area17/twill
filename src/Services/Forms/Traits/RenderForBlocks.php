<?php

namespace A17\Twill\Services\Forms\Traits;

trait RenderForBlocks
{
    public bool $renderForBlocks = false;

    public function renderForBlocks(bool $renderForBlocks = true): self
    {
        $this->renderForBlocks = $renderForBlocks;

        return $this;
    }

    public function forBlocks(): bool
    {
        return $this->renderForBlocks;
    }
}
