<?php

namespace A17\Twill\Tests\Integration\Blocks;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Services\Forms\Fields\BlockEditor;
use A17\Twill\Services\Forms\Fields\Browser;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;
use A17\Twill\Tests\Integration\TestCase;
use A17\Twill\View\Components\Blocks\TwillBlockComponent;
use App\Models\Book;
use App\Models\Writer;
use App\Repositories\BookRepository;
use App\Repositories\WriterRepository;

class BlockBrowsersTest extends TestCase
{
    public ?string $example = 'tests-browsers';

    public function testBrowsersAreClearedWhenPostingSingle(): void
    {
        $module = AnonymousModule::make('blockbrowserstests', $this->app)
            ->withFormFields(Form::make([
                BlockEditor::make()
            ]))
            ->boot();

        $block = new class extends TwillBlockComponent {
            public static function getBlockIdentifier(): string
            {
                return 'test-block-browsers';
            }

            public function getForm(): Form
            {
                return Form::make([
                    Browser::make()
                        ->name('books')
                        ->modules([Book::class]),
                    Browser::make()
                        ->name('writers')
                        ->modules([Writer::class])
                ]);
            }

            public function render(): string
            {
                return 'hello world!';
            }
        };


        TwillBlocks::registerManualBlock($block::class);

        $book = app(BookRepository::class)->create([
            'title' => 'book title',
            'published' => true,
        ]);

        $writer = app(WriterRepository::class)->create([
            'title' => 'writer title',
            'published' => true,
        ]);

        $blocks = [
            'blocks' => [
                [
                    'browsers' => [
                        'writers' => [
                            [
                                'id' => $writer->id,
                                'endpointType' => '\\App\\Models\\Writer',
                            ]
                        ],
                        'books' => [
                            [
                                'id' => $book->id,
                                'endpointType' => '\\App\\Models\\Book',
                            ]
                        ]
                    ],
                    'medias' => [],
                    'blocks' => [],
                    'type' => 'a17-block-test-block-browsers',
                    'content' => [],
                    'id' => time(),
                ],
            ],
        ];

        $entity = $module->getRepository()->create([
            'title' => 'Hello world',
            'published' => true,
        ]);

        $update = $module->getRepository()->update($entity->id, $blocks)->refresh();

        $block = $update->blocks->first();

        $this->assertCount(1, $update->blocks);
        $this->assertCount(2, $block->relatedItems()->get());
        $this->assertEquals('writer title', $block->getRelated('writers')->first()->title);
        $this->assertEquals('book title', $block->getRelated('books')->first()->title);

        $blocks = [
            'blocks' => [
                [
                    'browsers' => [
                        'books' => [
                            [
                                'id' => $book->id,
                                'endpointType' => '\\App\\Models\\Book',
                            ]
                        ]
                    ],
                    'medias' => [],
                    'blocks' => [],
                    'type' => 'a17-block-test-block-browsers',
                    'content' => [],
                    'id' => $block->id,
                ],
            ],
        ];

        $update = $module->getRepository()->update($entity->id, $blocks)->refresh();

        $block = $update->blocks->first();

        $this->assertCount(1, $update->blocks);
        $this->assertCount(1, $block->relatedItems()->get());
        $this->assertEmpty($block->getRelated('writers'));
        $this->assertEquals('book title', $block->getRelated('books')->first()->title);
    }
}
