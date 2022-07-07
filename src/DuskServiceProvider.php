<?php

namespace A17\Twill;

use Carbon\Laravel\ServiceProvider;
use Facebook\WebDriver\Chrome\ChromeDevToolsDriver;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;

class DuskServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Browser::macro('setLocationToParis', function () {
            $this->setLocationToLatLon(48.864716, 2.349014, 'Europe/Paris');
        });

        Browser::macro('setLocationToNewYork', function () {
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

        Browser::macro('assertDontSeeWithinASecond', function (string $string) {
            $text = Arr::wrap($string);

            $message = $this->formatTimeOutMessage('Waited %s seconds for text', implode("', '", $text));

            $attempts = 0;

            return $this->waitUsing(1, 100, function () use ($string, &$attempts) {
                $contains = Str::contains($this->resolver->findOrFail('')->getText(), $string);
                ray($contains);

                if ($contains) {
                    return false;
                }

                if ($attempts === 10) {
                    return true;
                }

                $attempts++;
            }, $message);
        });
    }
}
