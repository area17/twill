<?php

namespace A17\Twill\Tests\Integration;

use App\Models\Category;

class ModulesCategoriesTest extends ModulesTestBase
{
    public function testCanDisplayModuleInNavigation()
    {
        $this->httpRequestAssert('/twill');

        $this->assertSee('Personnel');
        $this->assertSee('Categories');

        $this->httpRequestAssert('/twill/categories');

        $this->assertSee('Name');
        $this->assertSee('Languages');
        $this->assertSee('Mine');
        $this->assertSee('Add new');
    }

    public function testCanCreateCategory()
    {
        $this->createCategory();
    }

    public function testCanShowCategoriesIndex()
    {
        $this->createCategory(5);

        $this->ajax('/twill/categories')->assertStatus(200);

        $this->assertJson($this->content());

        $this->assertEquals(
            5,
            count(json_decode($this->content(), true)['tableData'])
        );
    }

    public function testCanReorderCategories()
    {
        $this->createCategory(2);

        $category1 = Category::ordered()
            ->get()
            ->first();

        $category2 = Category::orderBy('position', 'desc')
            ->first();

        $this->assertEquals(1, $category1->position);
        $this->assertEquals(2, $category2->position);

        $this->httpRequestAssert('/twill/categories/reorder', 'POST', [
            'ids' => [
                [
                    'id' => $category2->id,
                    'children' => [],
                ],
                [
                    'id' => $category1->id,
                    'children' => [],
                ],
            ],
        ]);

        $this->assertNothingWrongHappened();

        $category1->refresh();
        $category2->refresh();

        $this->assertEquals(1, $category1->position);
        $this->assertEquals(0, $category2->position);
    }

    public function testCanNestCategories()
    {
        $this->createCategory(2);

        $category1 = Category::ordered()
            ->get()
            ->first();

        $category2 = Category::orderBy('position', 'desc')
            ->first();

        $this->httpRequestAssert('/twill/categories/reorder', 'POST', [
            'ids' => [
                [
                    'id' => $category2->id,
                    'children' => [
                        [
                            'id' => $category1->id,
                            'children' => [],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertNothingWrongHappened();

        $category1->refresh();
        $category2->refresh();

        $this->assertTrue($category2->isAncestorOf($category1));
        $this->assertEquals(0, $category1->position);
        $this->assertEquals($category2->title, $category1->parent->title);
    }
}
