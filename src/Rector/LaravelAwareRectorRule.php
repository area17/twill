<?php

namespace A17\Twill\Rector;

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
