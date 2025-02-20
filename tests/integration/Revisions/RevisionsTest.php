<?php

namespace A17\Twill\Tests\Integration\Revisions;

class RevisionsTest extends RevisionTestBase
{
    public function test_module_has_revisions(): void
    {
        $author = $this->createAuthorWithRevisions();

        $this->assertEquals(3, $author->revisions()->count());
    }

    public function test_update_modifies_published_record(): void
    {
        $author = $this->createAuthorWithRevisions();

        $this->httpRequestAssert("/twill/personnel/authors/{$author->id}", 'PUT', [
            'name' => ['en' => 'Test', 'fr' => 'Test'],
            'published' => true,
        ]);

        $author->refresh();
        $this->assertTrue((bool)$author->published);
        $this->assertEquals('Test', $author->name);
    }

    public function test_latest_revision_is_identified_as_current(): void
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

    public function test_can_create_draft_revisions(): void
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

    public function test_draft_revision_does_not_modify_published_record(): void
    {
        putenv('ENABLE_DRAFT_REVISIONS=true');

        $author = $this->createAuthorWithRevisions();

        $this->httpRequestAssert("/twill/personnel/authors/{$author->id}", 'PUT', [
            'name' => ['en' => 'Test', 'fr' => 'Test'],
            'published' => true,
            'cmsSaveType' => 'draft-revision',
        ]);

        $author->refresh();
        $this->assertTrue((bool)$author->published);
        $this->assertNotEquals('Test', $author->name);
    }

    public function test_draft_revision_is_saved_as_draft(): void
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
        $this->assertFalse((bool)$revisionData['published']);
    }

    public function test_draft_revision_is_not_identified_as_current(): void
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
