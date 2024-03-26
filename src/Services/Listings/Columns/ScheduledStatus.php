<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Listings\TableColumn;
use Carbon\Carbon;

class ScheduledStatus extends TableColumn
{
    public static function make(): static
    {
        $column = new static();
        $column->field('scheduled_status');

        return $column;
    }
}
