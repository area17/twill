<?php

declare(strict_types=1);

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Repositories\ModuleRepository;
use PHPStan\Type\ArrayType;
use PHPStan\Type\MixedType;
use Rector\CodingStyle\Rector\ClassMethod\MakeInheritedMethodVisibilitySameAsParentRector;
use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php80\Rector\ClassMethod\AddParamBasedOnParentClassMethodRector;
use Rector\Renaming\Rector\Namespace_\RenameNamespaceRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddMethodCallBasedStrictParamTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationBasedOnParentClassMethodRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ParamTypeByParentCallTypeRector;
use Rector\TypeDeclaration\Rector\Property\AddPropertyTypeDeclarationRector;
use Rector\TypeDeclaration\ValueObject\AddPropertyTypeDeclaration;

return static function (RectorConfig $config): void {

    $paths = [
        getcwd() . '/app/',
        getcwd() . '/resources/',
        getcwd() . '/routes/',
        getcwd() . '/config/',
    ];

    $enablePaths = [];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            $enablePaths[] = $path;
        }
    }

    // Update compatibility.
    $config->paths($enablePaths);
    $config->importNames(false);
    $config->importShortClasses(false);
    $config->phpVersion(PhpVersion::PHP_80);
    $config->disableParallel();


    $config->ruleWithConfiguration(RenameNamespaceRector::class, [
        'App\Http\Controllers\Admin' => 'App\Http\Controllers\Twill',
        'App\Http\Requests\Admin' => 'App\Http\Requests\Twill',
    ]);

    $config->rule(AddReturnTypeDeclarationBasedOnParentClassMethodRector::class);
    $config->rule(AddMethodCallBasedStrictParamTypeRector::class);
    $config->rule(MakeInheritedMethodVisibilitySameAsParentRector::class);
    $config->rule(ParamTypeByParentCallTypeRector::class);
    $config->rule(AddParamBasedOnParentClassMethodRector::class);

    $config->ruleWithConfiguration(AddPropertyTypeDeclarationRector::class, [
        new AddPropertyTypeDeclaration(
            ModuleRepository::class,
            'fieldsGroups',
            new ArrayType(new MixedType(), new MixedType())
        ),
        new AddPropertyTypeDeclaration(
            ModuleRepository::class,
            'model',
            new PHPStan\Type\ObjectType(TwillModelContract::class)
        ),
    ]);
};
