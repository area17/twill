<?php

namespace A17\Twill\Tests\Integration;

use App\Models\Article;
use App\Models\Author;
use App\Models\Bio;
use App\Models\Book;
use App\Repositories\ArticleRepository;
use App\Repositories\AuthorRepository;
use App\Repositories\BioRepository;
use App\Repositories\BookRepository;
use A17\Twill\Models\RelatedItem;

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
       '{$stubs}/browsers/articles-view.blade.php' => '{$resources}/views/site/article.blade.php',

       '{$stubs}/browsers/2021_08_10_0004_create_bios_tables.php' => '{$database}/migrations/',
       '{$stubs}/browsers/Bio.php' => '{$app}/Models/',
       '{$stubs}/browsers/BioController.php' => '{$app}/Http/Controllers/Admin/',
       '{$stubs}/browsers/BioRepository.php' => '{$app}/Repositories/',
       '{$stubs}/browsers/BioRequest.php' => '{$app}/Http/Requests/Admin/',
       '{$stubs}/browsers/BioRevision.php' => '{$app}/Models/Revisions/',
       '{$stubs}/browsers/bios-form.blade.php' => '{$resources}/views/admin/bios/',
       '{$stubs}/browsers/bios-view.blade.php' => '{$resources}/views/site/bio.blade.php',

       '{$stubs}/browsers/2021_08_10_0005_create_books_tables.php' => '{$database}/migrations/',
       '{$stubs}/browsers/Book.php' => '{$app}/Models/',
       '{$stubs}/browsers/BookController.php' => '{$app}/Http/Controllers/Admin/',
       '{$stubs}/browsers/BookRepository.php' => '{$app}/Repositories/',
       '{$stubs}/browsers/BookRequest.php' => '{$app}/Http/Requests/Admin/',
       '{$stubs}/browsers/BookRevision.php' => '{$app}/Models/Revisions/',
       '{$stubs}/browsers/books-form.blade.php' => '{$resources}/views/admin/books/',
       '{$stubs}/browsers/books-view.blade.php' => '{$resources}/views/site/book.blade.php',

       '{$stubs}/browsers/twill-navigation.php' => '{$config}',
       '{$stubs}/browsers/admin.php' => '{$base}/routes/admin.php',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->copyFiles($this->allFiles);

        $this->migrate();

        $this->login();
    }

    public function createAuthors()
    {
        $this->assertEquals(0, Author::count());

        $authors = collect(['Alice', 'Bob', 'Charlie'])->map(function ($name) {
            return app(AuthorRepository::class)->create([
                'title' => $name,
                'published' => true,
            ]);
        });

        $this->assertEquals(3, Author::count());

        return $authors;
    }

    public function createArticle()
    {
        $item = app(ArticleRepository::class)->create([
            'title' => 'Lorem ipsum dolor sit amet',
            'published' => true,
        ]);

        $this->assertEquals(1, Article::count());

        return $item;
    }

    public function createArticleWithAuthors($authors)
    {
        $item = $this->createArticle();

        $this->httpRequestAssert("/twill/articles/{$item->id}", 'PUT', [
            'browsers' => [
                'authors' => $authors->map(function ($author) {
                    return ['id' => $author->id];
                }),
            ],
        ]);

        return $item;
    }

    public function createBio()
    {
        $item = app(BioRepository::class)->create([
            'title' => 'Lorem ipsum dolor sit amet',
            'published' => true,
        ]);

        $this->assertEquals(1, Bio::count());

        return $item;
    }

    public function createBioWithAuthor($author)
    {
        $item = $this->createBio();

        $this->httpRequestAssert("/twill/bios/{$item->id}", 'PUT', [
            'browsers' => [
                'author' => [
                    ['id' => $author->id],
                ],
            ],
        ]);

        return $item;
    }

    public function createBook()
    {
        $item = app(BookRepository::class)->create([
            'title' => 'Lorem ipsum dolor sit amet',
            'published' => true,
        ]);

        $this->assertEquals(1, Book::count());

        return $item;
    }

    public function createBookWithAuthors($authors)
    {
        $item = $this->createBook();

        $this->httpRequestAssert("/twill/books/{$item->id}", 'PUT', [
            'browsers' => [
                'authors' => $authors->map(function ($author) {
                    return [
                        'id' => $author->id,
                        'endpointType' => '\\App\\Models\\Author',
                    ];
                }),
            ],
        ]);
        return $item;
    }

    public function testBrowserBelongsToMany()
    {
        $authors = $this->createAuthors();
        $article = $this->createArticleWithAuthors($authors);

        // User can attach authors
        $this->assertEquals(3, Article::first()->authors->count());
        $this->assertEquals(
            $authors->pluck('id')->sort()->toArray(),
            Article::first()->authors->pluck('id')->sort()->toArray()
        );
    }

    public function testBrowserBelongsToManyPreview()
    {
        $authors = $this->createAuthors();
        $article = $this->createArticleWithAuthors($authors);

        // User can preview
        $this->httpRequestAssert("/twill/articles/preview/{$article->id}", 'PUT', []);
        $this->assertSee('This is an article');
    }

    public function testBrowserBelongsToManyPreviewRevisions()
    {
        $authors = $this->createAuthors();
        $article = $this->createArticleWithAuthors($authors);

        // User can preview revisions
        $this->httpRequestAssert("/twill/articles/preview/{$article->id}", 'PUT', [
            'revisionId' => Article::first()->revisions->last()->id,
        ]);
        $this->assertSee('This is an article');
    }

    public function testBrowserBelongsToManyRestoreRevisions()
    {
        $authors = $this->createAuthors();
        $article = $this->createArticleWithAuthors($authors);

        // User can restore revisions
        $this->httpRequestAssert("/twill/articles/restoreRevision/{$article->id}", 'GET', [
            'revisionId' => Article::first()->revisions->last()->id,
        ]);
        $this->assertSee('You are currently editing an older revision of this content');
    }

    public function testBrowserBelongsTo()
    {
        $authors = $this->createAuthors();
        $bio = $this->createBioWithAuthor($authors[0]);

        // User can attach authors
        $this->assertNotEmpty(Bio::first()->author);
        $this->assertEquals($authors[0]->id, Bio::first()->author->id);
    }

    public function testBrowserBelongsToPreview()
    {
        $authors = $this->createAuthors();
        $bio = $this->createBioWithAuthor($authors[0]);

        // User can preview
        $this->httpRequestAssert("/twill/bios/preview/{$bio->id}", 'PUT', []);
        $this->assertSee('This is a bio');
    }

    public function testBrowserBelongsToPreviewRevisions()
    {
        $authors = $this->createAuthors();
        $bio = $this->createBioWithAuthor($authors[0]);

        // User can preview revisions
        $this->httpRequestAssert("/twill/bios/preview/{$bio->id}", 'PUT', [
            'revisionId' => Bio::first()->revisions->last()->id,
        ]);
        $this->assertSee('This is a bio');
    }

    public function testBrowserBelongsToRestoreRevisions()
    {
        $authors = $this->createAuthors();
        $bio = $this->createBioWithAuthor($authors[0]);

        // User can restore revisions
        $this->httpRequestAssert("/twill/bios/restoreRevision/{$bio->id}", 'GET', [
            'revisionId' => Bio::first()->revisions->last()->id,
        ]);
        $this->assertSee('You are currently editing an older revision of this content');
    }

    public function testBrowserRelated()
    {
        $authors = $this->createAuthors();
        $book = $this->createBookWithAuthors($authors);

        // User can attach authors
        $this->assertEquals(3, RelatedItem::count());
        $this->assertEquals(3, Book::first()->getRelated('authors')->count());
        $this->assertEquals(
            $authors->pluck('id')->sort()->toArray(),
            Book::first()->getRelated('authors')->pluck('id')->sort()->toArray()
        );
    }

    public function testBrowserRelatedPreview()
    {
        $authors = $this->createAuthors();
        $book = $this->createBookWithAuthors($authors);

        // User can preview
        $this->httpRequestAssert("/twill/books/preview/{$book->id}", 'PUT', []);
        $this->assertSee('This is a book');
    }

    public function testBrowserRelatedPreviewRevisions()
    {
        $authors = $this->createAuthors();
        $book = $this->createBookWithAuthors($authors);

        // User can preview revisions
        $this->httpRequestAssert("/twill/books/preview/{$book->id}", 'PUT', [
            'revisionId' => Book::first()->revisions->last()->id,
        ]);
        $this->assertSee('This is a book');
    }

    public function testBrowserRelatedRestoreRevisions()
    {
        $authors = $this->createAuthors();
        $book = $this->createBookWithAuthors($authors);

        // User can restore revisions
        $this->httpRequestAssert("/twill/books/restoreRevision/{$book->id}", 'GET', [
            'revisionId' => Book::first()->revisions->last()->id,
        ]);
        $this->assertSee('You are currently editing an older revision of this content');
    }
}
