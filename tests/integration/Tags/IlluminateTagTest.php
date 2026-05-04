<?php

namespace A17\Twill\Tests\Integration\Tags;

use Cartalyst\Tags\IlluminateTag;
use Cartalyst\Tags\IlluminateTagged;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class IlluminateTagTest extends FunctionalTestCase
{
    public function test_it_deletes_related_tagged_records(): void
    {
        $post = $this->createPost();

        $post->tag('foo, bar');

        $this->assertCount(2, $post->tags);

        $tag = IlluminateTag::first();

        $tag->delete();

        $post = $post->fresh();

        $this->assertCount(1, $post->tags);
    }

    public function test_it_has_a_taggable_relationship(): void
    {
        $tag = new IlluminateTag();

        $this->assertInstanceOf(MorphTo::class, $tag->taggable());
    }

    public function test_it_has_a_tag_relationship(): void
    {
        $tag = new IlluminateTag();

        $this->assertInstanceOf(HasMany::class, $tag->tagged());
    }

    public function test_it_has_a_name_scope(): void
    {
        IlluminateTag::create(['name' => 'Foo', 'slug' => 'foo', 'namespace' => 'foo']);

        $this->assertCount(1, IlluminateTag::name('Foo')->get());
    }

    public function test_it_has_a_slug_scope(): void
    {
        IlluminateTag::create(['name' => 'Foo', 'slug' => 'foo', 'namespace' => 'foo']);

        $this->assertCount(1, IlluminateTag::slug('foo')->get());
    }

    public function test_it_can_get_and_set_the_tagged_model(): void
    {
        $tag = new IlluminateTag();

        $tag->setTaggedModel(IlluminateTagged::class);

        $this->assertSame(IlluminateTagged::class, $tag->getTaggedModel());
    }
}
