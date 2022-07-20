<?php

namespace A17\Twill\Tests\Integration;

use App\Models\Author;
use App\Repositories\AuthorRepository;
use Carbon\Carbon;

class RevisionsTest extends ModulesTestBase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function addMinutes($minutes = 1)
    {
        Carbon::setTestNow($this->now->addMinutes($minutes));
    }

    public function createAuthorWithRevisions()
    {
        $this->addMinutes(1);

        $author = $this->createSingleAuthor();

        $this->addMinutes(1);

        $this->updateAuthor($author, [
            'name' => ['en' => 'Bobby', 'fr' => 'Bobby'],
            'published' => false,
        ]);

        $this->addMinutes(1);

        $this->updateAuthor($author, [
            'name' => ['en' => 'Bobby', 'fr' => 'Bobby'],
            'published' => true,
        ]);

        $this->addMinutes(1);

        return $author;
    }

    public function createSingleAuthor(array $fields = []): Author
    {
        $defaults = [
            'name' => ['en' => 'Bob', 'fr' => 'Bob'],
            'published' => false,
        ];

        return app(AuthorRepository::class)->create(
            array_merge($defaults, $fields)
        );
    }

    public function updateAuthor($author, $fields = [])
    {
        app(AuthorRepository::class)->update($author->id, $fields);

        return $author->refresh();
    }

    public function getRevisionLabels($item)
    {
        return collect($item->refresh()->revisionsArray())
            ->pluck('label')
            ->toArray();
    }

    public function getRevisionPayloads($item)
    {
        $revisions = $item->refresh()->revisions;

        return collect($revisions)->map(function ($revision) {
            return json_decode($revision->payload, true);
        });
    }

    public function test_module_has_revisions()
    {
        $author = $this->createAuthorWithRevisions();

        $this->assertEquals(3, $author->revisions()->count());
    }

    public function test_update_modifies_published_record()
    {
        $author = $this->createAuthorWithRevisions();

        $this->httpRequestAssert("/twill/personnel/authors/{$author->id}", 'PUT', [
            'name' => ['en' => 'Test', 'fr' => 'Test'],
            'published' => true,
        ]);

        $author->refresh();
        $this->assertTrue((bool) $author->published);
        $this->assertEquals('Test', $author->name);
    }

    public function test_latest_revision_is_identified_as_current()
    {
        $author = $this->createSingleAuthor();

        $this->assertEquals(['Current'], $this->getRevisionLabels($author));

        $this->updateAuthor($author, [
            'name' => ['en' => 'Bobby', 'fr' => 'Bobby'],
            'published' => false,
        ]);

        $this->assertEquals(['Current', ''], $this->getRevisionLabels($author));

        $this->updateAuthor($author, [
            'name' => ['en' => 'Bobby', 'fr' => 'Bobby'],
            'published' => true,
        ]);

        $this->assertEquals(['Current', '', ''], $this->getRevisionLabels($author));
    }

    public function test_can_create_draft_revisions()
    {
        putenv('ENABLE_DRAFT_REVISIONS=true');

        $author = $this->createAuthorWithRevisions();

        $this->httpRequestAssert("/twill/personnel/authors/{$author->id}", 'PUT', [
            'name' => ['en' => 'Test', 'fr' => 'Test'],
            'published' => true,
            'cmsSaveType' => 'draft-revision',
        ]);

        $this->assertEquals(4, $author->revisions()->count());
    }

    public function test_draft_revision_does_not_modify_published_record()
    {
        putenv('ENABLE_DRAFT_REVISIONS=true');

        $author = $this->createAuthorWithRevisions();

        $this->httpRequestAssert("/twill/personnel/authors/{$author->id}", 'PUT', [
            'name' => ['en' => 'Test', 'fr' => 'Test'],
            'published' => true,
            'cmsSaveType' => 'draft-revision',
        ]);

        $author->refresh();
        $this->assertTrue((bool) $author->published);
        $this->assertNotEquals('Test', $author->name);
    }

    public function test_draft_revision_is_saved_as_draft()
    {
        putenv('ENABLE_DRAFT_REVISIONS=true');

        $author = $this->createAuthorWithRevisions();

        $this->httpRequestAssert("/twill/personnel/authors/{$author->id}", 'PUT', [
            'name' => ['en' => 'Test', 'fr' => 'Test'],
            'published' => true,
            'cmsSaveType' => 'draft-revision',
        ]);

        $revisionData = $this->getRevisionPayloads($author)->first();
        $this->assertEquals('draft-revision', $revisionData['cmsSaveType']);
        $this->assertFalse((bool) $revisionData['published']);
    }

    public function test_draft_revision_is_not_identified_as_current()
    {
        putenv('ENABLE_DRAFT_REVISIONS=true');

        $author = $this->createAuthorWithRevisions();

        $this->httpRequestAssert("/twill/personnel/authors/{$author->id}", 'PUT', [
            'name' => ['en' => 'Test', 'fr' => 'Test'],
            'published' => true,
            'cmsSaveType' => 'draft-revision',
        ]);

        $this->assertEquals(['', 'Current', '', ''], $this->getRevisionLabels($author));
    }
}
