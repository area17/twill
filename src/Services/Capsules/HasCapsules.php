<?php

namespace A17\Twill\Services\Capsules;

use Illuminate\Support\Str;

trait HasCapsules
{
    protected function getAutoloader()
    {
        return app()->bound('autoloader')
            ? app('autoloader')
            : require base_path('vendor/autoload.php');
    }

    public function getCapsuleList()
    {
        $path = config('twill.capsules.path');

        $list = collect(config('twill.capsules.list'));

        if (config('twill.capsules.loaded')) {
            return $list;
        }

        return $list
            ->where('enabled', true)
            ->map(function ($capsule) use ($path) {
                return $this->makeCapsule($capsule, $path);
            });
    }

    public function getCapsuleByModel($model)
    {
        return $this->getCapsuleList()
            ->where('singular', $model)
            ->first();
    }

    public function getCapsuleByModule($module)
    {
        return $this->getCapsuleList()
            ->filter(function ($capsule) use ($module) {
                return Str::lower($capsule['plural']) == Str::lower($module);
            })
            ->first();
    }

    public function makeCapsule($capsule, $basePath)
    {
        $capsule['name'] = Str::studly($capsule['name']);

        $capsule['module'] = Str::camel($capsule['name']);

        $capsule['plural'] = $name = $capsule['name'];

        $capsule['singular'] = $singular =
            $capsule['singular'] ?? Str::singular($name);

        $twillNamespace = config('twill.namespace');

        $capsule[
            'namespace'
        ] = $capsuleNamespace = $this->getManager()->capsuleNamespace(
            $capsule['name']
        );

        $capsule[
            'database_namespace'
        ] = "$capsuleNamespace\Database";

        $capsule[
            'seeds_namespace'
        ] = "{$capsule['database_namespace']}\Seeds";

        $capsule['model'] = $capsule['models'] = $models =
            "{$capsuleNamespace}\\" .
            config('twill.capsules.namespaces.models');
        $capsule['repositories'] = $repositories =
            "{$capsuleNamespace}\\" .
            config('twill.capsules.namespaces.repositories');
        $capsule['controllers'] = $controllers =
            "{$capsuleNamespace}\\" .
            config('twill.capsules.namespaces.controllers');
        $capsule['requests'] = $requests =
            "{$capsuleNamespace}\\" .
            config('twill.capsules.namespaces.requests');

        $capsule['psr4_path'] = "$basePath/{$name}/app";

        $capsule['database_psr4_path'] = "$basePath/{$name}/database";

        $capsule['seeds_psr4_path'] = "{$capsule['database_psr4_path']}/seeds";

        $capsule['root_path'] = $root = $this->capsuleRootPath($capsule);

        $capsule[
            'migrations_dir'
        ] = "{$capsule['root_path']}/database/migrations";

        $capsule['views_dir'] = "{$capsule['root_path']}/resources/views";

        $capsule['view_prefix'] = "{$name}.resources.views.admin";

        $capsule['routes_file'] = "{$capsule['root_path']}/routes/admin.php";

        $capsule['model'] = "{$models}\\{$singular}";

        $capsule['models_dir'] = $this->namespaceToPath($capsule, $models);

        $capsule['translation'] = "{$models}\\{$singular}Translation";

        $capsule['slug'] = "{$models}\\{$singular}Slug";

        $capsule['revision'] = "{$models}\\{$singular}Revision";

        $capsule['repository'] = "{$repositories}\\{$singular}Repository";

        $capsule['repositories_dir'] = $this->namespaceToPath(
            $capsule,
            $repositories
        );

        $capsule['controller'] = "{$controllers}\\{$singular}Controller";

        $capsule['controllers_dir'] = $this->namespaceToPath(
            $capsule,
            $controllers
        );

        $capsule['formRequest'] = "{$requests}\\{$singular}Request";

        $capsule['requests_dir'] = $this->namespaceToPath($capsule, $requests);

        $this->registerPsr4Autoloader($capsule);

        return $capsule;
    }

    public function registerPsr4Autoloader($capsule)
    {
        $this->getAutoloader()->setPsr4(
            $capsule['namespace'] . '\\',
            $capsule['psr4_path']
        );

        $this->getAutoloader()->setPsr4(
            $capsule['database_namespace'] . '\\',
            $capsule['database_psr4_path']
        );

        $this->getAutoloader()->setPsr4(
            $capsule['database_namespace'] . '\\Seeds\\',
            $capsule['database_psr4_path'] . '/seeds'
        );
    }

    public function capsuleRootPath($capsule)
    {
        return config('twill.capsules.path') . '/' . $capsule['name'] ?? null;
    }

    public function getCapsuleRepositoryClass($model)
    {
        return $this->getCapsuleByModel($model)['repository'] ?? null;
    }

    public function getCapsuleTranslationClass($model)
    {
        return $this->getCapsuleByModel($model)['translation'] ?? null;
    }

    public function getCapsuleSlugClass($model)
    {
        return $this->getCapsuleByModel($model)['slug'] ?? null;
    }

    public function getCapsuleRevisionClass($model)
    {
        return $this->getCapsuleByModel($model)['revision'] ?? null;
    }

    public function getCapsuleFormRequestClass($model)
    {
        return $this->getCapsuleByModel($model)['formRequest'] ?? null;
    }

    public function getCapsuleViewPrefix($capsule)
    {
        return $this->getCapsuleByModule(Str::studly($capsule))[
            'view_prefix'
        ] ?? null;
    }

    public function namespaceToPath($capsule, $namespace)
    {
        return $this->capsuleNamespaceToPath(
            $namespace,
            $capsule['namespace'],
            $capsule['root_path']
        );
    }

    public function capsuleNamespaceToPath(
        $namespace,
        $capsuleNamespace,
        $rootPath
    ) {
        $namespace = Str::after($namespace, $capsuleNamespace . '\\');

        $subdir = config('twill.capsules.namespaces.subdir');

        $subdir = filled($subdir) ? "{$subdir}/" : '';

        return "{$rootPath}/{$subdir}" . str_replace('\\', '/', $namespace);
    }

    public function getManager()
    {
        return $this->manager = $this->manager ?? app('twill.capsules.manager');
    }

    public function seedCapsules()
    {
        $this->getCapsuleList()->each(function ($capsule) {
            if (filled($seeder = $this->makeCapsuleSeeder($capsule))) {
                $seeder->__invoke();
            }
        });
    }

    public function makeCapsuleSeeder($capsule)
    {
        $seeder = "{$capsule['database_namespace']}\\Seeds\DatabaseSeeder";

        if (class_exists($seeder)) {
            return app($seeder);
        }
    }
}
