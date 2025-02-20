<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables;

use A17\Twill\Services\Listings\Columns\Boolean;
use A17\Twill\Tests\Integration\ModulesTestBase;

class BooleanColumnTest extends ModulesTestBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->author = $this->createAuthor();
    }

    public function testColumn(): void
    {
        $column = Boolean::make()->field('published');

        $this->assertEquals('❌', $column->renderCell($this->author));

        $this->author->published = true;
        $this->assertEquals('✅', $column->renderCell($this->author));
    }
}
