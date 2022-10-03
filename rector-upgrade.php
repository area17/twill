<?php

declare(strict_types=1);

use A17\Twill\Rector\RenameRoutes;
use A17\Twill\Rector\RenameViews;
use Rector\Core\Configuration\Option;
use Rector\Renaming\Rector\Namespace_\RenameNamespaceRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddMethodCallBasedStrictParamTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationBasedOnParentClassMethodRector;

/**
 * This rector file is the one used by the UpgradeCommand for upgrading users code bases.
 */
return static function (\Rector\Config\RectorConfig $config): void {
    // Refactoring for Twill 2.6 to Twill 3.0.
    $parameters = $config->parameters();
    $parameters->set(Option::PATHS, [
        getcwd() . '/app',
        getcwd() . '/resources',
        getcwd() . '/routes',
        getcwd() . '/config',
    ]);

    $services = $config->services();
    $services->set(RenameRoutes::class)->configure(['path' => getcwd()]);
    $services->set(RenameViews::class)->configure(['path' => getcwd()]);
    $services->set(RenameNamespaceRector::class)->configure([
        'App\Http\Controllers\Admin' => 'App\Http\Controllers\Twill',
        'App\Http\Requests\Admin' => 'App\Http\Requests\Twill',
    ]);

    // I hope this split work.
    // Update compatibility.
    $config->paths([
        getcwd() . '/app/Twill',
        getcwd() . '/app/Http/Controllers/Twill',
        getcwd() . '/app/Http/Requests/Twill',
        getcwd() . '/app/Repositories',
    ]);

    $config->rule(AddReturnTypeDeclarationBasedOnParentClassMethodRector::class);
    $config->rule(AddMethodCallBasedStrictParamTypeRector::class);
};
