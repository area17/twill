<?php

namespace A17\Twill\Tests\Integration;

use App\Models\Bio;
use App\Models\Book;
use App\Models\Letter;
use App\Models\Writer;
use App\Repositories\BioRepository;
use App\Repositories\BookRepository;
use App\Repositories\LetterRepository;
use App\Repositories\WriterRepository;
use A17\Twill\Models\RelatedItem;

class BrowsersTest extends TestCase
{
    protected $allFiles = [
       '{$stubs}/browsers/2021_08_10_0001_create_writers_tables_for_browsers.php' => '{$database}/migrations/',
       '{$stubs}/browsers/Writer.php' => '{$app}/Models/',
       '{$stubs}/browsers/WriterController.php' => '{$app}/Http/Controllers/Admin/',
       '{$stubs}/browsers/WriterRepository.php' => '{$app}/Repositories/',
       '{$stubs}/browsers/WriterRequest.php' => '{$app}/Http/Requests/Admin/',
       '{$stubs}/browsers/WriterRevision.php' => '{$app}/Models/Revisions/',
       '{$stubs}/browsers/writers-form.blade.php' => '{$resources}/views/admin/writers/form.blade.php',
       '{$stubs}/browsers/writers-view.blade.php' => '{$resources}/views/site/writer.blade.php',

       '{$stubs}/browsers/2021_08_10_0002_create_letters_tables_for_browsers.php' => '{$database}/migrations/',
       '{$stubs}/browsers/2021_08_10_0003_create_letter_writer_table_for_browsers.php' => '{$database}/migrations/',
       '{$stubs}/browsers/Letter.php' => '{$app}/Models/',
       '{$stubs}/browsers/LetterController.php' => '{$app}/Http/Controllers/Admin/',
       '{$stubs}/browsers/LetterRepository.php' => '{$app}/Repositories/',
       '{$stubs}/browsers/LetterRequest.php' => '{$app}/Http/Requests/Admin/',
       '{$stubs}/browsers/LetterRevision.php' => '{$app}/Models/Revisions/',
       '{$stubs}/browsers/letters-form.blade.php' => '{$resources}/views/admin/letters/form.blade.php',
       '{$stubs}/browsers/letters-view.blade.php' => '{$resources}/views/site/letter.blade.php',

       '{$stubs}/browsers/2021_08_10_0004_create_bios_tables_for_browsers.php' => '{$database}/migrations/',
       '{$stubs}/browsers/Bio.php' => '{$app}/Models/',
       '{$stubs}/browsers/BioController.php' => '{$app}/Http/Controllers/Admin/',
       '{$stubs}/browsers/BioRepository.php' => '{$app}/Repositories/',
       '{$stubs}/browsers/BioRequest.php' => '{$app}/Http/Requests/Admin/',
       '{$stubs}/browsers/BioRevision.php' => '{$app}/Models/Revisions/',
       '{$stubs}/browsers/bios-form.blade.php' => '{$resources}/views/admin/bios/form.blade.php',
       '{$stubs}/browsers/bios-view.blade.php' => '{$resources}/views/site/bio.blade.php',

       '{$stubs}/browsers/2021_08_10_0005_create_books_tables_for_browsers.php' => '{$database}/migrations/',
       '{$stubs}/browsers/Book.php' => '{$app}/Models/',
       '{$stubs}/browsers/BookController.php' => '{$app}/Http/Controllers/Admin/',
       '{$stubs}/browsers/BookRepository.php' => '{$app}/Repositories/',
       '{$stubs}/browsers/BookRequest.php' => '{$app}/Http/Requests/Admin/',
       '{$stubs}/browsers/BookRevision.php' => '{$app}/Models/Revisions/',
       '{$stubs}/browsers/books-form.blade.php' => '{$resources}/views/admin/books/form.blade.php',
       '{$stubs}/browsers/books-view.blade.php' => '{$resources}/views/site/book.blade.php',

       '{$stubs}/browsers/twill-navigation.php' => '{$config}/',
       '{$stubs}/browsers/admin.php' => '{$base}/routes/admin.php',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->copyFiles($this->allFiles);

        $this->migrate();

        $this->login();
    }

    public function createWriters()
    {
        $this->assertEquals(0, Writer::count());

        $writers = collect(['Alice', 'Bob', 'Charlie'])->map(function ($name) {
            return app(WriterRepository::class)->create([
                'title' => $name,
                'published' => true,
            ]);
        });

        $this->assertEquals(3, Writer::count());

        return $writers;
    }

    public function createLetter()
    {
        $item = app(LetterRepository::class)->create([
            'title' => 'Lorem ipsum dolor sit amet',
            'published' => true,
        ]);

        $this->assertEquals(1, Letter::count());

        return $item;
    }

    public function createLetterWithWriters($writers)
    {
        $item = $this->createLetter();

        $this->httpRequestAssert("/twill/letters/{$item->id}", 'PUT', [
            'browsers' => [
                'writers' => $writers->map(function ($writer) {
                    return ['id' => $writer->id];
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

    public function createBioWithWriter($writer)
    {
        $item = $this->createBio();

        $this->httpRequestAssert("/twill/bios/{$item->id}", 'PUT', [
            'browsers' => [
                'writer' => [
                    ['id' => $writer->id],
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

    public function createBookWithWriters($writers)
    {
        $item = $this->createBook();

        $this->httpRequestAssert("/twill/books/{$item->id}", 'PUT', [
            'browsers' => [
                'writers' => $writers->map(function ($writer) {
                    return [
                        'id' => $writer->id,
                        'endpointType' => '\\App\\Models\\Writer',
                    ];
                }),
            ],
        ]);
        return $item;
    }

    // FIXME — this is needed for the new admin routes to take effect in the next test,
    // because files are copied in `setUp()` after the app is initialized.
    public function testDummy()
    {
        $this->assertTrue(true);
    }

    public function testBrowserBelongsToMany()
    {
        $writers = $this->createWriters();
        $letter = $this->createLetterWithWriters($writers);

        // User can attach writers
        $this->assertEquals(3, Letter::first()->writers->count());
        $this->assertEquals(
            $writers->pluck('id')->sort()->toArray(),
            Letter::first()->writers->pluck('id')->sort()->toArray()
        );
    }

    public function testBrowserBelongsToManyPreview()
    {
        $writers = $this->createWriters();
        $letter = $this->createLetterWithWriters($writers);

        // User can preview
        $this->httpRequestAssert("/twill/letters/preview/{$letter->id}", 'PUT', []);
        $this->assertSee('This is an letter');
    }

    public function testBrowserBelongsToManyPreviewRevisions()
    {
        $writers = $this->createWriters();
        $letter = $this->createLetterWithWriters($writers);

        // User can preview revisions
        $this->httpRequestAssert("/twill/letters/preview/{$letter->id}", 'PUT', [
            'revisionId' => Letter::first()->revisions->last()->id,
        ]);
        $this->assertSee('This is an letter');
    }

    public function testBrowserBelongsToManyRestoreRevisions()
    {
        $writers = $this->createWriters();
        $letter = $this->createLetterWithWriters($writers);

        // User can restore revisions
        $this->httpRequestAssert("/twill/letters/restoreRevision/{$letter->id}", 'GET', [
            'revisionId' => Letter::first()->revisions->last()->id,
        ]);
        $this->assertSee('You are currently editing an older revision of this content');
    }

    public function testBrowserBelongsTo()
    {
        $writers = $this->createWriters();
        $bio = $this->createBioWithWriter($writers[0]);

        // User can attach writers
        $this->assertNotEmpty(Bio::first()->writer);
        $this->assertEquals($writers[0]->id, Bio::first()->writer->id);
    }

    public function testBrowserBelongsToPreview()
    {
        $writers = $this->createWriters();
        $bio = $this->createBioWithWriter($writers[0]);

        // User can preview
        $this->httpRequestAssert("/twill/bios/preview/{$bio->id}", 'PUT', []);
        $this->assertSee('This is a bio');
    }

    public function testBrowserBelongsToPreviewRevisions()
    {
        $writers = $this->createWriters();
        $bio = $this->createBioWithWriter($writers[0]);

        // User can preview revisions
        $this->httpRequestAssert("/twill/bios/preview/{$bio->id}", 'PUT', [
            'revisionId' => Bio::first()->revisions->last()->id,
        ]);
        $this->assertSee('This is a bio');
    }

    public function testBrowserBelongsToRestoreRevisions()
    {
        $writers = $this->createWriters();
        $bio = $this->createBioWithWriter($writers[0]);

        // User can restore revisions
        $this->httpRequestAssert("/twill/bios/restoreRevision/{$bio->id}", 'GET', [
            'revisionId' => Bio::first()->revisions->last()->id,
        ]);
        $this->assertSee('You are currently editing an older revision of this content');
    }

    public function testBrowserRelated()
    {
        $writers = $this->createWriters();
        $book = $this->createBookWithWriters($writers);

        // User can attach writers
        $this->assertEquals(3, RelatedItem::count());
        $this->assertEquals(3, Book::first()->getRelated('writers')->count());
        $this->assertEquals(
            $writers->pluck('id')->sort()->toArray(),
            Book::first()->getRelated('writers')->pluck('id')->sort()->toArray()
        );
    }

    public function testBrowserRelatedPreview()
    {
        $writers = $this->createWriters();
        $book = $this->createBookWithWriters($writers);

        // User can preview
        $this->httpRequestAssert("/twill/books/preview/{$book->id}", 'PUT', []);
        $this->assertSee('This is a book');
    }

    public function testBrowserRelatedPreviewRevisions()
    {
        $writers = $this->createWriters();
        $book = $this->createBookWithWriters($writers);

        // User can preview revisions
        $this->httpRequestAssert("/twill/books/preview/{$book->id}", 'PUT', [
            'revisionId' => Book::first()->revisions->last()->id,
        ]);
        $this->assertSee('This is a book');
    }

    public function testBrowserRelatedRestoreRevisions()
    {
        $writers = $this->createWriters();
        $book = $this->createBookWithWriters($writers);

        // User can restore revisions
        $this->httpRequestAssert("/twill/books/restoreRevision/{$book->id}", 'GET', [
            'revisionId' => Book::first()->revisions->last()->id,
        ]);
        $this->assertSee('You are currently editing an older revision of this content');
    }
}
