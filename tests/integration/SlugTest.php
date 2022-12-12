<?php

namespace integration;

use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;
use A17\Twill\Tests\Integration\TestCase;

class SlugTest extends TestCase
{
    private AnonymousModule $module;

    public function setUp(): void
    {
        parent::setUp();
        config()->set('translatable.locales', ['en']);

        $this->module = AnonymousModule::make('seaslugs', $this->app)
            ->withFields([
                'title' => [],
            ])
            ->withSlugAttributes([
                'title',
            ])
            ->boot();
    }

    public function testBasicSlugModel(): void
    {
        $model = $this->module->getRepository()->create([
            'title' => 'My title',
            'slug' => ['en' => 'my-title'],
        ]);

        $this->assertEquals('my-title', $model->getSlug());
    }

    public function testBasicSlugModelDuplicate(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $model = $this->module->getRepository()->create([
                'title' => 'My title',
                'slug' => ['en' => 'my-title'],
            ]);

            $this->assertEquals($i === 0 ? 'my-title' : 'my-title-' . $i + 1, $model->getSlug());
        }
    }

    public function testCanReuseSoftDeletedSlug(): void
    {
        $model = $this->module->getRepository()->create([
            'title' => 'My title',
            'slug' => ['en' => 'my-title'],
        ]);

        $this->assertCount(1, $model->slugs()->get());
        $this->assertEquals('my-title', $model->getSlug());

        $this->module->getRepository()->delete($model->id);

        // Create a new model after the delete.
        $newModel = $this->module->getRepository()->create([
            'title' => 'My title',
            'slug' => ['en' => 'my-title'],
        ]);

        $this->assertCount(1, $newModel->slugs()->get());
        $this->assertEquals('my-title', $newModel->getSlug());

        // Total slugs should be 2.
        $this->assertCount(2, $this->module->getSlugModelClassName()::withTrashed()->get());

        // Restore the deleted model.
        $this->assertTrue($this->module->getRepository()->restore($model->id));

        $model = $this->module->getModelClassName()::find($model->id);

        $this->assertCount(1, $model->slugs()->get());
        $this->assertEquals('my-title-2', $model->getSlug());
    }

    public function testCanReuseSoftDeletedSlugWithHistory(): void
    {
        $model = $this->module->getRepository()->create([
            'title' => 'My title',
            'slug' => ['en' => 'my-title'],
        ]);

        $model = $this->module->getRepository()->update($model->id, [
            'slug' => ['en' => 'slug-update'],
        ]);

        $this->assertCount(2, $model->slugs()->get());
        $this->assertEquals('slug-update', $model->getSlug());

        $this->module->getRepository()->delete($model->id);

        // Create a new model after the delete.
        $newModel = $this->module->getRepository()->create([
            'title' => 'My title',
            'slug' => ['en' => 'slug-update'],
        ]);

        // Total slugs should be 3.
        $this->assertEquals('my-title', $this->module->getSlugModelClassName()::withTrashed()->get()[0]->slug);
        $this->assertEquals('slug-update', $this->module->getSlugModelClassName()::withTrashed()->get()[1]->slug);
        $this->assertEquals('slug-update', $this->module->getSlugModelClassName()::withTrashed()->get()[2]->slug);

        $this->assertCount(3, $this->module->getSlugModelClassName()::withTrashed()->get());

        // Restore the deleted model.
        $this->assertTrue($this->module->getRepository()->restore($model->id));

        $model = $this->module->getModelClassName()::find($model->id);

        $this->assertCount(2, $model->slugs()->get());
        $this->assertEquals('slug-update-2', $model->getSlug());
    }

    public function testReactivateSlug(): void
    {
        $model = $this->module->getRepository()->create([
            'title' => 'My title',
            'slug' => ['en' => 'my-title'],
        ]);

        $this->assertEquals('my-title', $model->getSlug());
        $this->assertCount(1, $model->slugs()->get());

        $model = $this->module->getRepository()->update($model->id, [
            'slug' => ['en' => 'slug-update'],
        ]);

        $this->assertEquals('slug-update', $model->getSlug());
        $this->assertCount(2, $model->slugs()->get());

        $model = $this->module->getRepository()->update($model->id, [
            'slug' => ['en' => 'my-title'],
        ]);

        $this->assertEquals('my-title', $model->getSlug());
        $this->assertCount(2, $model->slugs()->get());
    }
}
