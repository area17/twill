<?php

namespace A17\Twill\Tests\Integration;

use App\Models\Letter;
use App\Models\Writer;
use App\Repositories\LetterRepository;
use App\Repositories\WriterRepository;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;

class NplusOneRelatedTest extends TestCase
{
    public ?string $example = 'tests-browsers';

    public function setUp(): void
    {
        parent::setUp();

        $this->login();
    }

    public function testSingleRelatedItem(): void
    {
        $letter = app(LetterRepository::class)->create([
            'title' => 'shorterTitle',
            'published' => true,
        ]);

        $storeArray = [];

        for ($i = 0; $i < 10; $i++) {
            $writer = app(WriterRepository::class)->create([
                'title' => 'writer',
                'published' => true,
            ]);

            $storeArray[] = ['endpointType' => Writer::class, 'id' => $writer->id];
        }

        $letter->saveRelated($storeArray, 'dummyBrowser');

        $counter = 0;

        DB::listen(function (QueryExecuted $query) use (&$counter) {
            $counter++;
        });

        $this->assertEquals(0, $counter);

        $letter = Letter::findOrFail($letter->id);

        $this->assertEquals(1, $counter);

        $counter = 0;

        $letter = Letter::with('relatedItems')->findOrFail($letter->id);

        $this->assertEquals(2, $counter);

        // No matter how many times we load the related, it should not increase the query count beyond 12.
        // 1 for the base model above
        // 1 for the relatedItems
        // 1 for each related item (10 in total)
        $letter->getRelated('dummyBrowser');
        $letter->getRelated('dummyBrowser');
        $letter->getRelated('dummyBrowser');

        $this->assertEquals(12, $counter);
    }
}
