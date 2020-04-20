<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Commands\GenerateBlocks;
use A17\Twill\Models\User;

class CommandsTest extends TestCase
{
    public function testCanExecuteModuleCommand()
    {
        $this->artisan('twill:module', [
            'moduleName' => 'Posts',
            '--hasBlocks' => true,
            '--hasTranslation' => true,
            '--hasSlug' => true,
            '--hasMedias' => true,
            '--hasFiles' => true,
            '--hasPosition' => true,
            '--hasRevisions' => true,
        ]);

        $this->assertFileExists(
            twill_path('Http/Controllers/Admin/PostController.php')
        );

        $this->assertIsObject(
            $this->app->make(\App\Http\Controllers\Admin\PostController::class)
        );
    }

    public function testCanExecuteBlocksCommand()
    {
        $this->deleteDirectory($path = resource_path('views/admin/blocks'));
        $this->deleteDirectory(resource_path(config('twill.custom_vue_blocks_resource_path', 'assets/js/blocks')));

        $this->artisan('twill:blocks')->expectsOutput(
            GenerateBlocks::NO_BLOCKS_DEFINED
        );

        $this->files->makeDirectory(
            resource_path('views/admin/blocks'),
            0755,
            true
        );
        $this->files->makeDirectory(
            resource_path(config('twill.custom_vue_blocks_resource_path', 'assets/js/blocks')),
            0755,
            true
        );

        $this->artisan('twill:blocks')->expectsOutput(
            GenerateBlocks::SCANNING_BLOCKS
        );

        $this->files->copy(
            stubs('blocks/quote.blade.php'),
            $path . '/quote.blade.php'
        );

        $this->artisan('twill:blocks')->expectsOutput(
            'Block Quote generated successfully'
        );

        $this->assertFileExists(
            resource_path(config('twill.custom_vue_blocks_resource_path', 'assets/js/blocks') . '/BlockQuote.vue')
        );

        $this->assertEquals(
            read_file(stubs('blocks/BlockQuote.vue')),
            read_file(resource_path(config('twill.custom_vue_blocks_resource_path', 'assets/js/blocks') . '/BlockQuote.vue'))
        );
    }

    public function testCanExecuteSuperadminCommand()
    {
        $this->artisan('twill:superadmin')
            ->expectsQuestion('Enter an email', $this->superAdmin(true)->email)
            ->expectsQuestion('Enter a password', $this->superAdmin()->password)
            ->expectsQuestion(
                'Confirm the password',
                $this->superAdmin()->password
            );

        $this->assertNotNull(
            User::where('email', $this->superAdmin()->email)->first()
        );
    }

    public function testCanExecuteUpdateCommand()
    {
        $this->artisan('twill:update');

        $this->assertTrue(true);
    }
}
