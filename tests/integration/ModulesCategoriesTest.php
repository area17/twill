<?php

namespace A17\Twill\Tests\Integration;

class ModulesCategoriesTest extends ModulesTestBase
{
    public function testCanDisplayModuleInNavigation()
    {
        $this->request('/twill');

        $this->assertSee('Personnel');
        $this->assertSee('Categories');

        $this->request('/twill/categories');

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
}
