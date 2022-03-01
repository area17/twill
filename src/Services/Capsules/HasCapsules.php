<?php

namespace A17\Twill\Services\Capsules;

use Illuminate\Support\Facades\File;
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
        $path = $this->getCapsulesPath();

        $list = collect(config('twill.capsules.list'));

        if (!config('twill.capsules.loaded')) {
            $list = $list
                ->where('enabled', true)
                ->map(function ($capsule) use ($path) {
                    return $this->makeCapsule($capsule, $path);
                });
        }

        return $list;
    }

    public function getCapsuleByModel($model)
    {
        return $this->getCapsuleList()
            ->where('singular', Str::studly($model))
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

    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function getCapsulesPath()
    {
        return config('twill.capsules.path');
    }

    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function getCapsulesSubdir()
    {
        return config('twill.capsules.namespaces.subdir');
    }

    public function makeCapsule($capsule, $basePath = null): array
    {
        $basePath = $basePath ?? $this->getCapsulesPath();

        $capsule['name'] = Str::studly($capsule['name']);

        $capsule['module'] = Str::camel($capsule['name']);

        $capsule['plural'] = $name = $capsule['name'];

        $capsule['singular'] = $singular =
            $capsule['singular'] ?? Str::singular($name);

        $twillNamespace = config('twill.namespace');

        $capsule['base_namespace'] = config('twill.capsules.namespaces.base');

        $capsule['namespace'] = $capsuleNamespace = $this->getManager()->capsuleNamespace(
            $capsule['name']
        );

        $capsule['database_namespace'] = "$capsuleNamespace\Database";

        $capsule['seeds_namespace'] = "{$capsule['database_namespace']}\Seeds";

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

        $capsule['psr4_path'] =
            "$basePath/{$name}" .
            (filled($this->getCapsulesSubdir())
                ? $this->getCapsulesSubdir() . '/'
                : '');

        $capsule['base_path'] = $basePath;

        $capsule['database_psr4_path'] = "$basePath/{$name}/database";

        $capsule['seeds_psr4_path'] = "{$capsule['database_psr4_path']}/seeds";

        $capsule['root_path'] = $root = $this->capsuleRootPath($capsule);

        $capsule['migrations_dir'] = "{$capsule['root_path']}/database/migrations";

        $capsule['lang_dir'] = "{$capsule['root_path']}/resources/lang";

        $capsule['views_dir'] = "{$capsule['root_path']}/resources/views";

        $capsule['view_prefix'] = "{$name}.resources.views.admin";

        $capsule['routes_file'] = "{$capsule['root_path']}/routes/twill.php";

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

        $capsule['config_file'] = "$basePath/{$name}/config.php";

        $capsule['config'] = $this->loadCapsuleConfig($capsule);

        return $capsule;
    }

    public function bootstrapCapsule($capsule): void
    {
        $this->registerPsr4Autoloader($capsule);
        $this->autoloadConfigFiles($capsule);
        $this->registerServiceProvider($capsule);
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

    public function registerServiceProvider($capsule): void
    {
        $rootPath = $this->capsuleRootPath($capsule);
        $capsuleName = $capsule['name'];

        $serviceProviderName = $capsuleName . 'CapsuleServiceProvider';

        if (File::exists($rootPath . '/' . $serviceProviderName . '.php')) {
            $this->app->register($capsule['namespace'] . '\\' . $serviceProviderName);
        }
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
        return $this->getCapsuleByModule(Str::studly($capsule))['view_prefix'] ?? null;
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

        $subdir = $this->getCapsulesSubdir();

        $subdir = filled($subdir) ? "{$subdir}/" : '';

        return "{$rootPath}/{$subdir}" . str_replace('\\', '/', $namespace);
    }

    public function getManager()
    {
        return $this->manager = $this->manager ?? app('twill.capsules.manager');
    }

    public function seedCapsules($illuminateSeeder)
    {
        $twillSeeder = app(CapsuleSeeder::class);

        $this->getCapsuleList()->each(function ($capsule) use (
            $twillSeeder,
            $illuminateSeeder
        ) {
            if (filled($capsuleSeeder = $this->makeCapsuleSeeder($capsule))) {
                $twillSeeder->setCommand($illuminateSeeder->command);

                $twillSeeder->call($capsuleSeeder);
            }
        });
    }

    public function makeCapsuleSeeder($capsule)
    {
        $seeder = "{$capsule['database_namespace']}\\Seeds\DatabaseSeeder";

        if (class_exists($seeder)) {
            return $seeder;
        }

        return null;
    }

    public function capsuleExists($module)
    {
        return filled($this->getCapsuleByModule($module));
    }

    public function capsule($string)
    {
        if (file_exists($string)) {
            return $this->getCapsuleByPath($string);
        }

        if (class_exists($string)) {
            return $this->getCapsuleByClass($string);
        }

        return $this->getCapsuleByModule($string);
    }

    public function getCapsuleByPath($path)
    {
        $capsule = $this->getCapsuleList()->first();

        if (!Str::startsWith($path, $capsule['base_path'])) {
            return null;
        }

        $name = Str::before(
            Str::after(Str::after($path, $capsule['base_path']), '/'),
            '/'
        );

        $name = "{$capsule['base_path']}/$name";

        return $this->getCapsuleList()
            ->where('root_path', $name)
            ->first();
    }

    public function getCapsuleByClass($class)
    {
        $capsule = $this->getCapsuleList()->first();

        $namespace = Str::beforeLast($class, '\\');

        if (!Str::startsWith($class, $capsule['base_namespace'])) {
            return null;
        }

        $name = Str::before(
            Str::after(Str::after($class, $capsule['base_namespace']), '\\'),
            '\\'
        );

        $name = "{$capsule['base_namespace']}\\$name";

        return $this->getCapsuleList()
            ->where('namespace', $name)
            ->first();
    }

    public function loadCapsuleConfig($capsule)
    {
        $config = file_exists($file = $capsule['config_file'] ?? 'MISSING-CONFIG-FILE')
            ? require $file
            : [];

        $key =
            config('twill.capsules.capsule_config_prefix') .
            ".{$capsule['module']}";

        config([
            $key => array_replace_recursive(
                $config ?? [],
                $capsule['config'] ?? []
            ),
        ]);

        return $config;
    }

    public function autoloadConfigFiles($capsule)
    {
        $files = $capsule['config']['autoload']['files'] ?? null;

        if (blank($files)) {
            return;
        }

        collect($files)->each(function ($file) {
            if (file_exists($file)) {
                require_once $file;
            }
        });
    }
}
