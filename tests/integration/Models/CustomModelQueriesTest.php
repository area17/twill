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

        $content = \App\Models\Screen::firstByTranslatedField(value: 'demo', field: 'title');
    }

    public function testFirstByTranslatedFieldOrCreate(): void
    {
        $content = \App\Models\Screen::firstByTranslatedFieldOrCreate(
            value: 'demo',
            field: 'title',
            attributes: [
                'title' => [
                    'en' => 'english title',
                    'fr' => 'french title',
                ],
            ]
        );
    }
}
