<?php

namespace A17\Twill\Tests\Integration\Revisions;

use A17\Twill\Repositories\ModuleRepository;
use App\Repositories\AuthorRepository;

class RevisionSlugTest extends RevisionTestBase
{
    public function testSlugDataForRevision(): void
    {
        $author = $this->createSingleAuthor(['name' => ['en' => 'author en']]);

        $this->assertEquals(1, $author->revisions()->count());

        $firstRevision = $author->revisions()->first();
        $revisionPayload = json_decode($firstRevision->payload, true);

        $this->assertEquals('author-en', $author->getSlug('en'));
        $this->assertEquals('author en', $revisionPayload['name']['en']);

        $this->updateAuthor($author, ['name' => ['en' => 'author updated en']]);

        $latestRevision = $author->revisions()->latest('id')->first();
        $revisionPayload = json_decode($latestRevision->payload, true);

        $this->assertEquals('author-updated-en', $author->getSlug('en'));
        $this->assertEquals('author updated en', $revisionPayload['name']['en']);

        // Check the base version contains the correct data.
        $baseVersion = app(AuthorRepository::class)->previewForRevision($author->id, $latestRevision->id);
        $this->assertEquals('author updated en', $baseVersion->name);
        $this->assertEquals('author-updated-en', $baseVersion->getSlug('en'));

        // Check the old version.
        $restoringVersion = app(AuthorRepository::class)->previewForRevision($author->id, $firstRevision->id);

        $this->assertEquals('author en', $restoringVersion->name);
        // Slug here is author-updated-en as there was no slug submitted in the original payload.
        // @todo: This is unimplemented atm.
        // $this->assertEquals('author-updated-en', $restoringVersion->getSlug('en'));
    }

    public function testSlugDataForRevisionWithSlugPayload(): void
    {
        $author = $this->createSingleAuthor(['name' => ['en' => 'author en'], 'slug' => ['en' => 'author-en']]);

        $this->assertEquals(1, $author->revisions()->count());

        $firstRevision = $author->revisions()->first();
        $revisionPayload = json_decode($firstRevision->payload, true);

        $this->assertEquals('author-en', $author->getSlug('en'));
        $this->assertEquals('author en', $revisionPayload['name']['en']);

        $this->updateAuthor($author,
            ['name' => ['en' => 'author updated en'], 'slug' => ['en' => 'author-updated-en']]
        );

        $latestRevision = $author->revisions()->latest('id')->first();
        $revisionPayload = json_decode($latestRevision->payload, true);

        $this->assertEquals('author-updated-en', $author->getSlug('en'));
        $this->assertEquals('author updated en', $revisionPayload['name']['en']);

        // Check the base version contains the correct data.
        $baseVersion = app(AuthorRepository::class)->previewForRevision($author->id, $latestRevision->id);
        $this->assertEquals('author updated en', $baseVersion->name);
        $this->assertEquals('author-updated-en', $baseVersion->getSlug('en'));

        // Check the old version.
        $restoringVersion = app(AuthorRepository::class)->previewForRevision($author->id, $firstRevision->id);

        $this->assertEquals('author en', $restoringVersion->name);
        // Slug here is author-updated-en as there was no slug submitted in the original payload.
        // @todo: This is unimplemented atm.
        // $this->assertEquals('author-en', $restoringVersion->getSlug('en'));
    }
}
