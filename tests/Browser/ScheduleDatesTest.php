<?php

namespace A17\Twill\Tests\Browser;

use Carbon\Carbon;
use Laravel\Dusk\Browser;

class ScheduleDatesTest extends BrowserTestCase
{
    public function configTwill($app): void
    {
        parent::configTwill($app);
        $app['config']->set('twill.publish_date_24h', true);
        $app['config']->set('twill.publish_date_format', 'Y-m-d H:i');
        $app['config']->set('twill.publish_date_display_format', 'YYYY-MM-DD HH:mm');
    }

    public function testWithDateTimeWinter(): void
    {
        $class = null;
        $this->tweakApplication(function () use (&$class) {
            $class = \A17\Twill\Tests\Integration\Anonymous\AnonymousModule::make('servers', app())
                ->withFields([
                    'title' => [],
                    'publish_start_date' => [
                        'nullable' => true,
                        'type' => 'dateTime',
                    ],
                    'publish_end_date' => [
                        'nullable' => true,
                        'type' => 'dateTime',
                    ],
                ])
                ->boot();
        });

        $this->assertEquals('UTC', config('app.timezone'));

        $this->browse(function (Browser $paris, Browser $newYork) {
            $paris->setLocationToParis();
            $newYork->setLocationToNewYork();

            $paris->loginAs($this->superAdmin, 'twill_users');
            $paris->visit('/twill');

            $paris->clickLink('Servers');
            $paris->waitForText('There is no item here yet.');
            $paris->press('Add new');
            $paris->waitFor('.modal__header');
            $paris->type('title', 'Digitalocean');
            $paris->press('Create');

            $paris->waitForReload();

            // Expand the publisher.
            $paris->click('.accordion__trigger');

            $publishStart = Carbon::create(2022, 01, 10);
            $publishEnd = Carbon::create(2022, 01, 15);

            // Start date.
            $paris->with(
                '.accordion__dropdown .accordion__fields .datePicker:first-child',
                function ($dropDown) use ($publishStart) {
                    $targetDate = $publishStart->format('F j, Y');

                    // Select the date and set the hour.
                    $dropDown->waitFor('.form-control.input');
                    $dropDown->click('.form-control.input');

                    $dropDown->waitFor('.flatpickr-monthDropdown-months');
                    $dropDown->select('.flatpickr-monthDropdown-months', $publishStart->month - 1);

                    $dropDown->click('.flatpickr-day[aria-label="' . $targetDate . '"]');
                    $dropDown->type('.numInput.flatpickr-hour', '10'); // 10.30
                    $dropDown->type('.numInput.flatpickr-minute', '30');
                    // We click this element once more to trigger the state update.
                    $dropDown->click('.flatpickr-day[aria-label="' . $targetDate . '"]');
                    $dropDown->keys('.numInput.flatpickr-minute', '{enter}');

                    // Check that the date is exactly that what we expected according to our config.
                    $dropDown->assertInputValue('.form-control.input', $publishStart->format('Y-m-d') . ' 10:30');
                }
            );

            // Click something to remove focus and commit.
            $paris->click('.fieldset__header');

            // End date.
            $paris->with(
                '.accordion__dropdown .accordion__fields .datePicker:last-child',
                function ($dropDown) use ($publishEnd) {
                    $targetDate = $publishEnd->format('F j, Y');

                    // Select the date and set the hour.
                    $dropDown->waitFor('.form-control.input');
                    $dropDown->click('.form-control.input');

                    $dropDown->waitFor('.flatpickr-monthDropdown-months');
                    $dropDown->select('.flatpickr-monthDropdown-months', $publishEnd->month - 1);

                    $dropDown->click('.flatpickr-day[aria-label="' . $targetDate . '"]');
                    $dropDown->type('.numInput.flatpickr-hour', '10'); // 10.30
                    $dropDown->type('.numInput.flatpickr-minute', '30');
                    // We click this element once more to trigger the state update.
                    $dropDown->click('.flatpickr-day[aria-label="' . $targetDate . '"]');
                    $dropDown->keys('.numInput.flatpickr-minute', '{enter}');

                    // Check that the date is exactly that what we expected according to our config.
                    $dropDown->assertInputValue('.form-control.input', $publishEnd->format('Y-m-d') . ' 10:30');
                }
            );

            // Click something to remove focus and commit.
            $paris->click('.fieldset__header');

            $paris->press('Update');

            $paris->waitForText('Content saved. All good!');

            // Check after refresh that the date is the same.
            $paris->refresh();

            // Check the label is correctly formatted.
            $paris->assertSeeIn('.accordion__value div', $publishStart->setTime(10, 30)->format('Y-m-d H:i'));

            // NEW YORK
            // Now we also login to the newYork browser.
            $newYork->setLocationToNewYork();

            $newYork->loginAs($this->superAdmin, 'twill_users');
            $newYork->visit('/twill');

            $newYork->clickLink('Servers');
            $newYork->clickLink('Digitalocean');

            // Check that the date is exactly that what we expected.
            $newYork->assertSeeIn('.accordion__value div', $publishStart->setTime(04, 30)->format('Y-m-d H:i'));
        });

        $latest = $class::latest()->first();

        // Double check that in the database our timezone is in utc.
        // This is in winter time so we expect it to be one hour different.
        $this->assertEquals(
            '2022-01-10T09:30:00.000Z',
            Carbon::parse($latest->publish_start_date)->toIso8601ZuluString('millisecond')
        );
    }

    public function testWithDateTimeSummer(): void
    {
        $class = null;
        $this->tweakApplication(function () use (&$class) {
            $class = \A17\Twill\Tests\Integration\Anonymous\AnonymousModule::make('servers', app())
                ->withFields([
                    'title' => [],
                    'publish_start_date' => [
                        'nullable' => true,
                        'type' => 'dateTime',
                    ],
                    'publish_end_date' => [
                        'nullable' => true,
                        'type' => 'dateTime',
                    ],
                ])
                ->boot();
        });

        $this->assertEquals('UTC', config('app.timezone'));

        $this->browse(function (Browser $paris, Browser $newYork) {
            $paris->setLocationToParis();
            $newYork->setLocationToNewYork();

            $paris->loginAs($this->superAdmin, 'twill_users');
            $paris->visit('/twill');

            $paris->clickLink('Servers');
            $paris->waitForText('There is no item here yet.');
            $paris->press('Add new');
            $paris->waitFor('.modal__header');
            $paris->type('title', 'Digitalocean');
            $paris->press('Create');

            $paris->waitForReload();

            // Expand the publisher.
            $paris->click('.accordion__trigger');

            $publishStart = Carbon::create(2022, 06, 10);
            $publishEnd = Carbon::create(2022, 06, 15);

            // Start date.
            $paris->with(
                '.accordion__dropdown .accordion__fields .datePicker:first-child',
                function (Browser $dropDown) use ($publishStart) {
                    $targetDate = $publishStart->format('F j, Y');

                    // Select the date and set the hour.
                    $dropDown->waitFor('.form-control.input');
                    $dropDown->click('.form-control.input');

                    $dropDown->waitFor('.flatpickr-monthDropdown-months');
                    $dropDown->select('.flatpickr-monthDropdown-months', $publishStart->month - 1);

                    $dropDown->click('.flatpickr-day[aria-label="' . $targetDate . '"]');
                    $dropDown->type('.numInput.flatpickr-hour', '10'); // 10.30
                    $dropDown->type('.numInput.flatpickr-minute', '30');
                    // We click this element once more to trigger the state update.
                    $dropDown->click('.flatpickr-day[aria-label="' . $targetDate . '"]');
                    $dropDown->keys('.numInput.flatpickr-minute', '{enter}');

                    // Check that the date is exactly that what we expected according to our config.
                    $dropDown->assertInputValue('.form-control.input', $publishStart->format('Y-m-d') . ' 10:30');
                }
            );

            // Click something to remove focus and commit.
            $paris->click('.fieldset__header');

            // End date.
            $paris->with(
                '.accordion__dropdown .accordion__fields .datePicker:last-child',
                function ($dropDown) use ($publishEnd) {
                    // Select the date and set the hour.
                    $targetDate = $publishEnd->format('F j, Y');

                    // Select the date and set the hour.
                    $dropDown->waitFor('.form-control.input');
                    $dropDown->click('.form-control.input');

                    $dropDown->waitFor('.flatpickr-monthDropdown-months');
                    $dropDown->select('.flatpickr-monthDropdown-months', $publishEnd->month - 1);

                    $dropDown->click('.flatpickr-day[aria-label="' . $targetDate . '"]');
                    $dropDown->type('.numInput.flatpickr-hour', '10'); // 10.30
                    $dropDown->type('.numInput.flatpickr-minute', '30');
                    // We click this element once more to trigger the state update.
                    $dropDown->click('.flatpickr-day[aria-label="' . $targetDate . '"]');
                    $dropDown->keys('.numInput.flatpickr-minute', '{enter}');

                    // Check that the date is exactly that what we expected according to our config.
                    $dropDown->assertInputValue('.form-control.input', $publishEnd->format('Y-m-d') . ' 10:30');
                }
            );

            // Click something to remove focus and commit.
            $paris->click('.fieldset__header');

            $paris->press('Update');

            $paris->waitForText('Content saved. All good!');

            // Check after refresh that the date is the same.
            $paris->refresh();

            // Check the label is correctly formatted.
            $paris->assertSeeIn('.accordion__value div', $publishStart->setTime(10, 30)->format('Y-m-d H:i'));

            // NEW YORK
            // Now we also login to the newYork browser.
            $newYork->setLocationToNewYork();

            $newYork->loginAs($this->superAdmin, 'twill_users');
            $newYork->visit('/twill');

            $newYork->clickLink('Servers');
            $newYork->clickLink('Digitalocean');

            // Check that the date is exactly that what we expected.
            $newYork->assertSeeIn('.accordion__value div', $publishStart->setTime(04, 30)->format('Y-m-d H:i'));
        });

        $latest = $class::latest()->first();

        // Double check that in the database our timezone is in utc.
        // This is in winter time so we expect it to be one hour different.
        $this->assertEquals(
            '2022-06-10T08:30:00.000Z',
            Carbon::parse($latest->publish_start_date)->toIso8601ZuluString('millisecond')
        );
    }

}
