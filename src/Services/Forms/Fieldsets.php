<?php

namespace A17\Twill\Services\Forms;

use A17\Twill\Services\Forms\Contracts\CanHaveSubfields;
use A17\Twill\Services\Forms\Traits\HasSubFields;
use Illuminate\Support\Collection;

class Fieldsets extends Collection implements  CanHaveSubfields
{
    use HasSubFields;
}
