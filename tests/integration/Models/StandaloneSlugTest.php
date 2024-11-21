<?php

namespace A17\Twill\Tests\Integration\Models;

use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;
use A17\Twill\Tests\Integration\ModulesTestBase;
use A17\Twill\Tests\Integration\TestCase;
use App\Models\Author;
use App\Repositories\AuthorRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StandaloneSlugTest extends ModulesTestBase
{
    public function testSavingStandaloneSlug(): void
    {
        $allPublished = [['published' => true, 'value' => 'en'], ['published' => true, 'value' => 'fr'], ['published' => true, 'value' => 'pt-BR']];
        $createAuthor = fn (): Author => $this->app->get(AuthorRepository::class)->create(['name' => ['en' => 'Test author', 'fr' => 'Test auteur', 'pt-BR' => 'Test author'], 'languages' => $allPublished]);

        $author = $createAuthor();
        $this->assertCount(3, $author->getSlugParams(null, true));
        $this->assertEquals(3, $author->slugs()->count());
        $this->assertEquals('test-author', $author->slug);
        $this->assertEquals('test-auteur', $author->getSlug('fr'));
        $this->assertEquals('test-author', $author->getSlug('pt-BR'));

        app(AuthorRepository::class)
            ->create(['name' => ['en' => 'Random author to change id']]);

        DB::enableQueryLog();
        $author->save();
        $log = DB::getRawQueryLog();
        // Nothing changed, there should be no queries
        $this->assertCount(0, $log);

        DB::enableQueryLog();
        $author->name = 'New test author';
        $author->translate('fr')->name = 'Nouveau test auteur';
        $author->save();
        $log = DB::getRawQueryLog();
        DB::flushQueryLog();

        // There should be 2 select and 2 updates for slugs and 1 update for the locale per locale changed
        $this->assertEquals(10, count($log));
        $this->assertEquals('new-test-author', $author->getSlug('en'));
        $this->assertEquals('nouveau-test-auteur', $author->getSlug('fr'));
        // All queries combined should take less than 40ms (they usually take a total of 5ms, allow big range to avoid flaky test)
        $this->assertLessThan(40, array_sum(Arr::pluck($log, 'time')));

        $author2 = $createAuthor();
        $this->assertEquals('test-author-2', $author2->slug);
        $this->assertEquals('test-auteur-2', $author2->getSlug('fr'));
        $author3 = $createAuthor();
        $this->assertEquals('test-author-3', $author3->slug);
        $this->assertEquals('test-auteur-3', $author3->getSlug('fr'));
        $author4 = $createAuthor();
        $this->assertEquals('test-author-'.$author4->id, $author4->slug);
        $this->assertEquals('test-auteur-'.$author4->id, $author4->getSlug('fr'));
        $author4 = $author4->fresh();
        $author4->name = 'New author slug';
        DB::flushQueryLog();
        $author4->save();
        $log2 = DB::getRawQueryLog();
        // 2 slug existence check, 1 slug insert, 1 slug update, 1 translation update,
        $this->assertCount(5, $log2);
        $this->assertEquals(3, $author4->slugs()->whereActive(true)->count());
        $this->assertEquals(1, $author4->slugs()->whereActive(false)->count());
        $this->assertEquals('new-author-slug', $author4->slug);
        $author2->delete();
        DB::flushQueryLog();
        $author4->name = 'Test author';
        $author4->save();
        $log3 = DB::getRawQueryLog();
        $this->assertEquals(3, $author4->slugs()->whereActive(true)->count());
        $this->assertEquals(2, $author4->slugs()->whereActive(false)->count());
        // 3 selects, 1 insert, 2 updates
        $this->assertCount(6, $log3);
        $this->assertEquals('test-author-2', $author4->slug);
        DB::disableQueryLog();

        $this->assertEquals(3, $author->slugs()->whereActive(true)->count());
        $this->assertEquals(5, $author->slugs()->count());
    }

}
