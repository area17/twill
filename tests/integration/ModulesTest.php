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
    protected $description_en;
    protected $description_fr;
    protected $bio_en;
    protected $bio_fr;
    private $birthday;

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
    protected function getCreateAuthorData(): array
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

    public function getUpdateAuthorData()
    {
        return [
            'name' => [
                'en' => $this->name_en,
                'fr' => $this->name_fr,
                'pt-BR' => '',
            ],
            'slug' => [
                'en' => $this->slug_en,
                'fr' => $this->slug_en,
                'pt-BR' => $this->slug_en,
            ],
            'description' => [
                'en' => ($this->description_en =
                    '[EN] ' . $this->faker->text(80)),
                'fr' => ($this->description_fr =
                    '[FR] ' . $this->faker->text(80)),
                'pt-BR' => '',
            ],
            'birthday' => ($this->birthday = now()->format('Y-m-d')),
            'bio' => [
                'en' => ($this->bio_en = '[EN] ' . $this->faker->text(800)),
                'fr' => ($this->bio_fr = '[FR] ' . $this->faker->text(800)),
                'pt-BR' => '',
            ],
            'cmsSaveType' => 'save',
            'published' => false,
            'public' => false,
            'publish_start_date' => null,
            'publish_end_date' => null,
            'languages' => [
                [
                    'shortlabel' => 'EN',
                    'label' => 'English',
                    'value' => 'en',
                    'disabled' => false,
                    'published' => '1',
                ],
                [
                    'shortlabel' => 'FR',
                    'label' => 'French',
                    'value' => 'fr',
                    'disabled' => false,
                    'published' => '0',
                ],
                [
                    'shortlabel' => 'PT-BR',
                    'label' => 'pt-BR',
                    'value' => 'pt-BR',
                    'disabled' => false,
                    'published' => '0',
                ],
            ],
            'parent_id' => 0,
            'medias' => [],
            'browsers' => [],
            'blocks' => [],
            'repeaters' => [],
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
            $this->getCreateAuthorData()
        )->assertStatus(200);

        $authorTranslation = AuthorTranslation::where('name', $this->name_en)
            ->where('locale', 'en')
            ->first();

        $this->assertNotNull($authorTranslation);

        $this->assertCount(3, $authorTranslation->author->slugs);

        $this->request(
            "/twill/personnel/authors/{$authorTranslation->author->id}",
            'PUT',
            $this->getUpdateAuthorData()
        )->assertStatus(200);

        $authorTranslation->author->refresh();

        $this->assertEquals(
            $authorTranslation->author->birthday,
            $this->birthday
        );
    }
}
