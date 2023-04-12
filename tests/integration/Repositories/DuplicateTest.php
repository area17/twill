<?php

namespace A17\Twill\Tests\Integration\Repositories;

use A17\Twill\Models\Block;
use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;
use A17\Twill\Tests\Integration\Behaviors\CreatesMedia;
use A17\Twill\Tests\Integration\Behaviors\FileTools;
use A17\Twill\Tests\Integration\TestCase;
use Illuminate\Support\Facades\DB;

class DuplicateTest extends TestCase
{
    use FileTools;
    use CreatesMedia;

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

    public function testDuplicateWithBlocksAndJsonRepeaters(): void
    {
        $module = AnonymousModule::make('y_bleaves', $this->app)
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

        $this->assertCount(1, twillModel('block')::get());

        $duplicate = $module->getRepository()->duplicate($model->id);

        $this->assertCount(1, $model->repeaterdata);
        $this->assertCount(1, $duplicate->blocks);
        $this->assertCount(2, twillModel('block')::get());

        $this->assertNotEquals($duplicate->blocks->first()->id, $model->blocks->first()->id);
    }

    public function testDuplicateWithMedias(): void
    {
        $module = AnonymousModule::make('x_bleaves', $this->app)
            ->withRevisions()
            ->withMedias()
            ->withFields(['title' => ['translatable' => true]])
            ->boot();

        $this->login();
        $media = $this->createMedia();

        $model = $module->getRepository()->create([
            'title' => ['en' => 'English title'],
            'active' => ['en' => true],
            'medias' => [
                'cover' => [
                    [
                        'id' => $media->id,
                    ],
                ],
            ],
        ]);

        $model->refresh();

        // There should be 3, 1 for each crop. If this test fails in the future, it might be because the default crops
        // have been changed.
        $this->assertCount(3, $model->medias);
        $this->assertCount(1, $model->images('cover'));

        $this->assertEquals(3, DB::table(config('twill.mediables_table', 'twill_mediables'))->count());

        $duplicate = $module->getRepository()->duplicate($model->id);

        $this->assertCount(3, $duplicate->medias);
        $this->assertCount(1, $duplicate->images('cover'));
        $this->assertEquals(6, DB::table(config('twill.mediables_table', 'twill_mediables'))->count());
    }
}
