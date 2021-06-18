<?php

namespace A17\Twill\Tests\Integration\Commands;

use A17\Twill\Tests\Integration\Behaviors\CopyBlocks;
use A17\Twill\Tests\Integration\TestCase;
use Symfony\Component\Console\Exception\RuntimeException;

class MakeBlockTest extends TestCase
{
    use CopyBlocks;

    public function setup(): void
    {
        parent::setUp();

        $this->copyBlocks();
    }

    public function testRaiseExceptionWhenMissingArguments()
    {
        $this->expectException(RuntimeException::class);

        $this->artisan('twill:make:block')->run();
    }

    public function testCanExecuteModuleCommand()
    {
        $pendingCommand = $this->artisan($command = 'twill:make:block', [
            'name' => 'SuperQuote',
            'base' => 'quote',
            'icon' => 'text',
        ]);

        $this->getCommand($command)
            ->getBlockMaker()
            ->getBlockCollection()
            ->load();

        $this->assertExitCodeIsGood($pendingCommand->run());

        $this->assertFileExists(
            config('twill.block_editor.directories.destination.blocks') .
            '/super-quote.blade.php'
        );
    }

    public function testCanMakeBlockWithRepeater()
    {
        $pendingCommand = $this->artisan($command = 'twill:make:block', [
            'name' => 'SuperCarousel',
            'base' => 'carousel',
            'icon' => 'text',
        ]);

        $this->getCommand($command)
            ->getBlockMaker()
            ->getBlockCollection()
            ->load();

        $this->assertExitCodeIsGood($pendingCommand->run());

        $this->assertFileExists(
            config('twill.block_editor.directories.destination.blocks') .
            '/super-carousel.blade.php'
        );

        $this->assertFileExists(
            config('twill.block_editor.directories.destination.repeaters') .
            '/super-carousel-item.blade.php'
        );
    }

    public function testCannotMakeBlockWithMissingBase()
    {
        $pendingCommand = $this->artisan($command = 'twill:make:block', [
            'name' => 'WillBeMissed',
            'base' => 'missing-block',
            'icon' => 'text',
        ]);

        $this->getCommand($command)
            ->getBlockMaker()
            ->getBlockCollection()
            ->load();

        $this->assertExitCodeIsNotGood($pendingCommand->run());
    }

    public function testCannotMakeBlockWithMissingIcon()
    {
        $pendingCommand = $this->artisan($command = 'twill:make:block', [
            'name' => 'IconWillBeMissed',
            'base' => 'quote',
            'icon' => 'missing-icon',
        ]);

        $this->getCommand($command)
            ->getBlockMaker()
            ->getBlockCollection()
            ->load();

        $this->assertExitCodeIsNotGood($pendingCommand->run());
    }

    public function testCannotMakeAlreadyExistingBlock()
    {
        $pendingCommand = $this->artisan($command = 'twill:make:block', [
            'name' => 'SuperQuote',
            'base' => 'quote',
            'icon' => 'text',
        ]);

        $this->getCommand($command)
            ->getBlockMaker()
            ->getBlockCollection()
            ->load();

        $this->assertExitCodeIsGood($pendingCommand->run());

        $pendingCommand = $this->artisan($command = 'twill:make:block', [
            'name' => 'SuperQuote',
            'base' => 'quote',
            'icon' => 'text',
        ]);

        $this->getCommand($command)
            ->getBlockMaker()
            ->getBlockCollection()
            ->load();

        $this->assertExitCodeIsNotGood($pendingCommand->run());
    }
}
