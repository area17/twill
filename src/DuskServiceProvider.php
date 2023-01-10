<?php

namespace A17\Twill;

use Carbon\Carbon;
use Carbon\Laravel\ServiceProvider;
use Facebook\WebDriver\Chrome\ChromeDevToolsDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Laravel\Dusk\ElementResolver;

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

        Browser::macro('inRow', function (string $stringInTable, \Closure $closure) {
            $resolver = new ElementResolver(
                $this->driver->findElement(WebDriverBy::xpath('//table//tr[td/span//text()[contains(., "' . $stringInTable . '")]]')),
            );

            $browser = new Browser(
                $this->driver,
                $resolver
            );

            $closure($browser);
        });

        Browser::macro('visitTwill', function () {
            if (
                ! Str::contains($this->driver->getCurrentURL(), '/twill') ||
                Str::contains($this->driver->getCurrentURL(), '_dusk')
            ) {
                $this->visit('/twill');
            }
        });

        Browser::macro('visitModuleEntryWithTitle', function (string $menuName, string $title) {
            $this->visitTwill();

            $this->clickLink($menuName);

            $this->waitForText($title);
            $this->clickLink($title);

            $this->assertSee($title);
        });

        Browser::macro('createModuleEntryWithTitle', function (string $menuName, string $title) {
            $this->visitTwill();

            $this->waitForText($menuName);
            $this->clickLink($menuName);

            $this->createWithTitle($title);
        });

        Browser::macro('createWithTitle', function (string $title) {
            $this->press('Add new');

            $this->waitFor('.modal__header');

            if ($this->element('.input-wrapper-title\[en\]')) {
                $this->type('title[en]', $title);
            } else {
                $this->type('title', $title);
            }

            $this->press('Create');

            $this->waitForReload();
        });

        Browser::macro('assertVselectHasOptions', function (string $wrapperClass, array $optionLabels) {
            $this->with($wrapperClass, function (Browser $element) use ($optionLabels) {
                $element->click('.vs__search');
                $element->waitFor('.vs__dropdown-menu');

                foreach ($optionLabels as $optionLabel) {
                    $element->assertSeeIn('.vs__dropdown-menu', $optionLabel);
                }
            });
        });

        Browser::macro('selectVselectOption', function (string $wrapperClass, string $optionLabel) {
            $this->with($wrapperClass, function (Browser $element) use ($optionLabel, $wrapperClass) {
                $element->click('.vs__search');
                $element->waitFor('.vs__dropdown-menu');

                $element->clickAtXPath('//li[contains(.,"' . $optionLabel . '")]');

                $this->assertVselectHasOptionSelected($wrapperClass, $optionLabel);
            });
        });

        Browser::macro('assertVselectHasOptionSelected', function (string $wrapperClass, string $optionLabel) {
            $this->with($wrapperClass, function (Browser $element) use ($optionLabel) {
                $element->assertSeeIn('.vs__selected-options', $optionLabel);
            });
        });

        Browser::macro('pressSaveAndCheckSaved', function (string $saveButtonText = 'Update') {
            $this->pause(200);
            $this->press($saveButtonText);
            $this->waitForText('Content saved. All good!');
        });

        Browser::macro('withinEditor', function (\Closure $closure) {
            $this->press('EDITOR');
            $this->with('.overlay__window', function (Browser $element) use ($closure) {
                $closure($element);

                $element->press('Close');
            });
        });

        Browser::macro('withinNewBlock', function (string $block, \Closure $closure, string $addLabel = 'Add content') {
            $this->press($addLabel);

            $this->waitFor('.dropdown__scroller');

            $this->with('.dropdown__scroller', function (Browser $element) use ($block) {
                $element->press($block);
            });

            $this->with('.blocks .blocks__container div:last-child', function (Browser $element) use ($closure) {
                $for = $element->element('label')?->getAttribute('for');

                $prefix = Str::before($for ?? '', ']') . ']';

                $closure($element, $prefix);
            });

            $this->pause(500);
        });

        Browser::macro('attachImage', function (string $fieldName, string $imagePath) {
            $this->with('.input-wrapper-medias\.' . $fieldName, function (Browser $mediaField) use ($imagePath) {
                $mediaField->waitForText('Attach image');
                $mediaField->press('Attach image');
                $mediaField->elsewhere('.medialibrary', function (Browser $mediaManager) use ($imagePath) {
                    $mediaManager->attach('.uploader__dropzone input', $imagePath);
                    $mediaManager->pause(150);
                    $mediaManager->waitFor('.mediagrid__item');
                    $mediaManager->click('.mediagrid__item:nth-of-type(1)');
                    $mediaManager->press('Insert image');
                });
            });
        });

        Browser::macro('dragBlock', function (string $block, ?\Closure $closure = null) {
            $buttons = $this->elements('.editorSidebar__button');

            $index = 1;
            foreach ($buttons as $button) {
                if ($button->findElement(WebDriverBy::className('editorSidebar__buttonLabel'))->getText() === $block) {
                    $this->drag('.editorSidebar__button:nth-of-type(' . $index . ')', '.editorPreview__content');
                    break;
                }
                $index++;
            }

            if ($closure) {
                $this->waitFor('.editorSidebar__body');
                $this->with('.editorSidebar__body', function (Browser $element) use ($closure) {
                    $for = $element->element('label')?->getAttribute('for');
                    $prefix = Str::before($for ?? '', ']') . ']';

                    $closure($element, $prefix);
                });
            }

            $this->with('.editorSidebar__actions', function (Browser $element) {
                $element->press('Done');
            });
        });

        Browser::macro(
            'addBlockWithContent',
            function (string $block, array $fields, string $addLabel = 'Add content') {
                $this->press($addLabel);

                $this->waitFor('.dropdown__scroller');

                $this->with('.dropdown__scroller', function (Browser $element) use ($block) {
                    $element->press($block);
                });

                $this->with('.blocks .blocks__container div:last-child', function (Browser $element) use ($fields) {
                    foreach ($fields as $label => $value) {
                        $labelElement = $element->driver
                            ->findElement(WebDriverBy::xpath('.//label[contains(string(), "' . $label . '")]'));
                        $for = $labelElement->getAttribute('for');

                        $wrapper = '.input-wrapper-' .
                            Str::replace(['[', ']'], ['\[', '\]'], Str::beforeLast($for, '-'));

                        if ($wysiwyg = $element->element($wrapper . ' .ql-editor')) {
                            $wysiwyg->sendKeys($value);
                            $this->pause(200);
                            $element->element('.input__label')->click();
                        } else {
                            $field = $element->driver->findElement(WebDriverBy::id($for));
                            $field->sendKeys($value);
                        }
                        $this->pause(200);
                    }
                });

                $this->pause(500);
            }
        );

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
