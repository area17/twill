<?php

namespace A17\Twill\Tests\Integration\Tags;

use A17\Twill\Tests\Integration\Tags\Stubs\Post;
use Cartalyst\Tags\IlluminateTag;
use Cartalyst\Tags\IlluminateTagged;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;

abstract class FunctionalTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createSchema();
        $this->resetTagConfiguration();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            \Cartalyst\Tags\TagsServiceProvider::class,
        ];
    }

    protected function createPost(): Post
    {
        return Post::create(['title' => 'My Test Post']);
    }

    private function createSchema(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('title');
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
            $table->integer('count')->unsigned()->default(0);
            $table->string('namespace')->nullable();
        });

        Schema::create('tagged', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tag_id')->unsigned();
            $table->integer('taggable_id')->unsigned();
            $table->string('taggable_type');
        });
    }

    private function resetTagConfiguration(): void
    {
        Post::setTagsDelimiter(',');
        Post::setTagsModel(IlluminateTag::class);
        Post::setSlugGenerator('Illuminate\Support\Str::slug');
        IlluminateTag::setTaggedModel(IlluminateTagged::class);
    }
}
