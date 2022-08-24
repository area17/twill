<?php

namespace A17\Twill\Tests\Browser;

use Carbon\Carbon;
use Laravel\Dusk\Browser;

class ScheduleDatesTest extends BrowserTestCase
{
    /**
     * @dataProvider timesDataProvider
     */
    public function testWithDateTime(Carbon $from, Carbon $to, array $expectedTime, bool $time24h): void
    {
        $class = null;
        $this->tweakApplication(function () use (&$class, $time24h) {
            config()->set('translatable.locales', ['en']);
            config()->set('twill.publish_date_24h', $time24h);
            $class = \A17\Twill\Tests\Integration\Anonymous\AnonymousModule::make('scheduleservers', app())
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
                ->boot()
                ->getModelClassName();
        });

        $this->assertEquals('UTC', config('app.timezone'));

        $this->browse(function (Browser $paris, Browser $newYork) use ($from, $to, $time24h) {
            $paris->setBrowserLocationToParis();
            $newYork->setBrowserLocationToNewYork();

            $paris->loginAs($this->superAdmin, 'twill_users');
            $paris->createModuleEntryWithTitle('Scheduleservers', 'Digitalocean');

            // Expand the publisher.
            $paris->click('.accordion__trigger');

            $paris->setDateTimeInDatePicker(
                fieldSelector: '.accordion__dropdown .accordion__fields .datePicker:first-child .form-control',
                dateTime: $from,
                dateFormat: $time24h ? 'F j, Y H:i' : 'F j, Y H:i A',
                staticWrapper: '.accordion__dropdown .accordion__fields .datePicker:first-child'
            );
            $paris->setDateTimeInDatePicker(
                fieldSelector: '.accordion__dropdown .accordion__fields .datePicker:last-child .form-control',
                dateTime: $to,
                dateFormat: $time24h ? 'F j, Y H:i' : 'F j, Y H:i A',
                staticWrapper: '.accordion__dropdown .accordion__fields .datePicker:last-child'
            );

            $paris->press('Update');

            $paris->waitForText('Content saved. All good!');

            // Check after refresh that the date is the same.
            $paris->refresh();

            $shouldSeeDateFormat = $time24h ? 'M, j, Y, H:i' : 'M, j, Y, H:i A';

            // Check the label is correctly formatted.
            $paris->assertSeeIn('.accordion__value div', $from->format($shouldSeeDateFormat));
            // We set time to be 24H so we should not see this.
            if ($time24h) {
                $paris->assertDontSeeIn('.accordion__value div', 'AM');
            } else {
                $paris->assertSeeIn('.accordion__value div', 'AM');
            }

            // NEW YORK
            // Now we also login to the newYork browser.
            $newYork->setBrowserLocationToNewYork();

            $newYork->loginAs($this->superAdmin, 'twill_users');
            $newYork->visitModuleEntryWithTitle('Scheduleservers', 'Digitalocean');

            // Check that the date is exactly that what we expected.
            $newYork->assertSeeIn('.accordion__value div', $from->setTime(04, 30)->format($shouldSeeDateFormat));
            if (! $time24h) {
                $newYork->assertSeeIn('.accordion__value div', 'AM');
            } else {
                $newYork->assertDontSeeIn('.accordion__value div', 'AM');
            }
        });

        $latest = $class::latest()->first();

        // Double check that in the database our timezone is in utc.
        $this->assertEquals(
            $from->setTime($expectedTime['hour'], $expectedTime['minute'])->format('Y-m-d H:i'),
            $latest->publish_start_date->format('Y-m-d H:i')
        );

        $this->assertEquals(
            $to->setTime($expectedTime['hour'], $expectedTime['minute'])->format('Y-m-d H:i'),
            $latest->publish_end_date->format('Y-m-d H:i')
        );
    }

    public function timesDataProvider(): array
    {
        return [
            'winterAMPM' => [
                Carbon::createFromDate(Carbon::now()->year, 01, 10)->setTime(10, 30),
                Carbon::createFromDate(Carbon::now()->year, 01, 15)->setTime(10, 30),
                [
                    'hour' => 9,
                    'minute' => 30,
                ],
                false,
            ],
            'summerAMPM' => [
                Carbon::createFromDate(Carbon::now()->year, 07, 10)->setTime(10, 30),
                Carbon::createFromDate(Carbon::now()->year, 07, 15)->setTime(10, 30),
                [
                    'hour' => 8,
                    'minute' => 30,
                ],
                false,
            ],
            'winter24h' => [
                Carbon::createFromDate(Carbon::now()->year, 01, 10)->setTime(10, 30),
                Carbon::createFromDate(Carbon::now()->year, 01, 15)->setTime(10, 30),
                [
                    'hour' => 9,
                    'minute' => 30,
                ],
                true,
            ],
            'summer24h' => [
                Carbon::createFromDate(Carbon::now()->year, 07, 10)->setTime(10, 30),
                Carbon::createFromDate(Carbon::now()->year, 07, 15)->setTime(10, 30),
                [
                    'hour' => 8,
                    'minute' => 30,
                ],
                true,
            ],
        ];
    }
}
