<?php

namespace A17\Twill\Rector;

use App\Exceptions\Handler;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;

abstract class LaravelAwareRectorRule extends AbstractRector implements ConfigurableRectorInterface
{
    protected Application $app;

    public function configure(array $configuration): void
    {
        $this->app ??= $this->bootstrapLaravel(
            $configuration['path'] ?? getcwd(),
        );
    }

    private function bootstrapLaravel(string $basePath): Application
    {
        $app = new Application($basePath);

        $app->singleton(
            \Illuminate\Contracts\Http\Kernel::class,
            \App\Http\Kernel::class
        );

        $app->singleton(
            Kernel::class,
            \App\Console\Kernel::class
        );

        $app->singleton(
            ExceptionHandler::class,
            Handler::class
        );

        $kernel = $app->make(Kernel::class);
        $kernel->bootstrap();

        return $app;
    }
}
