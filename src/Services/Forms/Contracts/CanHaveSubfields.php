<?php

namespace A17\Twill\Services\Forms\Contracts;

interface CanHaveSubfields
{
    public function registerDynamicRepeaters(): void;
}
