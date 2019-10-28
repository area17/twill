<?php

namespace A17\Twill\Tests\Integration;

use App\Models\Author;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use App\Models\Revisions\AuthorRevision;
use App\Models\Translations\AuthorTranslation;

class ModulesAuthorsTest extends ModulesTestBase
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
    protected $birthday;
    protected $block_id;
    protected $block_quote;
    protected $translation;
    protected $author;

    protected function addBlock()
    {
        $this->request(
            "/twill/personnel/authors/{$this->author->id}",
            'PUT',
            $this->getUpdateAuthorWithBlock()
        )->assertStatus(200);

        $this->assertEquals(1, $this->author->blocks->count());

        $this->assertEquals(
            $block_quote = ['quote' => $this->block_quote],
            $this->author->blocks->first()->content
        );

        // Check if blocks are rendering
        $this->assertEquals(
            clean_file(json_encode($block_quote)),
            clean_file(trim($this->author->renderBlocks()))
        );

        // Get browser data
        $this->request('/twill/personnel/authors/browser')->assertStatus(200);

        $this->assertJson($this->content());

        $data = json_decode($this->content(), true)['data'][0];

        $this->assertEquals(
            $data['edit'],
            "http://twill.test/twill/personnel/authors/{$this->author->id}/edit"
        );

        $this->assertEquals($data['endpointType'], 'App\Models\Author');
    }

    protected function assertSomethingWrongHappened()
    {
        $this->assertSee('Something wrong happened!');
    }

    protected function assertNothingWrongHappened()
    {
        $this->assertDontSee('Something wrong happened!');
    }

    protected function createAuthor($count = 1)
    {
        foreach (range(1, $count) as $c) {
            $this->request(
                '/twill/personnel/authors',
                'POST',
                $this->getCreateAuthorData()
            )->assertStatus(200);
        }

        $this->translation = AuthorTranslation::where('name', $this->name_en)
            ->where('locale', 'en')
            ->first();

        $this->author = $this->translation->author;

        $this->assertNotNull($this->translation);

        $this->assertCount(3, $this->author->slugs);
    }

    protected function destroyAuthor()
    {
        $this->createAuthor();

        $this->assertNull($this->author->deleted_at);

        $this->request(
            "/twill/personnel/authors/{$this->author->id}",
            'DELETE',
            $this->getUpdateAuthorWithBlock()
        )->assertStatus(200);

        $this->assertNothingWrongHappened();

        $this->author->refresh();

        $this->assertNotNull($this->author->deleted_at);

        $this->request(
            '/twill/personnel/authors/9999999',
            'DELETE',
            $this->getUpdateAuthorWithBlock()
        )->assertStatus(404);
    }

    protected function editAuthor()
    {
        $this->request(
            "/twill/personnel/authors/{$this->author->id}",
            'PUT',
            $this->getUpdateAuthorData()
        )->assertStatus(200);

        $this->assertNothingWrongHappened();

        $this->author->refresh();

        $this->assertEquals($this->author->birthday, $this->birthday);

        $this->translation->refresh();

        $this->assertEquals(
            $this->description_en,
            $this->translation->description
        );

        $this->assertEquals($this->bio_en, $this->translation->bio);

        $this->assertTrue($this->translation->active);
    }

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
                'en' => ($this->description_en = '[EN] ' . $this->fakeText(80)),
                'fr' => ($this->description_fr = '[FR] ' . $this->fakeText(80)),
                'pt-BR' => '',
            ],
            'birthday' => ($this->birthday = now()->format('Y-m-d')),
            'bio' => [
                'en' => ($this->bio_en = '[EN] ' . $this->fakeText(255)),
                'fr' => ($this->bio_fr = '[FR] ' . $this->fakeText(255)),
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
            'repeaters' => [],
        ];
    }

    public function getUpdateAuthorWithBlock()
    {
        return $this->getUpdateAuthorData() + [
            'blocks' => [
                [
                    'id' => ($this->block_id = rand(
                        1570000000000,
                        1579999999999
                    )),
                    'type' => 'a17-block-quote',
                    'content' => [
                        'quote' => ($this->block_quote = $this->fakeText()),
                    ],
                    'medias' => [],
                    'browsers' => [],
                    'blocks' => [],
                ],
            ],
            'repeaters' => [],
        ];
    }

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

    public function testCanDisplayModuleInNavigation()
    {
        $this->request('/twill');

        $this->assertSee('Personnel');
        $this->assertSee('Categories');

        $this->request('/twill/personnel/authors');

        $this->assertSee('Name');
        $this->assertSee('Languages');
        $this->assertSee('Mine');
        $this->assertSee('Add new');
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
