<?php

namespace A17\Twill\Tests\Integration\Commands;

use A17\Twill\Tests\Integration\TestCase;

class MakeModuleTest extends TestCase
{
    public function testCanExecuteModuleCommand()
    {
        $this->assertExitCodeIsGood(
            $this->artisan('twill:make:module', [
                'moduleName' => 'Posts',
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
            twill_path('Http/Controllers/Twill/PostController.php')
        );

        $this->assertIsObject(
            $this->app->make(\App\Http\Controllers\Twill\PostController::class)
        );
    }
}
