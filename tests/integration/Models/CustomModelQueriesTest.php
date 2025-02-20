<?php

namespace A17\Twill\Tests\Integration\Models;

use A17\Twill\Repositories\ModuleRepository;
use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;
use A17\Twill\Tests\Integration\TestCase;

class CustomModelQueriesTest extends TestCase
{
    private AnonymousModule $module;

    public function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Config::set('translatable.locales', ['en', 'fr']);
        $this->module = AnonymousModule::make('screens', $this->app)
            ->withFields(['title' => ['translatable' => 'true']])
            ->boot();
    }

    public function testFirstByTranslatedField(): void
    {
        /** @var ModuleRepository $repo */
        $repo = app($this->module->getRepositoryClassName());

        $repo->create([
            'title' => [
                'en' => 'a-title-1-en',
                'fr' => 'b-title-1-fr',
            ],
            'published' => true,
        ]);
        $repo->create([
            'title' => [
                'en' => 'b-title-1-en',
                'fr' => 'a-title-1-fr',
            ],
            'published' => true,
        ]);

        $result = \App\Models\Screen::orderByTranslation('title')
            ->whereTranslationLike('title', '%title%')
            ->get();

        $this->assertEquals('a-title-1-en', $result[0]->title);
        $this->assertEquals('b-title-1-en', $result[1]->title);

        $result = \App\Models\Screen::orderByTranslation('title', 'desc')
            ->whereTranslationLike('title', '%title%')
            ->get();

        $this->assertEquals('b-title-1-en', $result[0]->title);
        $this->assertEquals('a-title-1-en', $result[1]->title);

        // Now query just the first entry.
        $result = \App\Models\Screen::orderByTranslation('title')
            ->whereTranslationLike('title', '%title%')
            ->first();

        $this->assertEquals('a-title-1-en', $result->title);

        $result = \App\Models\Screen::orderByTranslation('title', 'desc')
            ->whereTranslationLike('title', '%title%')
            ->first();

        $this->assertEquals('b-title-1-en', $result->title);
    }
}
