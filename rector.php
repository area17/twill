<?php

declare(strict_types=1);

use A17\Twill\Rector\RenameRoutes;
use A17\Twill\Rector\RenameViews;
use Rector\Core\Configuration\Option;
use Rector\Renaming\Rector\Namespace_\RenameNamespaceRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Refactoring for Twill 2.6 to Twill 3.0.
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        getcwd() . '/app',
        getcwd() . '/resources',
        getcwd() . '/routes',
        getcwd() . '/config',
    ]);

    $services = $containerConfigurator->services();
    $services->set(RenameRoutes::class)->configure(['path' =>  getcwd()]);
    $services->set(RenameViews::class)->configure(['path' =>  getcwd()]);
    $services->set(RenameNamespaceRector::class)->configure([
        'App\Http\Controllers\Admin' => 'App\Http\Controllers\Twill',
        'App\Http\Requests\Admin' => 'App\Http\Requests\Twill',
        'App\Repositories' => 'App\Repositories\Twill',
    ]);
};

