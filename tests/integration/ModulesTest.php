<?php

namespace A17\Twill\Tests\Integration;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use App\Models\Translations\AuthorTranslation;

class ModulesTest extends TestCase
{
    protected $name;

    protected $name_en;

    protected $name_fr;

    protected $slug_en;

    protected $slug_fr;

    private $authorFiles = [
        '{$stubs}/modules/authors/2019_10_18_193753_create_authors_tables.php' =>
            '{$database}/migrations/2019_10_18_193753_create_authors_tables.php',

        '{$stubs}/modules/authors/admin.php' => '{$base}/routes/admin.php',

        '{$stubs}/modules/authors/Author.php' => '{$app}/Models/Author.php',

        '{$stubs}/modules/authors/AuthorController.php' =>
            '{$app}/Http/Controllers/Admin/AuthorController.php',

        '{$stubs}/modules/authors/AuthorTranslation.php' =>
            '{$app}/Models/Translations/AuthorTranslation.php',

        '{$stubs}/modules/authors/AuthorRevision.php' =>
            '{$app}/Models/Revisions/AuthorRevision.php',

        '{$stubs}/modules/authors/AuthorSlug.php' =>
            '{$app}/Models/Slugs/AuthorSlug.php',

        '{$stubs}/modules/authors/AuthorRepository.php' =>
            '{$app}/Repositories/AuthorRepository.php',

        '{$stubs}/modules/authors/AuthorRequest.php' =>
            '{$app}/Http/Requests/Admin/AuthorRequest.php',

        '{$stubs}/modules/authors/form.blade.php' =>
            '{$resources}/views/admin/authors/form.blade.php',

        '{$stubs}/modules/authors/translatable.php' =>
            '{$config}/translatable.php',

        '{$stubs}/modules/authors/twill-navigation.php' =>
            '{$config}/twill-navigation.php',
    ];

    /**
     * @return array
     */
    protected function getData(): array
    {
        $name = $this->name = $this->faker->name;

        return [
            'name' => [
                'en' => ($this->name_en = '[EN] ' . $name),
                'fr' => ($this->name_fr = '[FR] ' . $name),
            ],
            'slug' => [
                'en' => ($this->slug_en = Str::slug($this->name_en)),
                'fr' => ($this->slug_fr = Str::slug($this->name_fr)),
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
                    'published' => false,
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

    public function setUp(): void
    {
        parent::setUp();

        $this->copyFiles($this->authorFiles);

        $this->migrate();

        $this->login();
    }

    public function testCanCopyFiles()
    {
        collect($this->authorFiles)->each(function ($destination) {
            $this->assertFileExists($this->makeFileName($destination));
        });
    }

    public function testCanMigrateDatabase()
    {
        $this->assertTrue(Schema::hasTable('authors'));
        $this->assertTrue(Schema::hasTable('author_translations'));
        $this->assertTrue(Schema::hasTable('author_slugs'));
        $this->assertTrue(Schema::hasTable('author_revisions'));
    }

    public function testCanDisplayModuleInNavigation()
    {
        $this->request('/twill');

        $this->assertStringContainsString('Personnel', $this->content());

        $this->request('/twill/personnel/authors');

        $this->assertStringContainsString('Name', $this->content());
        $this->assertStringContainsString('Languages', $this->content());
        $this->assertStringContainsString('Mine', $this->content());
        $this->assertStringContainsString('Add new', $this->content());
    }

    public function testCanCreateAnAuthor()
    {
        $this->request(
            '/twill/personnel/authors',
            'POST',
            $this->getData()
        )->assertStatus(200);

        $authorTranslation = AuthorTranslation::where('name', $this->name_en)
            ->where('locale', 'en')
            ->first();

        $this->assertNotNull($authorTranslation);

        $this->assertCount(3, $authorTranslation->author->slugs);
    }
}
