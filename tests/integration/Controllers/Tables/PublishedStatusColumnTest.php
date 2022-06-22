<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables;

use A17\Twill\Services\Listings\Columns\PublishStatus;
use A17\Twill\Tests\Integration\ModulesTestBase;

class PublishedStatusColumnTest extends ModulesTestBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->author = $this->createAuthor();
    }

    public function testColumn(): void
    {
        // This column is just empty and renders nothing on its own currently.
        $column = PublishStatus::make();

        $this->assertEquals('published', $column->getKey());

        $this->assertEquals('', $column->renderCell($this->author));

        $this->author->published = true;
        $this->assertEquals('', $column->renderCell($this->author));
    }
}
