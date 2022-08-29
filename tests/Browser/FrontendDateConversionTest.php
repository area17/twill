<?php

namespace A17\Twill\Tests\Browser;

use A17\Twill\Services\Forms\Fields\DatePicker;
use A17\Twill\Services\Forms\Form;
use Carbon\Carbon;
use Laravel\Dusk\Browser;

class FrontendDateConversionTest extends BrowserTestCase
{
    /**
     * @dataProvider timesDataProvider
     */
    public function testWithDateTime(Carbon $targetDate, array $expectedTime, bool $use24h): void
    {
        $class = null;
        $this->tweakApplication(function () use (&$class, $use24h) {
            $class = \A17\Twill\Tests\Integration\Anonymous\AnonymousModule::make('gateways', app())
                ->withFields([
                    'title' => [],
                    'last_booted' => [
                        'default' => null,
                        'nullable' => true,
                        'type' => 'dateTime',
                    ],
                ])
                ->withFormFields(
                    Form::make([
                        DatePicker::make()
                            ->name('last_booted')
                            ->time24h($use24h),
                    ])
                )
                ->boot()
                ->getModelClassName();
        });

        $this->assertEquals('UTC', config('app.timezone'));

        $this->browse(function (Browser $paris, Browser $newYork) use ($targetDate, $use24h) {
            $paris->setBrowserLocationToParis();
            $newYork->setBrowserLocationToNewYork();

            $paris->loginAs($this->superAdmin, 'twill_users');
            $paris->createModuleEntryWithTitle('Gateways', 'Digitalocean');

            $paris->setDateTimeInDatePicker(
                fieldSelector: '.datePicker__field .form-control',
                dateTime: $targetDate,
                dateFormat: $use24h ? 'F j, Y H:i' : 'F j, Y g:i A',
            );

            $paris->press('Update');

            $paris->waitForText('Content saved. All good!');

            // Check after refresh that the date is the same.
            $paris->refresh();

            $shouldSeeDateFormat = $use24h ? 'F j, Y H:i' : 'F j, Y g:i A';

            // Check that the date is exactly that what we expected.
            $paris->waitForText('Last Booted');
            $paris->assertInputValue('.datePicker__field .form-control', $targetDate->format($shouldSeeDateFormat));

            // NEW YORK
            // Now we also login to the newYork browser.
            $newYork->setBrowserLocationToNewYork();

            $newYork->loginAs($this->superAdmin, 'twill_users');
            $newYork->visitModuleEntryWithTitle('Gateways', 'Digitalocean');

            // Check that the date is exactly that what we expected.
            $newYork->waitForText('Last Booted');
            $newYork->assertInputValue(
                '.datePicker__field .form-control',
                $targetDate->setTime(4, 30)->format($shouldSeeDateFormat)
            );
        });

        // Double check that in the database our timezone is in utc.
        $this->assertEquals(
            $targetDate->setTime($expectedTime['hour'], $expectedTime['minute'])->format('Y-m-d H:i'),
            $class::latest()->first()->last_booted->format('Y-m-d H:i')
        );
    }

    public function timesDataProvider(): array
    {
        return [
            'winterAMPM' => [
                Carbon::createFromDate(Carbon::now()->year, 01, 15)->setTime(10, 30),
                [
                    'hour' => 9,
                    'minute' => 30,
                ],
                false,
            ],
            'summerAMPM' => [
                Carbon::createFromDate(Carbon::now()->year, 07, 15)->setTime(10, 30),
                [
                    'hour' => 8,
                    'minute' => 30,
                ],
                false,
            ],
            'winter24h' => [
                Carbon::createFromDate(Carbon::now()->year, 01, 15)->setTime(10, 30),
                [
                    'hour' => 9,
                    'minute' => 30,
                ],
                true,
            ],
            'summer24h' => [
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
