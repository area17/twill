<?php

namespace A17\Twill\Tests\Integration;

use App\Models\Author;
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

        $this->request(
            "/twill/personnel/authors/{$this->author->id}"
        )->assertStatus(200);

        $this->assertSee($this->description_en);
    }

    public function testCanDisplayDashboard()
    {
        $this->request('/twill')->assertStatus(200);

        $this->assertSee('Personnel');
        $this->assertSee('Categories');

        $this->request('/twill/personnel/authors')->assertStatus(200);

        $this->assertSee('Name');
        $this->assertSee('Languages');
        $this->assertSee('Mine');
        $this->assertSee('Add new');

        $this->request('/twill/categories')->assertStatus(200);
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

        $this->request(
            "/twill/personnel/authors/restoreRevision/{$first->id}",
            'GET',
            ['revisionId' => $last->id]
        )->assertStatus(200);

        $this->assertSee(
            'You are currently editing an older revision of this content'
        );
    }

    public function testCanPublishAuthor()
    {
        $this->createAuthor();

        // Publishing
        $this->assertEquals('0', $this->author->published);

        $this->request('/twill/personnel/authors/publish', 'PUT', [
            'id' => $this->author->id,
            'active' => false,
        ])->assertStatus(200);

        $this->assertNothingWrongHappened();

        $this->author->refresh();

        $this->assertEquals('1', $this->author->published);
    }

    public function testCanDisplayErrorWhenPublishHasWrongData()
    {
        $this->request('/twill/personnel/authors/publish', 'PUT')->assertStatus(
            200
        );

        $this->assertSomethingWrongHappened();
    }

    public function testCanRaiseHttpNotFoundOnAnEmptyRestoreRevision()
    {
        $this->request(
            '/twill/personnel/authors/restoreRevision/1'
        )->assertStatus(404);
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

        $this->request('/twill/blocks/preview', 'POST', $data)->assertStatus(
            200
        );

        $this->assertSee(json_encode(['quote' => $quote]));
    }

    public function testCanPreviewAuthor()
    {
        $this->createAuthor();

        $this->request(
            "/twill/personnel/authors/preview/{$this->author->id}",
            'PUT'
        )->assertStatus(200);

        $this->assertSee(
            'Previews have not been configured on this Twill module, please let the development team know about it.'
        );

        $this->files->copy(
            $this->makeFileName(
                '{$stubs}/modules/authors/site.author.blade.php'
            ),
            $this->makeFileName('{$resources}/views/site/author.blade.php')
        );

        $this->request(
            "/twill/personnel/authors/preview/{$this->author->id}",
            'PUT',
            ['activeLanguage' => 'en']
        )->assertStatus(200);

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

        $this->request('/twill/personnel/authors/restore', 'PUT', [
            'id' => $this->author->id,
        ])->assertStatus(200);

        $this->assertNothingWrongHappened();

        $this->author->refresh();

        $this->assertNull($this->author->deleted_at);
    }

    public function testCanReturnErrorWhenRestoringWrongAuthor()
    {
        $this->request('/twill/personnel/authors/restore', 'PUT', [
            'id' => 999999,
        ])->assertStatus(200);

        $this->assertSomethingWrongHappened();
    }

    public function testCanFeatureAuthor()
    {
        $this->createAuthor(2);

        $this->assertFalse($this->author->featured);

        $this->request('/twill/personnel/authors/feature', 'PUT', [
            'id' => $this->author->id,
            'active' => false,
        ])->assertStatus(200);

        $this->assertNothingWrongHappened();

        $this->author->refresh();

        $this->assertTrue($this->author->featured);
    }

    public function testCanReturnErrorWhenFeaturingWrongAuthor()
    {
        $this->request('/twill/personnel/authors/feature', 'PUT', [
            'id' => 999999,
            'active' => true,
        ])->assertStatus(404);
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

        $this->request('/twill/personnel/authors/reorder', 'POST', [
            'ids' => [$author1->id, $author2->id],
        ])->assertStatus(200);

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

        $this->request('/twill/personnel/authors/reorder', 'POST', [
            'ids' => [1, 2],
        ])->assertStatus(500);
    }

    public function testCanGetTags()
    {
        $this->request('/twill/personnel/authors/tags')->assertStatus(200);

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

    public function testCanShowEditForm()
    {
        $this->createAuthor();
        $this->editAuthor();

        $this->request(
            "/twill/personnel/authors/{$this->author->id}/edit"
        )->assertStatus(200);

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

        $this->request(
            "/twill/personnel/authors/{$this->author->id}/edit"
        )->assertStatus(200);

        $this->assertSee('v-svg symbol="close_modal"');
    }
}
