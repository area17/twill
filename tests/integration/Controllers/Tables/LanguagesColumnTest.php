<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables;

use A17\Twill\Services\Listings\Columns\Languages;
use A17\Twill\Tests\Integration\ModulesTestBase;

class LanguagesColumnTest extends ModulesTestBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->author = $this->createAuthor();
    }

    public function testColumn(): void
    {
        // This column is just empty and renders nothing on its own currently.
        $column = Languages::make();

        $this->assertEquals('languages', $column->getKey());

        $this->assertEquals('', $column->renderCell($this->author));
    }
}
