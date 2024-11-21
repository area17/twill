<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Model;
use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;

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

    public function testMultipleSlugAttributes()
    {
        $module = AnonymousModule::make('usernames', $this->app)
            ->withFields([
                'first_name' => [],
                'last_name' => [],
            ])
            ->withSlugFields([
                'last_name', 'first_name'
            ])
            ->boot();

        $model = $module->getRepository()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals('doe-john', $model->getSlug());
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
        $this->module->getRepository()->create([
            'title' => 'Id increment',
            'slug' => ['en' => 'Id increment'],
        ]);
        for ($i = 0; $i < 10; $i++) {
            $model = $this->module->getRepository()->create([
                'title' => 'My title',
                'slug' => ['en' => 'my-title'],
            ]);

            $this->assertEquals($i === 0 ? 'my-title' : 'my-title-' . ($i > 2 ? $model->id : $i + 1), $model->getSlug());
        }
    }

    public function testReactivateSlug(): void
    {
        $model = $this->module->getRepository()->create([
            'title' => 'My title',
            'slug' => ['en' => 'my-title'],
        ]);

        $this->assertEquals('my-title', $model->getSlug());

        $this->module->getRepository()->update($model->id, ['title' => 'My title updated']);

        $this->assertEquals('my-title-updated', $model->fresh()->getSlug());

        $activeSlug = $model->slugs()->where('active', true)->get();
        $inactiveSlug = $model->slugs()->where('active', false)->get();
        $this->assertEquals('my-title-updated', $activeSlug->first()->slug);
        $this->assertEquals('my-title', $inactiveSlug->first()->slug);
        $this->assertCount(1, $activeSlug);
        $this->assertCount(1, $inactiveSlug);

        $this->module->getRepository()->update($model->id, ['title' => 'My title']);

        $this->assertEquals('my-title', $model->fresh()->getSlug());

        $activeSlug = $model->slugs()->where('active', true)->get();
        $inactiveSlug = $model->slugs()->where('active', false)->get();
        $this->assertEquals('my-title', $activeSlug->first()->slug);
        $this->assertEquals('my-title-updated', $inactiveSlug->first()->slug);
        $this->assertCount(1, $activeSlug);
        $this->assertCount(1, $inactiveSlug);
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

        $this->assertEquals(1, $model->slugs()->onlyTrashed()->count());
        $this->assertEquals(0, $model->slugs()->count());

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

        $model = $model->fresh();

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
        $this->module->getRepository()->create([
            'title' => 'My title',
            'slug' => ['en' => 'slug-update'],
        ]);

        // Total slugs should be 3.
        $slugs = $this->module->getSlugModelClassName()::withTrashed()->get();
        $this->assertEquals('my-title', $slugs[0]->slug);
        $this->assertEquals('slug-update', $slugs[1]->slug);
        $this->assertEquals('slug-update', $slugs[2]->slug);

        $this->assertCount(3, $slugs);

        // Restore the deleted model.
        $this->assertTrue($this->module->getRepository()->restore($model->id));

        $model = $model->fresh();

        $this->assertCount(2, $model->slugs()->get());
        $this->assertEquals('slug-update-2', $model->getSlug());
    }

    public function testCustomSlugDoesntChangeOnUpdate(): void
    {
        /** @var Model|HasSlug $model */
        $model = $this->module->getRepository()->create([
            'title' => 'My title',
            'slug' => ['en' => 'my-custom-slug'],
        ]);

        $this->assertEquals('my-custom-slug', $model->getSlug());
        $this->assertEquals(1, $model->slugs()->count());

        $model = $this->module->getRepository()->update($model->id, ['position' => 1]);

        $this->assertEquals(1, $model->slugs()->count());
        $this->assertEquals('my-custom-slug', $model->getSlug());

        $model = $this->module->getRepository()->update($model->id, ['title' => 'My new title']);
        $this->assertEquals('my-new-title', $model->getSlug());
    }
}
