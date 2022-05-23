<?php

namespace A17\Twill\Rector;

use Illuminate\Foundation\Application;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;

abstract class LaravelAwareRectorRule extends AbstractRector implements ConfigurableRectorInterface
{
    public $baseDir = null;

    public function configure(array $configuration): void
    {
        $this->baseDir = $configuration['path'] ?? getcwd();
    }

    protected function getLaravel(): Application
    {
        $app = new Application($this->baseDir);
        $app->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \App\Http\Kernel::class
        );

        $app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            \App\Console\Kernel::class
        );

        $app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \App\Exceptions\Handler::class
        );

        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        return $app;
    }
}
