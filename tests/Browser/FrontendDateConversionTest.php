<?php

namespace A17\Twill\Tests\Browser;

use A17\Twill\Services\Forms\Fields\DatePicker;
use A17\Twill\Services\Forms\Form;
use Laravel\Dusk\Browser;

class FrontendDateConversionTest extends BrowserTestCase
{
    public function testWithDateTimeWinter(): void
    {
        $class = null;
        $this->tweakApplication(function () use (&$class) {
            $class = \A17\Twill\Tests\Integration\Anonymous\AnonymousModule::make('servers', app())
                ->withFields([
                    'title' => [

                    ],
                    'last_booted' => [
                        'nullable' => true,
                        'type' => 'dateTime',
                    ],
                ])
                ->withFormFields(
                    Form::make([
                        DatePicker::make()
                            ->name('last_booted')
                            ->time24h(),
                    ])
                )
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

            // Select the date and set the hour.
            $paris->click('.datePicker__field .form-control');
            $paris->click('.flatpickr-day[aria-label="January 21, 2000"]');
            $paris->type('.numInput.flatpickr-hour', '10'); // 10.30
            $paris->type('.numInput.flatpickr-minute', '30');
            // We click this element once more to trigger the state update.
            $paris->click('.flatpickr-day[aria-label="January 21, 2000"]');
            $paris->keys('.numInput.flatpickr-minute', '{enter}');

            // Check that the date is exactly that what we expected.
            $paris->assertInputValue('.datePicker__field .form-control', 'January 21, 2000 10:30');

            // Click something to remove focus.
            $paris->click('.input__label');

            $paris->press('Update');

            $paris->waitForText('Content saved. All good!');

            // Check after refresh that the date is the same.
            $paris->refresh();

            // Check that the date is exactly that what we expected.
            $paris->waitForText('Last Booted');
            $paris->assertInputValue('.datePicker__field .form-control', 'January 21, 2000 10:30');

            // NEW YORK
            // Now we also login to the newYork browser.
            $newYork->setLocationToNewYork();

            $newYork->loginAs($this->superAdmin, 'twill_users');
            $newYork->visit('/twill');

            $newYork->clickLink('Servers');
            $newYork->clickLink('Digitalocean');

            // Check that the date is exactly that what we expected.
            $newYork->waitForText('Last Booted');
            $newYork->assertInputValue('.datePicker__field .form-control', 'January 21, 2000 04:30');
        });

        $latest = $class::latest()->first();

        // Double check that in the database our timezone is in utc.
        // This is in winter time so we expect it to be one hour different.
        $this->assertEquals('2000-01-21T09:30:00.000Z', $latest->last_booted);
    }

    public function testWithDateTimeSummer(): void
    {
        $class = null;
        $this->tweakApplication(function () use (&$class) {
            $class = \A17\Twill\Tests\Integration\Anonymous\AnonymousModule::make('servers', app())
                ->withFields([
                    'title' => [

                    ],
                    'last_booted' => [
                        'nullable' => true,
                        'type' => 'dateTime',
                    ],
                ])
                ->withFormFields(
                    Form::make([
                        DatePicker::make()
                            ->name('last_booted')
                            ->time24h(),
                    ])
                )
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

            // Select the date and set the hour.
            $paris->click('.datePicker__field .form-control');
            $paris->select('.flatpickr-monthDropdown-months', '6');
            $paris->click('.flatpickr-day[aria-label="July 21, 2000"]');
            $paris->type('.numInput.flatpickr-hour', '10'); // 10.30
            $paris->type('.numInput.flatpickr-minute', '30');
            // We click this element once more to trigger the state update.
            $paris->click('.flatpickr-day[aria-label="July 21, 2000"]');
            $paris->keys('.numInput.flatpickr-minute', '{enter}');

            // Check that the date is exactly that what we expected.
            $paris->assertInputValue('.datePicker__field .form-control', 'July 21, 2000 10:30');

            // Click something to remove focus.
            $paris->click('.input__label');

            $paris->press('Update');

            $paris->waitForText('Content saved. All good!');

            // Check after refresh that the date is the same.
            $paris->refresh();

            // Check that the date is exactly that what we expected.
            $paris->waitForText('Last Booted');
            $paris->assertInputValue('.datePicker__field .form-control', 'July 21, 2000 10:30');

            // NEW YORK
            // Now we also login to the newYork browser.
            $newYork->setLocationToNewYork();

            $newYork->loginAs($this->superAdmin, 'twill_users');
            $newYork->visit('/twill');

            $newYork->clickLink('Servers');
            $newYork->clickLink('Digitalocean');

            // Check that the date is exactly that what we expected.
            $newYork->waitForText('Last Booted');
            $newYork->assertInputValue('.datePicker__field .form-control', 'July 21, 2000 04:30');
        });

        $latest = $class::latest()->first();

        // Double check that in the database our timezone is in utc.
        // This is in summer time so we expect it to be 2 hours different.
        $this->assertEquals('2000-07-21T08:30:00.000Z', $latest->last_booted);
    }
}
