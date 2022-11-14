<?php

namespace integration\Repositories;

use A17\Twill\Tests\Integration\ModulesTestBase;
use App\Repositories\AuthorRepository;

class TagsHandlerTest extends ModulesTestBase
{
    public function testBasicTagSlug(): void
    {
        $author = app(AuthorRepository::class)
            ->create(['tags' => 'Tag a,Tag_-2134 b,Tag & !@#! C']);

        $this->assertEquals(3, $author->tags()->count());

        $this->assertEquals('Tag a', $author->tags()->get()[0]->name);
        $this->assertEquals('tag-a', $author->tags()->get()[0]->slug);

        $this->assertEquals('Tag_-2134 b', $author->tags()->get()[1]->name);
        $this->assertEquals('tag-2134-b', $author->tags()->get()[1]->slug);

        $this->assertEquals('Tag & !@#! C', $author->tags()->get()[2]->name);
        $this->assertEquals('tag-at-c', $author->tags()->get()[2]->slug);
    }

    /**
     * @dataProvider slugExamples
     */
    public function testTagSlugsCharacters(string $string, string $slug): void
    {
        $author = app(AuthorRepository::class)
            ->create(['tags' => $string]);

        $this->assertEquals($string, $author->tags()->get()[0]->name);
        $this->assertEquals($slug, $author->tags()->get()[0]->slug);
    }

    public function slugExamples(): array
    {
        return [
            'default' => [
                'string' => 'some string',
                'slug' => 'some-string'
            ],
            'numbers' => [
                'string' => '12345',
                'slug' => '12345'
            ],
            'atsign' => [
                'string' => 'example @ foo',
                'slug' => 'example-at-foo'
            ],
            'specialcharacters' => [
                'string' => '!#$%^^&*^(',
                'slug' => ''
            ],
            'chinesecharacters' => [
                'string' => '標籤',
                'slug' => '標籤'
            ],
            'chinesecharacters2' => [
                'string' => '示範',
                'slug' => '示範'
            ],
            'chinesecharacters3' => [
                'string' => 'test:示@範 foo!',
                'slug' => 'test-示-範-foo-'
            ],
        ];
    }
}
