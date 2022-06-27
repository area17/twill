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

    public function testColumnFutureStartAndEndDate(): void
    {
        $column = ScheduledStatus::make();

        $this->author->publish_start_date = Carbon::createFromDate(2050, 5, 11);
        $this->author->publish_end_date = Carbon::createFromDate(2050, 5, 12);

        $flattedOutput = rtrim(
            str_replace(PHP_EOL, null, preg_replace('/\s+/', ' ', $column->renderCell($this->author)))
        );

        $this->assertEquals(
            "<span> <span class=\"tablecell__datePub \"> May 11, 2050 - May 12, 2050 <br> <span>Scheduled</span> </span> </span>",
            $flattedOutput
        );
    }

    public function testColumnPastStartAndEndDate(): void
    {
        $column = ScheduledStatus::make();

        $this->author->publish_start_date = Carbon::createFromDate(2020, 5, 11);
        $this->author->publish_end_date = Carbon::createFromDate(2020, 5, 12);

        $flattedOutput = rtrim(
            str_replace(PHP_EOL, null, preg_replace('/\s+/', ' ', $column->renderCell($this->author)))
        );

        $this->assertEquals(
            "<span> <span class=\"tablecell__datePub s--expired \"> May 11, 2020 - May 12, 2020 <br> <span>Expired</span> </span> </span>",
            $flattedOutput
        );
    }

    public function testColumnFutureStartOnly(): void
    {
        $column = ScheduledStatus::make();

        $this->author->publish_start_date = Carbon::createFromDate(2050, 5, 11);
        $this->author->publish_end_date = null;

        $flattedOutput = rtrim(
            str_replace(PHP_EOL, null, preg_replace('/\s+/', ' ', $column->renderCell($this->author)))
        );

        $this->assertEquals(
            "<span> <span class=\"tablecell__datePub \"> May 11, 2050 <br> <span>Scheduled</span> </span> </span>",
            $flattedOutput
        );
    }

    public function testColumnPastStartOnly(): void
    {
        $column = ScheduledStatus::make();

        $this->author->publish_start_date = Carbon::createFromDate(2020, 5, 11);
        $this->author->publish_end_date = null;

        $flattedOutput = rtrim(
            str_replace(PHP_EOL, null, preg_replace('/\s+/', ' ', $column->renderCell($this->author)))
        );

        $this->assertEquals(
            "<span> - </span>",
            $flattedOutput
        );
    }

    public function testColumnFutureEndOnly(): void
    {
        $column = ScheduledStatus::make();

        $this->author->publish_start_date = null;
        $this->author->publish_end_date = Carbon::createFromDate(2050, 5, 11);

        $flattedOutput = rtrim(
            str_replace(PHP_EOL, null, preg_replace('/\s+/', ' ', $column->renderCell($this->author)))
        );

        $this->assertEquals(
            "<span> - </span>",
            $flattedOutput
        );
    }
}
