<?php

namespace A17\Twill\Tests\Integration\Dashboard;

use A17\Twill\Http\Controllers\Admin\DashboardController;
use A17\Twill\Tests\Integration\Anonymous\AnonymousModule;
use A17\Twill\Tests\Integration\TestCase;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Models\Activity;

class DashboardTest extends TestCase
{
    public AnonymousModule $computer;

    public AnonymousModule $licences;

    public function setUp(): void
    {
        parent::setUp();

        $this->computer = AnonymousModule::make('computers', $this->app)->boot();
        $this->licences = AnonymousModule::make('licences', $this->app)->boot();

        $this->actingAs($this->superAdmin(), 'twill_users');
    }

    public function testLogsActivity(): void
    {
        $this->post(route('twill.computers.store'), ['title' => 'Test title'])
            ->assertJsonPath('redirect', 'http://twill.test/twill/computers/1/edit');

        $this->post(route('twill.licences.store'), ['title' => 'Test title'])
            ->assertJsonPath('redirect', 'http://twill.test/twill/licences/1/edit');

        $this->assertCount(2, $activity = Activity::all());

        $this->assertEquals('created', $activity[0]->description);
        $this->assertEquals('App\Models\Computer', $activity[0]->subject_type);

        $this->assertEquals('created', $activity[1]->description);
        $this->assertEquals('App\Models\Licence', $activity[1]->subject_type);

        // The dashboard query should now be empty.
        $allActivities = $this->getInvadedDashboardController()->getAllActivities();

        $this->assertEmpty($allActivities);
    }

    public function testDashboardActivitiesWhenConfigured(): void
    {
        Config::set('twill.dashboard', [
            'modules' => [
                \App\Models\Computer::class => [
                    'name' => 'computers',
                    'count' => true,
                    'create' => true,
                    'activity' => true,
                    'draft' => true,
                    'search' => true,
                ],
            ],
        ]);
        $this->post(route('twill.computers.store'), ['title' => 'Test title'])
            ->assertJsonPath('redirect', 'http://twill.test/twill/computers/1/edit');

        $this->post(route('twill.licences.store'), ['title' => 'Test title'])
            ->assertJsonPath('redirect', 'http://twill.test/twill/licences/1/edit');

        $allActivities = $this->getInvadedDashboardController()->getAllActivities();

        $this->assertCount(1, $allActivities);
        $this->assertEquals('Computer', $allActivities[0]['type']);

        // Switch the config and reassert.
        Config::set('twill.dashboard', [
            'modules' => [
                \App\Models\Licence::class => [
                    'name' => 'licences',
                    'count' => true,
                    'create' => true,
                    'activity' => true,
                    'draft' => true,
                    'search' => true,
                ],
            ],
        ]);

        $allActivities = $this->getInvadedDashboardController()->getAllActivities();

        $this->assertCount(1, $allActivities);
        $this->assertEquals('Licence', $allActivities[0]['type']);
    }

    public function testDashboardActivityWithMixedContent(): void
    {
        // We create more than 20 entries. Originally if there was mixed content and one was enabled and not the other
        // this would display 10 results instead of the 20 limit.

        // Create 20 of each content.
        for ($i = 0; $i < 20; $i++) {
            $this->post(route('twill.computers.store'), ['title' => 'Test title' . $i])
                ->assertOk();

            $this->post(route('twill.licences.store'), ['title' => 'Test title' . $i])
                ->assertOk();
        }

        $this->assertCount(40, $activity = Activity::all());

        // Check that we have 20 entries.
        Config::set('twill.dashboard', [
            'modules' => [
                \App\Models\Computer::class => [
                    'name' => 'computers',
                    'count' => true,
                    'create' => true,
                    'activity' => true,
                    'draft' => true,
                    'search' => true,
                ],
            ],
        ]);

        $allActivities = $this->getInvadedDashboardController()->getAllActivities();

        $this->assertCount(20, $allActivities);
    }

    public function testDashboardActivitiesWithMorphMap(): void
    {
        Relation::morphMap([
            'computer' => \App\Models\Computer::class,
        ]);

        // Create 20 of each content.
        for ($i = 0; $i < 20; $i++) {
            $this->post(route('twill.computers.store'), ['title' => 'Test title' . $i])
                ->assertOk();

            $this->post(route('twill.licences.store'), ['title' => 'Test title' . $i])
                ->assertOk();
        }

        $this->assertCount(40, $activity = Activity::all());

        // Check that we have 20 entries.
        Config::set('twill.dashboard', [
            'modules' => [
                'computer' => [
                    'name' => 'computers',
                    'count' => true,
                    'create' => true,
                    'activity' => true,
                    'draft' => true,
                    'search' => true,
                ],
            ],
        ]);

        $allActivities = $this->getInvadedDashboardController()->getAllActivities();

        $this->assertCount(20, $allActivities);
    }

    /**
     * This must be docblock annotated as php parsers otherwise fail.
     *
     * @return DashboardController
     */
    private function getInvadedDashboardController(): mixed
    {
        /** @var \A17\Twill\Http\Controllers\Admin\DashboardController $controller */
        $controller = invade(app()->make(DashboardController::class));

        return $controller;
    }
}
