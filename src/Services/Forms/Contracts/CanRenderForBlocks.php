<?php

namespace A17\Twill\Services\Forms\Contracts;

interface CanRenderForBlocks
{
    public function renderForBlocks(bool $renderForBlocks = true): self;

    public function forBlocks(): bool;
}
