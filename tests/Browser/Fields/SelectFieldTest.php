<?php

namespace A17\Twill\Tests\Browser\Fields;

use A17\Twill\Services\Forms\Fields\Select;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Services\Forms\Option;
use A17\Twill\Tests\Browser\BrowserTestCase;
use Laravel\Dusk\Browser;

class SelectFieldTest extends BrowserTestCase
{
    public function testSelectEnabled(): void
    {
        $class = null;
        $this->tweakApplication(function () use (&$class) {
            $class = \A17\Twill\Tests\Integration\Anonymous\AnonymousModule::make('servers', app())
                ->withFields([
                    'title' => [],
                    'option' => [],
                ])
                ->withFormFields(
                    Form::make([
                        Select::make()
                            ->name('option')
                            ->label('Select')
                            ->addOption(new Option('option1', 'option1'))
                            ->addOption(new Option('option2', 'option2')),
                    ])
                )
                ->boot()
                ->getModelClassName();
        });

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superAdmin, 'twill_users');

            $browser->createModuleEntryWithTitle('Servers', 'Example server');

            $browser->assertDontSee('option1');
            $browser->assertDontSee('option2');

            $browser->assertVselectHasOptions('.input-wrapper-option', ['option1', 'option2']);

            $browser->selectVselectOption('.input-wrapper-option', 'option1');
            $browser->pressSaveAndCheckSaved();

            $browser->refresh();
            $browser->assertVselectHasOptionSelected('.input-wrapper-option', 'option1');
        });

        $this->assertEquals('option1', $class::latest()->first()->option);
    }

    public function testSelectDisabled(): void
    {
        $this->tweakApplication(function () {
            \A17\Twill\Tests\Integration\Anonymous\AnonymousModule::make('servers', app())
                ->withFields([
                    'title' => [],
                    'option' => [],
                ])
                ->withFormFields(
                    Form::make([
                        Select::make()
                            ->name('option')
                            ->label('Select')
                            ->disabled()
                            ->addOption(new Option('option1', 'option1'))
                            ->addOption(new Option('option2', 'option2')),
                    ])
                )
                ->boot();
        });

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->superAdmin, 'twill_users');

            $browser->createModuleEntryWithTitle('Servers', 'Example server');

            $browser->assertDisabled('.input-wrapper-option .vs__selected-options input');
        });
    }
}
