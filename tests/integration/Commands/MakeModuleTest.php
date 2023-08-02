<?php

namespace A17\Twill\Tests\Integration\Commands;

use A17\Twill\Tests\Integration\TestCase;

class MakeModuleTest extends TestCase
{
    public function testCanExecuteModuleCommand()
    {
        $this->assertExitCodeIsGood(
            $this->artisan('twill:make:module', [
                'moduleName' => 'Example',
                '--hasBlocks' => true,
                '--hasTranslation' => true,
                '--hasSlug' => true,
                '--hasMedias' => true,
                '--hasFiles' => true,
                '--hasPosition' => true,
                '--hasRevisions' => true,
                '--hasNesting' => true,
            ])
            ->expectsConfirmation('Do you also want to generate the preview file?', 'no')
            ->run()
        );

        $this->assertFileExists(
            twill_path('Http/Controllers/Twill/ExampleController.php')
        );

        $this->assertIsObject(
            $this->app->make(\App\Http\Controllers\Twill\ExampleController::class)
        );
    }
}
