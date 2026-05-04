<?php

namespace A17\Twill\Tests\Integration\Tags;

use A17\Twill\Tests\Integration\Tags\Stubs\Post;
use A17\Twill\Tests\Integration\Tags\Stubs\Post2;
use Cartalyst\Tags\IlluminateTag;

class TaggableTraitTest extends FunctionalTestCase
{
    public function test_it_can_add_single_tags(): void
    {
        $post1 = $this->createPost();
        $post2 = $this->createPost();

        $post1->tag('foo');
        $post2->tag(['foo']);

        $this->assertSame(['foo'], $post1->tags->pluck('slug')->toArray());
        $this->assertSame(['foo'], $post2->tags->pluck('slug')->toArray());
    }

    public function test_it_can_add_multiple_tags(): void
    {
        $post1 = $this->createPost();
        $post2 = $this->createPost();
        $post3 = $this->createPost();

        $post1->tag('foo, bar');
        $post2->tag(['foo', 'bar']);
        $post3->tag(null);

        $this->assertSame(['foo', 'bar'], $post1->tags->pluck('slug')->toArray());
        $this->assertSame(['foo', 'bar'], $post2->tags->pluck('slug')->toArray());
        $this->assertEmpty($post3->tags->pluck('slug')->toArray());
    }

    public function test_it_can_untag(): void
    {
        $post = $this->createPost();

        $post->tag('foo');

        $this->assertSame(['foo'], $post->tags->pluck('slug')->toArray());

        $post->untag('foo');
        $post->untag('foo');

        $this->assertEmpty($post->tags->pluck('slug')->toArray());
    }

    public function test_it_can_remove_all_tags(): void
    {
        $post = $this->createPost();

        $post->tag('foo, bar, baz');

        $this->assertCount(3, $post->tags);

        $post->untag();

        $this->assertCount(0, $post->tags);
    }

    public function test_it_can_set_tags(): void
    {
        $post = $this->createPost();

        $post->tag('baz');

        $post->setTags('foo, bar');

        $this->assertSame(['foo', 'bar'], $post->tags->pluck('slug')->toArray());
    }

    public function test_it_can_retrieve_tags(): void
    {
        $post = $this->createPost();

        $post->tag('foo, bar, baz');

        $this->assertCount(3, $post->tags);
    }

    public function test_it_can_retrieve_all_tags(): void
    {
        $post1 = $this->createPost();
        $post2 = $this->createPost();

        $post1->tag('foo, bar, baz');
        $post2->tag('fooo');

        $this->assertCount(4, Post::allTags()->get());
        $this->assertCount(0, Post2::allTags()->get());
    }

    public function test_it_can_retrieve_by_the_given_tags(): void
    {
        $post1 = $this->createPost();
        $post2 = $this->createPost();

        $post1->tag('foo, bar, baz');
        $post2->tag('foo, bat');

        $this->assertCount(1, Post::whereTag('foo, bar')->get());
        $this->assertCount(2, Post::withTag('foo')->get());
        $this->assertCount(1, Post::withTag('bat')->get());
    }

    public function test_it_can_retrieve_without_the_given_tags(): void
    {
        $post1 = $this->createPost();
        $post2 = $this->createPost();

        $post1->tag('foo, bar, baz');
        $post2->tag('foo, bat');

        $this->assertCount(0, Post::withoutTag('foo')->get());
        $this->assertCount(1, Post::withoutTag('bar')->get());
        $this->assertCount(1, Post::withoutTag('bat')->get());
    }

    public function test_it_can_get_and_set_the_tags_delimiter(): void
    {
        $post = new Post();

        $post->setTagsDelimiter(',');

        $this->assertSame(',', $post->getTagsDelimiter());
    }

    public function test_it_can_get_and_set_the_tags_model(): void
    {
        $post = new Post();

        $post->setTagsModel(IlluminateTag::class);

        $this->assertSame(IlluminateTag::class, $post->getTagsModel());
    }

    public function test_it_can_get_and_set_the_slug_generator_as_a_string(): void
    {
        $post = new Post();

        $post->setSlugGenerator('Illuminate\Support\Str::slug');

        $this->assertSame('Illuminate\Support\Str::slug', $post->getSlugGenerator());
    }

    public function test_it_can_get_and_set_the_slug_generator_as_a_closure(): void
    {
        $post = new Post();

        $post->setSlugGenerator(function ($value) {
            return str_replace(' ', '_', strtolower($value));
        });

        $this->assertIsObject($post->getSlugGenerator());
    }
}
