<?php

namespace A17\Twill\Tests\Integration\Commands;

use A17\Twill\Tests\Integration\TestCase;

class MakeSingletonTest extends TestCase
{
    public function testCanExecuteCommand()
    {
        $this->assertExitCodeIsGood(
            $this->artisan('twill:make:singleton', [
                'moduleName' => 'homepage',
                '--hasBlocks' => true,
                '--hasTranslation' => true,
                '--hasSlug' => true,
                '--hasMedias' => true,
                '--hasFiles' => true,
                '--hasRevisions' => true,
            ])
            ->expectsConfirmation('Do you also want to generate the preview file?', 'no')
            ->run()
        );

        $this->assertFileExists(
            twill_path('Http/Controllers/Admin/HomepageController.php')
        );

        $this->assertIsObject(
            $this->app->make(\App\Http\Controllers\Admin\HomepageController::class)
        );
    }
}
