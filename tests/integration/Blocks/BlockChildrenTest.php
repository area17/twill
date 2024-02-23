<?php

namespace A17\Twill\Tests\Integration\Blocks;

use A17\Twill\Repositories\ModuleRepository;
use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;
use A17\Twill\Tests\Integration\TestCase;

class BlockChildrenTest extends TestCase
{
    public function testBCOfBlockSchema(): void
    {
        $module = AnonymousModule::make('childrenservers', $this->app)
            ->boot();

        /** @var ModuleRepository $repository */
        $repository = app()->make($module->getRepositoryClassName());

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
                                'content' => [
                                    'quote' => 'This is the nested quote at position 1.',
                                    'author' => 'This is the nested author at position 1.',
                                ],
                                'id' => time() + 2,
                            ],
                        ]
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

        // Check the nested child order.
        $this->assertEquals(
            'This is the nested quote at position 2.',
            $update->blocks[0]->children[0]->content['quote']
        );
        $this->assertEquals(
            'This is the nested quote at position 1.',
            $update->blocks[0]->children[1]->content['quote']
        );
    }

    public function testSorting(): void
    {
        $module = AnonymousModule::make('childrenservers', $this->app)
            ->boot();

        /** @var ModuleRepository $repository */
        $repository = app()->make($module->getRepositoryClassName());

        $server = $repository->create([
            'title' => 'Hello world',
            'published' => true,
        ]);

        $blocks = [
            'blocks' => [
                [
                    'editor_name' => 'description',
                    'browsers' => [],
                    'medias' => [],
                    'blocks' => [
                        [
                            'browsers' => [],
                            'medias' => [],
                            'blocks' => [],
                            'child_key' => 'content',
                            'editor_name' => 'blocks-yada-1|content',
                            'type' => 'a17-block-quote',
                            'is_repeater' => false,
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
                            'child_key' => 'content',
                            'editor_name' => 'blocks-yada-1|content',
                            'type' => 'a17-block-quote',
                            'is_repeater' => false,
                            'content' => [
                                'quote' => 'This is the nested quote at position 1.',
                                'author' => 'This is the nested author at position 1.',
                            ],
                            'id' => time() + 2,
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

        // Check the nested child order.
        $this->assertEquals(
            'This is the nested quote at position 2.',
            $update->blocks[0]->children[0]->content['quote']
        );
        $this->assertEquals(
            'This is the nested quote at position 1.',
            $update->blocks[0]->children[1]->content['quote']
        );

        // Now we update it a second time, but we update the order.
        $blocksUpdate = $blocks;
        $blocksUpdate['blocks'][0]['blocks'][0]['id'] = $update->blocks[1]->id;
        $blocksUpdate['blocks'][0]['blocks'][1]['id'] = $update->blocks[2]->id;

        // We now swap them so their actual position is correct.
        $backup = $blocksUpdate['blocks'][0]['blocks'][0];

        $blocksUpdate['blocks'][0]['blocks'][0] = $blocksUpdate['blocks'][0]['blocks'][1];
        $blocksUpdate['blocks'][0]['blocks'][1] = $backup;

        $update = $repository->update($server->id, $blocksUpdate);

        // Check the nested child order.
        $this->assertEquals('This is the quote.', $update->blocks[0]->content['quote']);
        $this->assertEquals(
            'This is the nested quote at position 1.',
            $update->blocks[0]->children[0]->content['quote']
        );
        $this->assertEquals(
            'This is the nested quote at position 2.',
            $update->blocks[0]->children[1]->content['quote']
        );
    }
}
