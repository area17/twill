<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Commands\GenerateBlocks;

class CommandsTest extends TestCase
{
    public function testModuleCommand()
    {
        $this->artisan('twill:module', ['moduleName' => 'Posts']);

        $this->assertFileExists(
            twill_path('Http/Controllers/Admin/PostController.php')
        );

        $this->assertIsObject(
            $this->app->make(\App\Http\Controllers\Admin\PostController::class)
        );
    }

    public function testBlocksCommand()
    {
        $this->deleteDirectory($path = resource_path('views/admin/blocks'));
        $this->deleteDirectory(resource_path('assets/js/blocks'));

        $this->artisan('twill:blocks')->expectsOutput(
            GenerateBlocks::NO_BLOCKS_DEFINED
        );

        $this->files->makeDirectory(
            resource_path('views/admin/blocks'),
            0755,
            true
        );
        $this->files->makeDirectory(
            resource_path('assets/js/blocks'),
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
            resource_path('assets/js/blocks/BlockQuote.vue')
        );

        $this->assertEquals(
            read_file(stubs('blocks/BlockQuote.vue')),
            read_file(resource_path('assets/js/blocks/BlockQuote.vue'))
        );
    }
}
