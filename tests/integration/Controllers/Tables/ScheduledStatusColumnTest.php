<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables;

use A17\Twill\Services\Listings\Columns\ScheduledStatus;
use A17\Twill\Tests\Integration\ModulesTestBase;
use Carbon\Carbon;

class ScheduledStatusColumnTest extends ModulesTestBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->author = $this->createAuthor();
    }

    public function testColumn(): void
    {
        $column = ScheduledStatus::make();

        $this->author->publish_start_date = Carbon::createFromDate(2050, 5, 11);
        $this->author->publish_end_date = Carbon::createFromDate(2050, 5, 12);

        $this->assertEquals('', $column->renderCell($this->author));
    }
}
