<?php

namespace A17\Twill\Tests\Integration;

use Illuminate\Support\Str;
use App\Models\Translations\CategoryTranslation;

class ModulesCategoriesTest extends ModulesTest
{
    public $title;
    public $title_en;
    public $title_fr;
    public $slug_en;
    public $slug_fr;
    public $category;

    /**
     * @return array
     */
    protected function getCreateCategoryData(): array
    {
        $category = $this->title = 'Category: ' . $this->faker->name;

        return [
            'title' => [
                'en' => ($this->title_en = '[EN] ' . $category),
                'fr' => ($this->title_fr = '[FR] ' . $category),
            ],
            'slug' => [
                'en' => ($this->slug_en = Str::slug($this->title_en)),
                'fr' => ($this->slug_fr = Str::slug($this->title_fr)),
            ],
            'published' => false,
            'languages' => [
                [
                    'shortlabel' => 'EN',
                    'label' => 'English',
                    'value' => 'en',
                    'disabled' => false,
                    'published' => true,
                ],
                [
                    'shortlabel' => 'FR',
                    'label' => 'French',
                    'value' => 'fr',
                    'disabled' => false,
                    'published' => true,
                ],
                [
                    'shortlabel' => 'PT-BR',
                    'label' => 'pt-BR',
                    'value' => 'pt-BR',
                    'disabled' => false,
                    'published' => false,
                ],
            ],
        ];
    }

    protected function createCategory($count = 1)
    {
        foreach (range(1, $count) as $c) {
            $this->request(
                '/twill/categories',
                'POST',
                $this->getCreateCategoryData()
            )->assertStatus(200);
        }

        $this->translation = CategoryTranslation::where(
            'title',
            $this->title_en
        )
            ->where('locale', 'en')
            ->first();

        $this->category = $this->translation->category;

        $this->assertNotNull($this->translation);

        $this->assertCount(3, $this->category->slugs);
    }

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
