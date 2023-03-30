<?php

namespace A17\Twill\Tests\Integration;

use App\Models\Author;
use App\Models\Category;
use A17\Twill\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use App\Models\Revisions\AuthorRevision;

class ModulesAuthorsTest extends ModulesTestBase
{
    public function testCanCopyFiles()
    {
        collect($this->allFiles)->each(function ($destination, $source) {
            $this->assertFileExists($this->makeFileName($destination, $source));
        });
    }

    public function testCanMigrateDatabase()
    {
        $this->assertTrue(Schema::hasTable('authors'));
        $this->assertTrue(Schema::hasTable('author_translations'));
        $this->assertTrue(Schema::hasTable('author_slugs'));
        $this->assertTrue(Schema::hasTable('author_revisions'));
    }

    public function testCanCreateAnAuthor()
    {
        $this->createAuthor();
    }

    public function testCanEditAuthor()
    {
        $this->createAuthor();

        $this->editAuthor();
    }

    public function testCanAddBlock()
    {
        $this->createAuthor();

        $this->editAuthor();

        $this->addBlock();
    }

    public function testCanRedirectAuthorsToEdit()
    {
        $this->createAuthor();

        $this->httpRequestAssert(
            "/twill/personnel/authors/{$this->author->id}"
        );

        $this->assertSee($this->description_en);
    }

    public function testCanDisplayDashboard()
    {
        $this->httpRequestAssert('/twill');

        $this->assertSee('Personnel');
        $this->assertSee('Categories');

        $this->httpRequestAssert('/twill/personnel/authors');

        $this->assertSee('Name');
        $this->assertSee('Languages');
        $this->assertSee('Mine');
        $this->assertSee('Add new');

        $this->httpRequestAssert('/twill/categories');
    }

    public function testCanSearchString()
    {
        $this->createAuthor(3);

        $this->ajax("/twill/search?search={$this->name_en}")->assertStatus(200);

        $this->assertJson($this->content());

        $result = json_decode($this->content(), true);

        $this->assertGreaterThan(0, count($result));

        $this->assertEquals(
            $this->now->format('Y-m-d\TH:i:s+00:00'),
            $result[0]['date']
        );
    }

    public function testCanStartRestoringRevision()
    {
        $this->createAuthor();
        $this->editAuthor();
        $this->addBlock();

        // Check revisions
        $this->assertCount(3, AuthorRevision::all());

        // Restore revision 1
        $first = AuthorRevision::first();
        $last = AuthorRevision::all()->last();

        $this->httpRequestAssert(
            "/twill/personnel/authors/restoreRevision/{$first->id}",
            'GET',
            ['revisionId' => $last->id]
        );

        $this->assertSee(
            'You are currently editing an older revision of this content'
        );
    }

    public function testCanPublishAuthor()
    {
        $this->createAuthor();

        // Publishing
        $this->assertEquals('0', $this->author->published);

        $this->httpRequestAssert('/twill/personnel/authors/publish', 'PUT', [
            'id' => $this->author->id,
            'active' => false,
        ]);

        $this->assertNothingWrongHappened();

        $this->author->refresh();

        $this->assertEquals('1', $this->author->published);
    }

    public function testCanDisplayErrorWhenPublishHasWrongData()
    {
        $this->httpRequestAssert('/twill/personnel/authors/publish', 'PUT');

        $this->assertSomethingWrongHappened();
    }

    public function testCanRaiseHttpNotFoundOnAnEmptyRestoreRevision()
    {
        $this->httpRequestAssert(
            '/twill/personnel/authors/restoreRevision/1',
            'GET',
            [],
            404
        );
    }

    public function testCanPreviewBlock()
    {
        $data = [
            'id' => 1,
            'type' => 'a17-block-quote',
            'content' => [
                'quote' => ($quote = $this->fakeText(70)),
            ],
            'medias' => [],
            'browsers' => [],
            'blocks' => [],
            'activeLanguage' => 'en',
        ];

        $this->httpRequestAssert('/twill/blocks/preview', 'POST', $data);

        $this->assertSee(json_encode(['quote' => $quote]));
    }

    public function testCanPreviewAuthor()
    {
        $this->createAuthor();

        $this->httpRequestAssert(
            "/twill/personnel/authors/preview/{$this->author->id}",
            'PUT'
        );

        $this->assertSee(
            'Previews have not been configured on this Twill module, please let the development team know about it.'
        );

        $this->files->copy(
            $this->makeFileName(
                '{$stubs}/modules/authors/site.author.blade.php'
            ),
            $this->makeFileName('{$resources}/views/site/author.blade.php')
        );

        $this->httpRequestAssert(
            "/twill/personnel/authors/preview/{$this->author->id}",
            'PUT',
            ['activeLanguage' => 'en']
        );

        $this->assertDontSee(
            'Previews have not been configured on this Twill module, please let the development team know about it.'
        );
    }

    public function testCanDestroyAuthor()
    {
        $this->destroyAuthor();
    }

    public function testCanRestoreAuthor()
    {
        $this->destroyAuthor();

        $this->assertNotNull($this->author->deleted_at);

        $this->httpRequestAssert('/twill/personnel/authors/restore', 'PUT', [
            'id' => $this->author->id,
        ]);

        $this->assertNothingWrongHappened();

        $this->author->refresh();

        $this->assertNull($this->author->deleted_at);
    }

    public function testCanReturnErrorWhenRestoringWrongAuthor()
    {
        $this->httpRequestAssert('/twill/personnel/authors/restore', 'PUT', [
            'id' => 999999,
        ]);

        $this->assertSomethingWrongHappened();
    }

    public function testCanFeatureAuthor()
    {
        $this->createAuthor(2);

        $this->assertFalse($this->author->featured);

        $this->httpRequestAssert('/twill/personnel/authors/feature', 'PUT', [
            'id' => $this->author->id,
            'active' => false,
        ]);

        $this->assertNothingWrongHappened();

        $this->author->refresh();

        $this->assertTrue($this->author->featured);
    }

    public function testCanReturnErrorWhenFeaturingWrongAuthor()
    {
        $this->httpRequestAssert('/twill/personnel/authors/feature', 'PUT', [
            'id' => 999999,
            'active' => true,
        ], 404);
    }

    public function testCanChangeOrder()
    {
        $this->createAuthor(2);

        $author1 = Author::ordered()
            ->get()
            ->first();

        $author2 = Author::ordered()
            ->get()
            ->first();

        $this->assertEquals(1, $author1->position);

        $this->httpRequestAssert('/twill/personnel/authors/reorder', 'POST', [
            'ids' => [$author1->id, $author2->id],
        ]);

        $this->assertNothingWrongHappened();

        $author1->refresh();

        $this->assertEquals(2, $author1->position);
    }

    public function testCanReturnWhenReorderingWrongAuthor()
    {
        /**
         * Should not return Error 500
         *
         * TODO
         */

        $this->httpRequestAssert('/twill/personnel/authors/reorder', 'POST', [
            'ids' => [1, 2],
        ], 500);
    }

    public function testCanGetTags()
    {
        $this->httpRequestAssert('/twill/personnel/authors/tags');

        $this->assertJson($this->content());
    }

    public function testCanShowAuthorsIndex()
    {
        $this->createAuthor(5);

        $this->ajax('/twill/personnel/authors')->assertStatus(200);

        $this->assertJson($this->content());

        $this->assertEquals(
            5,
            count(json_decode($this->content(), true)['tableData'])
        );
    }

    public function testCanShowAuthorRelationshipColumn()
    {
        $this->createAuthor(1);
        $author = Author::first();

        $this->createCategory(2);
        $categories = Category::all();

        $author->categories()->attach($categories);

        $this->ajax('/twill/personnel/authors')->assertStatus(200);

        $content = json_decode($this->content(), true);

        $this->assertEquals(
            $content['tableData'][0]['categoriesTitle'],
            $categories->pluck('title')->join(', ')
        );
    }

    public function testCanShowEditForm()
    {
        $this->createAuthor();
        $this->editAuthor();

        $this->httpRequestAssert(
            "/twill/personnel/authors/{$this->author->id}/edit"
        );

        $this->assertSee($this->name_en);
        $this->assertSee($this->description_en);
        $this->assertSee($this->bio_en);
    }

    public function testCanShowEditFormViaAjax()
    {
        putenv('EDIT_IN_MODAL=true');

        $this->createAuthor();
        $this->editAuthor();

        $this->ajax(
            "/twill/personnel/authors/{$this->author->id}/edit"
        )->assertStatus(200);

        $this->assertSee($this->name_en);
        $this->assertSee($this->description_en);
        $this->assertSee($this->bio_en);
    }

    public function testCanShowEditFormInModal()
    {
        $this->createAuthor();
        $this->editAuthor();

        putenv('EDIT_IN_MODAL=true');

        $this->httpRequestAssert(
            "/twill/personnel/authors/{$this->author->id}/edit"
        );

        $this->assertSee('v-svg symbol="close_modal"');
    }

    public function testCanSeeRenderedBlocks()
    {
        $this->createAuthor();
        $this->editAuthor();

        putenv('EDIT_IN_MODAL=false');

        $this->httpRequestAssert(
            "/twill/personnel/authors/{$this->author->id}/edit"
        );

        // Check if it can see a rendered block
        $this->assertSee(
            '<script*type="text/x-template"*id="a17-block-quote">'
        );
    }

    public function testCannotHaveDuplicateSlugs(): void
    {
        $dataItem1 = $this->getCreateAuthorData();
        $dataItem1['languages'][1]['published'] = true;

        $slugEn = Str::slug($dataItem1['name']['en']);
        $slugFr = Str::slug($dataItem1['name']['fr']);

        $this->httpRequestAssert(
            '/twill/personnel/authors',
            'POST',
            $dataItem1
        );

        $this->httpRequestAssert(
            '/twill/personnel/authors',
            'POST',
            $dataItem1
        );

        $item1 = Author::first();

        $this->assertEquals($slugEn, $item1->slug);
        $this->assertEquals($slugFr, $item1->getSlug('fr'));

        $item2 = Author::orderBy('id', 'desc')->first();

        $this->assertEquals($slugEn . '-2', $item2->slug);
        $this->assertEquals($slugFr . '-2', $item2->getSlug('fr'));
    }

    public function testCanCastDates()
    {
        $this->createAuthor();

        $author = Author::first();

        $author->deleted_at = '2020-01-01';
        $this->assertInstanceOf(Carbon::class, $author->deleted_at);

        if (app()->version() >= '10') {
            $author->test_date_casts = '2020-01-02';
            $this->assertInstanceOf(Carbon::class, $author->test_date_casts);
        } else {
            $author->test_date_dates = '2020-01-02';
            $this->assertInstanceOf(Carbon::class, $author->test_date_dates);
        }

        $author->not_a_date = '2020-01-02';
        $this->assertIsString($author->not_a_date);
        $this->assertEquals('2020-01-02', $author->not_a_date);
    }

    public function testCanGetDates()
    {
        $this->createAuthor();

        $author = Author::first();

        $dates = array_values(Arr::sort($author->getDates()));

        $expected = array_values(Arr::sort([
            'created_at',
            'updated_at',
            'deleted_at',
            'test_date_dates',
            'test_date_casts',
        ]));

        $this->assertEquals($dates, $expected);

        $user = User::where('email', $this->superAdmin()->email)->first();

        $dates = array_values(Arr::sort($user->getDates()));

        $expected = array_values(Arr::sort([
            'created_at',
            'updated_at',
            'deleted_at',
        ]));

        $this->assertEquals($expected, $dates);
    }
}
