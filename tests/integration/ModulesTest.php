<?php

namespace A17\Twill\Tests\Integration;

use Illuminate\Support\Facades\Schema;

class ModulesTest extends TestCase
{
    private $authorFiles = [
        '{$stubs}/modules/authors/2019_10_18_193753_create_authors_tables.php' =>
            '{$database}/migrations/2019_10_18_193753_create_authors_tables.php',

        '{$stubs}/modules/authors/admin.php' => '{$base}/routes/admin.php',

        '{$stubs}/modules/authors/Author.php' => '{$app}/Models/Author.php',

        '{$stubs}/modules/authors/AuthorController.php' =>
            '{$app}/Http/Controllers/Admin/AuthorController.php',

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
    }
}
