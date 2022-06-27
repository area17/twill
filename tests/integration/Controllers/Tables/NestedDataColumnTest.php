<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables;

use A17\Twill\Models\Model;
use A17\Twill\Services\Listings\Columns\NestedData;
use A17\Twill\Tests\Integration\NestedModuleTestBase;

class NestedDataColumnTest extends NestedModuleTestBase
{
    public Model $parent;

    public function setUp(): void
    {
        parent::setUp();

        $this->parent = $this->createNodes(['Parent 1'])->first();
    }

    public function testColumn(): void
    {
        $column = NestedData::make()->title('Child');

        $this->assertEquals('0 children', $column->renderCell($this->parent));
    }

    public function testSingleChild(): void {
        $this->parent->children()->create(['title' => 'Child 1', 'published' => true]);

        $column = NestedData::make()->title('Child');
        $this->assertEquals('1 child', $column->renderCell($this->parent));
    }

    public function testMultipleChilden(): void {
        $this->parent->children()->create(['title' => 'Child 1', 'published' => true]);
        $this->parent->children()->create(['title' => 'Child 1', 'published' => true]);

        $column = NestedData::make()->title('Child');
        $this->assertEquals('2 children', $column->renderCell($this->parent));
    }
}
