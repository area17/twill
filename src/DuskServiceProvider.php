<?php

namespace A17\Twill;

use Carbon\Carbon;
use Carbon\Laravel\ServiceProvider;
use Facebook\WebDriver\Chrome\ChromeDevToolsDriver;
use Laravel\Dusk\Browser;

class DuskServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Browser::macro('setBrowserLocationToParis', function () {
            $this->setLocationToLatLon(48.864716, 2.349014, 'Europe/Paris');
        });

        Browser::macro('setBrowserLocationToNewYork', function () {
            $this->setLocationToLatLon(40.730610, -73.935242, 'America/New_York');
        });

        Browser::macro('setLocationToLatLon', function (float $lat, float $lon, string $timezone) {
            $devTools = new ChromeDevToolsDriver($this->driver);

            $location = [
                $lat,
                $lon,
                1,
            ];

            $devTools->execute('Emulation.setTimezoneOverride', ['timezoneId' => $timezone]);
            $devTools->execute('Emulation.setGeolocationOverride', $location);
            $this->pause(100);
        });

        /**
         * Example:
         * ```php
         *      $browser->setDateTimeInDatePicker('.datePicker__field .form-control', Carbon::now());
         * ```
         */
        Browser::macro(
            'setDateTimeInDatePicker',
            function (
                string $fieldSelector,
                Carbon $dateTime,
                string $dateFormat = 'F j, Y H:i',
                string $staticWrapper = null
            ) {
                $this->waitFor($fieldSelector);

                $flatPickrDateFormatted = $dateTime->format('F j, Y');
                $targetDateFormatted = $dateTime->format($dateFormat);

                // Open the date picker.
                $this->click($fieldSelector);

                $selectorPrefix = $staticWrapper ? $staticWrapper . ' ' : '';

                // Select the date and set the hour.
                $this->waitFor($selectorPrefix . '.flatpickr-calendar .flatpickr-monthDropdown-months');
                $this->select(
                    $selectorPrefix . '.flatpickr-calendar .flatpickr-monthDropdown-months',
                    $dateTime->month - 1
                );
                $this->click(
                    $selectorPrefix . '.flatpickr-calendar .flatpickr-day[aria-label="' . $flatPickrDateFormatted . '"]'
                );
                $this->type($selectorPrefix . '.flatpickr-calendar .numInput.flatpickr-hour', $dateTime->hour);
                $this->type($selectorPrefix . '.flatpickr-calendar .numInput.flatpickr-minute', $dateTime->minute);
                // Set am/pm if needed.
                if ($amPmSpan = $this->element($selectorPrefix . '.flatpickr-am-pm')) {
                    if ($amPmSpan->getText() === 'AM' && $dateTime->format('A') === 'PM') {
                        $amPmSpan->click();
                    } elseif ($amPmSpan->getText() === 'PM' && $dateTime->format('A') === 'AM') {
                        $amPmSpan->click();
                    }
                }

                if ($this->element($selectorPrefix . '.flatpickr-am-pm')) {
                    $this->assertSeeIn($selectorPrefix . '.flatpickr-am-pm', $dateTime->format('A'));
                }

                // We click this element once more to trigger the state update.
                $this->click(
                    $selectorPrefix . '.flatpickr-calendar .flatpickr-day[aria-label="' . $flatPickrDateFormatted . '"]'
                );
                $this->keys($selectorPrefix . '.flatpickr-calendar .numInput.flatpickr-minute', '{enter}');

                // Check that the date is exactly that what we expected.
                $this->assertInputValue(
                    $fieldSelector,
                    $targetDateFormatted
                );
            }
        );
    }
}
