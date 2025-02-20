<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Facades\TwillConfig;
use App\Models\RevisionLimited;
use App\Repositories\RevisionLimitedRepository;

class ModuleLimitRevisionsTest extends ModulesTestBase
{
    public function testSetupRoutes(): void
    {
        $this->assertTrue(true);
    }

    public function testWithoutLimit(): void
    {
        $this->createAuthor();
        for ($i = 0; $i < 9; $i++) {
            $this->editAuthor();
        }

        $this->assertEquals(10, $this->author->revisions()->count());
    }

    public function testRevisionLimit(): void
    {
        TwillConfig::maxRevisions(5);

        $this->createAuthor();
        for ($i = 0; $i < 9; $i++) {
            $this->editAuthor();
        }

        $this->assertEquals(5, $this->author->revisions()->count());
    }

    public function testRevisionLimitHigherNumber(): void
    {
        TwillConfig::maxRevisions(13);

        $this->createAuthor();
        for ($i = 0; $i < 9; $i++) {
            $this->editAuthor();
        }

        $this->assertEquals(10, $this->author->revisions()->count());
    }

    public function testRevisionLimitOnModel(): void
    {
        TwillConfig::maxRevisions(10);

        $model = RevisionLimited::create(['title' => 'test']);

        /** @var RevisionLimitedRepository $repo */
        $repo = app(RevisionLimitedRepository::class);

        for ($i = 0; $i < 15; $i++) {
            $repo->update($model->id, ['title' => 'title' . $i]);
        }

        // Our model is revision limited to 5 using $limitRevisions.
        $this->assertEquals(5, $model->revisions()->count());
    }

    public function testRevisionLimitOnModelWithoutGlobalConfig(): void
    {
        $this->assertNull(TwillConfig::getRevisionLimit());

        // Our model is revision limited to 5.
        $model = RevisionLimited::create(['title' => 'test']);

        /** @var RevisionLimitedRepository $repo */
        $repo = app(RevisionLimitedRepository::class);

        for ($i = 0; $i < 15; $i++) {
            $repo->update($model->id, ['title' => 'title' . $i]);
        }

        $this->assertEquals(5, $model->revisions()->count());
    }
}
