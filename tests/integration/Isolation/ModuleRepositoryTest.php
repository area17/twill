<?php

namespace A17\Twill\Tests\Integration\Isolation;

use A17\Twill\Tests\Integration\TestCase;
use App\Repositories\AuthorRepository;
use App\Repositories\CategoryRepository;

class ModuleRepositoryTest extends TestCase
{
    public ?string $example = 'tests-modules';

    public function testSlugIsSetWhenSlugTraitBeforeTranslations(): void
    {
        $author = app(AuthorRepository::class)->create([
            'name' => [
                'en' => 'name-en',
                'fr' => 'name-fr',
            ],
            'published' => true,
        ]);

        $fields = app(AuthorRepository::class)->getFormFields($author);

        $this->assertArrayHasKey('slug', $fields['translations']);
        $this->assertEquals('name-en', $fields['translations']['slug']['en']);
    }

    public function testSlugIsSetWhenSlugTraitAfterTranslations(): void
    {
        $category = app(CategoryRepository::class)->create([
            'title' => [
                'en' => 'name-en',
                'fr' => 'name-fr',
            ],
            'published' => true,
        ]);

        $fields = app(CategoryRepository::class)->getFormFields($category);

        $this->assertArrayHasKey('slug', $fields['translations']);
        $this->assertEquals('name-en', $fields['translations']['slug']['en']);
    }
}
