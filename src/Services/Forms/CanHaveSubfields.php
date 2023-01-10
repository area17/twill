<?php

namespace A17\Twill\Services\Forms;

interface CanHaveSubfields
{
    public function registerDynamicRepeaters(): void;
}
