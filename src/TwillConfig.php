<?php

namespace A17\Twill;

class TwillConfig
{
    protected ?int $maxRevisions = null;

    public function maxRevisions(int $max): void
    {
        $this->maxRevisions = $max;
    }

    public function getRevisionLimit(): ?int
    {
        return $this->maxRevisions;
    }
}
