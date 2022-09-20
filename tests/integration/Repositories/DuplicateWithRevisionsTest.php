<?php

namespace A17\Twill\Tests\Integration\Repositories;

use A17\Twill\Facades\TwillUtil;
use A17\Twill\Models\Block;
use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;
use A17\Twill\Tests\Integration\Behaviors\FileTools;
use A17\Twill\Tests\Integration\TestCase;

class DuplicateWithRevisionsTest extends TestCase
{
    use FileTools;

    public function setUp(): void
    {
        parent::setUp();
        config()->set('translatable.locales', ['en']);
    }

    public function testSimpleDuplicateContent(): void
    {
        $module = AnonymousModule::make('d_leaves', $this->app)
            ->withRevisions()
            ->withFields(['title' => ['translatable' => true]])
            ->boot();

        $model = $module->getRepository()->create([
            'title' => ['en' => 'English title'],
            'active' => ['en' => true],
        ]);

        $this->assertEquals('English title', $model->title);

        $module->getRepository()->duplicate($model->id);

        $this->assertEquals('English title', $model->title);

        $this->assertCount(2, $module->getModelClassName()::get());
    }

    public function testDuplicateWithRelated(): void
    {
        $browserModule = AnonymousModule::make('x_apps', $this->app)
            ->withFields(['title'])
            ->boot();

        $module = AnonymousModule::make('x_leaves', $this->app)
            ->withRevisions()
            ->withFields(['title' => ['translatable' => true]])
            ->withRelated(['x_leaves'])
            ->boot();

        $model = $module->getRepository()->create([
            'title' => ['en' => 'English title'],
            'active' => ['en' => true],
            'browsers' => [
                'x_leaves' => [
                    [
                        'id' => $treeId = $browserModule->getModelClassName()::create(['title' => 'demo'])->id,
                        'endpointType' => $browserModule->getModelClassName(),
                    ],
                ],
            ],
        ]);

        $this->assertCount(1, $model->loadRelated('x_leaves'));
        $this->assertEquals($treeId, $model->loadRelated('x_leaves')->first()->id);

        $duplicate = $module->getRepository()->duplicate($model->id);

        $this->assertCount(1, $duplicate->loadRelated('x_leaves'));
        $this->assertEquals($treeId, $duplicate->loadRelated('x_leaves')->first()->id);
    }

    public function testDuplicateWithBelongsToManyBrowser(): void
    {
        $browserModule = AnonymousModule::make('d_apps', $this->app)
            ->withFields(['title'])
            ->boot();

        $module = AnonymousModule::make('d_aleaves', $this->app)
            ->withRevisions()
            ->withFields(['title' => ['translatable' => true]])
            ->withBelongsToMany(['d_apps' => $browserModule->getModelClassName()])
            ->boot();

        $model = $module->getRepository()->create([
            'title' => ['en' => 'English title'],
            'active' => ['en' => true],
            'browsers' => [
                'd_apps' => [
                    ['id' => $treeId = $browserModule->getModelClassName()::create(['title' => 'demo'])->id],
                ],
            ],
        ]);

        $this->assertCount(1, $model->d_apps);
        $this->assertEquals($treeId, $model->d_apps->first()->id);

        $duplicate = $module->getRepository()->duplicate($model->id);

        $this->assertCount(1, $duplicate->d_apps);
        $this->assertEquals($treeId, $duplicate->d_apps->first()->id);
    }

    public function testDuplicateWithBlocksAndJsonRepeaters(): void
    {
        $module = AnonymousModule::make('d_bleaves', $this->app)
            ->withRevisions()
            ->withFields(['title' => ['translatable' => true], 'repeaterdata' => ['type' => 'json']])
            ->boot();

        $model = $module->getRepository()->create([
            'title' => ['en' => 'English title'],
            'active' => ['en' => true],
            'blocks' => [
                [
                    'type' => 'a17-block-quote',
                    'content' => [
                        'quote' => 'Quote',
                        'author' => 'Variable the first',
                    ],
                    'id' => time(),
                ],
            ],
            'repeaters' => [
                'repeaterdata' => [
                    [
                        'id' => time() + 1,
                        'content' => [
                            'description' => 'Hello world!',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertCount(1, $model->repeaterdata);
        $this->assertEquals('English title', $model->title);
        $this->assertCount(1, $model->blocks);

        $this->assertCount(1, Block::get());

        $duplicate = $module->getRepository()->duplicate($model->id);

        $this->assertCount(1, $model->repeaterdata);
        $this->assertCount(1, $duplicate->blocks);
        $this->assertCount(2, Block::get());

        $this->assertNotEquals($duplicate->blocks->first()->id, $model->blocks->first()->id);
    }

    public function testDuplicateWithRepeaters(): void
    {
        $module = AnonymousModule::make('d_codes', $this->app)
            ->withRevisions()
            ->withFields(['title' => ['translatable' => true]])
            ->withRepeaters(["\App\Models\DTree"])
            ->boot();

        AnonymousModule::make('d_trees', $this->app)
            ->withBelongsTo(['d_code' => $module->getModelClassName()])
            ->withFields(['title'])
            ->boot();

        $model = $module->getRepository()->create([
            'title' => ['en' => 'English title'],
            'active' => ['en' => true],
            'repeaters' => [
                'dtrees' => [
                    [
                        'id' => time(),
                        'title' => 'Hello repeater!',
                    ],
                ],
            ],
        ]);

        $this->assertCount(1, $model->dtrees);

        // We have to clear the temp store as this would also happen on a real environment.
        // @todo: This is not ideal behaviour as this might regress in real environments as well.
        TwillUtil::clearTempStore();

        $duplicate = $module->getRepository()->duplicate($model->id);

        $this->assertCount(1, $duplicate->dtrees);
        $this->assertNotEquals($model->dtrees->first()->id, $duplicate->dtrees->first()->id);
    }
}
