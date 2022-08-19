<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Repositories\ModuleRepository;
use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;

class BlockChildrenTest extends TestCase
{
    public function testSorting(): void
    {
        $module = AnonymousModule::make('servers', $this->app)
            ->boot();

        /** @var ModuleRepository $repository */
        $repository = app()->make($module->getModelController()->getRepositoryClass($module->getModelClassName()));

        $server = $repository->create([
            'title' => 'Hello world',
            'published' => true,
        ]);

        $blocks = [
            'blocks' => [
                [
                    'browsers' => [],
                    'medias' => [],
                    'blocks' => [
                        [
                            [
                                'browsers' => [],
                                'medias' => [],
                                'blocks' => [],
                                'type' => 'a17-block-quote',
                                'is_repeater' => false,
                                'position' => 2,
                                'content' => [
                                    'quote' => 'This is the nested quote at position 2.',
                                    'author' => 'This is the nested author at position 2.',
                                ],
                                'id' => time() + 1,
                            ],
                            [
                                'browsers' => [],
                                'medias' => [],
                                'blocks' => [],
                                'type' => 'a17-block-quote',
                                'is_repeater' => false,
                                'position' => 2,
                                'content' => [
                                    'quote' => 'This is the nested quote at position 1.',
                                    'author' => 'This is the nested author at position 1.',
                                ],
                                'id' => time() + 1,
                            ],
                        ],
                    ],
                    'type' => 'a17-block-quote',
                    'content' => [
                        'quote' => 'This is the quote.',
                        'author' => 'This is the author.',
                    ],
                    'id' => time(),
                ],
            ],
        ];

        $update = $repository->update($server->id, $blocks);

        $this->assertEquals('This is the quote.', $update->blocks[0]->content['quote']);
        $this->assertEquals('This is the nested quote at position 1.', $update->blocks[1]->content['quote']);
        $this->assertEquals('This is the nested quote at position 2.', $update->blocks[2]->content['quote']);
    }
}
