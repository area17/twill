<?php

namespace A17\Twill\Tests\Integration;

class BrowsersTest extends TestCase
{
    protected $allFiles = [
       '{$stubs}/browsers/2021_08_10_0001_create_authors_tables.php' => '{$database}/migrations/',
       '{$stubs}/browsers/Author.php' => '{$app}/Models/',
       '{$stubs}/browsers/AuthorController.php' => '{$app}/Http/Controllers/Admin/',
       '{$stubs}/browsers/AuthorRepository.php' => '{$app}/Repositories/',
       '{$stubs}/browsers/AuthorRequest.php' => '{$app}/Http/Requests/Admin/',
       '{$stubs}/browsers/authors-form.blade.php' => '{$resources}/views/admin/authors/',

       '{$stubs}/browsers/2021_08_10_0002_create_articles_tables.php' => '{$database}/migrations/',
       '{$stubs}/browsers/2021_08_10_0003_create_article_author_table.php' => '{$database}/migrations/',
       '{$stubs}/browsers/Article.php' => '{$app}/Models/',
       '{$stubs}/browsers/ArticleController.php' => '{$app}/Http/Controllers/Admin/',
       '{$stubs}/browsers/ArticleRepository.php' => '{$app}/Repositories/',
       '{$stubs}/browsers/ArticleRequest.php' => '{$app}/Http/Requests/Admin/',
       '{$stubs}/browsers/ArticleRevision.php' => '{$app}/Models/Revisions/',
       '{$stubs}/browsers/articles-form.blade.php' => '{$resources}/views/admin/articles/',

       '{$stubs}/browsers/2021_08_10_0004_create_bios_tables.php' => '{$database}/migrations/',
       '{$stubs}/browsers/Bio.php' => '{$app}/Models/',
       '{$stubs}/browsers/BioController.php' => '{$app}/Http/Controllers/Admin/',
       '{$stubs}/browsers/BioRepository.php' => '{$app}/Repositories/',
       '{$stubs}/browsers/BioRequest.php' => '{$app}/Http/Requests/Admin/',
       '{$stubs}/browsers/BioRevision.php' => '{$app}/Models/Revisions/',
       '{$stubs}/browsers/bios-form.blade.php' => '{$resources}/views/admin/bios/',

       '{$stubs}/browsers/2021_08_10_0005_create_books_tables.php' => '{$database}/migrations/',
       '{$stubs}/browsers/Book.php' => '{$app}/Models/',
       '{$stubs}/browsers/BookController.php' => '{$app}/Http/Controllers/Admin/',
       '{$stubs}/browsers/BookRepository.php' => '{$app}/Repositories/',
       '{$stubs}/browsers/BookRequest.php' => '{$app}/Http/Requests/Admin/',
       '{$stubs}/browsers/BookRevision.php' => '{$app}/Models/Revisions/',
       '{$stubs}/browsers/books-form.blade.php' => '{$resources}/views/admin/books/',

       '{$stubs}/browsers/twill-navigation.php' => '{$config}',
       '{$stubs}/browsers/admin.php' => '{$routes}',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->copyFiles($this->allFiles);

        $this->loadModulesConfig();

        $this->migrate();

        $this->login();
    }

    public function testItWorksDummy()
    {
        $this->assertEquals('yes', 'yes');
    }
}
