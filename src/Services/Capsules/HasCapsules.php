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
        $capsule['plural'] = $name = $capsule['name'];

        $capsule['singular'] = $singular =
            $capsule['singular'] ?? Str::singular($name);

        $twillNamespace = config('twill.namespace');

        $capsuleNamespace = "{$twillNamespace}\Twill\Capsules\\{$name}";

        $models = "{$capsuleNamespace}\Data\Models";

        $capsule['psr4_path'] = "$basePath/{$name}/app";

        $capsule['namespace'] = $capsuleNamespace;

        $capsule['root_path'] = $this->capsuleRootPath($capsule);

        $capsule[
            'migrations_dir'
        ] = "{$capsule['root_path']}/database/migrations";

        $capsule['views_dir'] = "{$capsule['root_path']}/resources/views";

        $capsule['view_prefix'] = "{$name}.resources.views.admin";

        $capsule['routes_file'] = "{$capsule['root_path']}/routes/admin.php";

        $capsule['model'] = "{$capsuleNamespace}\\Data\Models\\{$singular}";

        $capsule['translation'] = "{$models}\\{$singular}Translation";

        $capsule['slug'] = "{$models}\\{$singular}Slug";

        $capsule['revision'] = "{$models}\\{$singular}Revision";

        $capsule[
            'repository'
        ] = "{$capsule['namespace']}\\Data\Repositories\\{$singular}Repository";

        $capsule[
            'controller'
        ] = "{$capsule['namespace']}\\Http\Controllers\\{$singular}Controller";

        $capsule[
            'formRequest'
        ] = "{$capsule['namespace']}\\Http\Requests\\{$singular}Request";

        $this->registerPsr4Autoloader($capsule);

        return $capsule;
    }

    public function registerPsr4Autoloader($capsule)
    {
        $this->getAutoloader()->setPsr4(
            $capsule['namespace'] . '\\',
            $capsule['psr4_path']
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
}
