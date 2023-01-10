<?php

namespace A17\Twill\Tests\Integration\Revisions;

use A17\Twill\Tests\Integration\ModulesTestBase;
use App\Models\Author;
use App\Repositories\AuthorRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

abstract class RevisionTestBase extends ModulesTestBase
{
    public function addMinute(): void
    {
        Carbon::setTestNow($this->now->addMinute());
    }

    public function createAuthorWithRevisions(): Author
    {
        $this->addMinute();

        $author = $this->createSingleAuthor();

        $this->addMinute();

        $this->updateAuthor($author, [
            'name' => ['en' => 'Bobby', 'fr' => 'Bobby'],
            'published' => false,
        ]);

        $this->addMinute();

        $this->updateAuthor($author, [
            'name' => ['en' => 'Bobby', 'fr' => 'Bobby'],
            'published' => true,
        ]);

        $this->addMinute();

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

    public function updateAuthor($author, $fields = []): Author
    {
        app(AuthorRepository::class)->update($author->id, $fields);

        return $author->refresh();
    }

    public function getRevisionLabels($item): array
    {
        return collect($item->refresh()->revisionsArray())
            ->pluck('label')
            ->toArray();
    }

    public function getRevisionPayloads($item): Collection
    {
        $revisions = $item->refresh()->revisions;

        return collect($revisions)->map(function ($revision) {
            return json_decode($revision->payload, true);
        });
    }
}
