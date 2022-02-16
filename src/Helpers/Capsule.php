<?php

namespace A17\Twill\Helpers;

use A17\Twill\Facades\TwillRoutes;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class Capsule
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $path;

    /**
     * @var bool
     */
    public $enabled;

    /**
     * @var bool
     */
    public $packageCapsule = false;

    /**
     * @var bool
     */
    public $loaded = false;

    /**
     * @var string|null
     */
    private $singular;

    /**
     * @var string
     */
    private $namespace;

    public function __construct(
        string $name,
        string $namespace,
        string $path,
        string $singular = null,
        bool $enabled = true,
        bool $packageCapsule = false
    ) {
        $this->name = $name;
        $this->path = $path;
        $this->enabled = $enabled;
        $this->namespace = $namespace;
        $this->singular = $singular;
        $this->packageCapsule = $packageCapsule;

        $this->boot();
    }

    public function boot(): void
    {
        $this->autoloadConfigFiles();
        $this->registerServiceProvider();
        $this->registerViews();
        $this->loadMigrations();

        if ($this->packageCapsule) {
            $this->registerConfig();
        }

        $this->registerRoutes();
        $this->loadTranslations();

        $this->loaded = true;
    }

    public function registerServiceProvider(): void
    {
        $serviceProviderName = $this->name . 'CapsuleServiceProvider';

        if (File::exists($this->path . '/' . $serviceProviderName . '.php')) {
            // @todo: test.
            App::register($this->namespace . '\\' . $serviceProviderName);
        }
    }

    public function autoloadConfigFiles(): void
    {
        $files = $this->getConfig()['autoload']['files'] ?? null;

        if (blank($files)) {
            return;
        }

        collect($files)->each(function ($file) {
            if (file_exists($file)) {
                require_once $file;
            }
        });
    }

    public function registerViews(): void
    {
        View::addLocation(Str::replaceLast('/' . $this->name, '', $this->path));
    }

    public function loadMigrations(): void
    {
        $callback = function (Migrator $migrator) {
            $migrator->path($this->getMigrationsPath());
        };

        App()->afterResolving('migrator', $callback);

        if (app()->resolved('migrator')) {
            $callback(App::make('migrator'));
        }
    }

    public function registerRoutes(): void {
        TwillRoutes::registerCapsuleRoutes(App::get('router'), $this);
    }

    public function loadTranslations(): void {
        $callback = function (Translator $translator) {
            $translator->addNamespace($this->getLanguagesPath(), 'twill:capsules:' . $this->getModule());
        };

        App()->afterResolving('translator', $callback);

        if (app()->resolved('translator')) {
            $callback(App::make('translator'));
        }
    }

    public function getBasePath(string $path): string
    {
        $exploded = explode('/', $path);
        return implode('/', array_pop($exploded));
    }

    public function getModule(): string
    {
        return Str::camel($this->name);
    }

    public function getDisplayName(): string
    {
        return Str::studly($this->name);
    }

    public function getPlural(): string
    {
        return $this->name;
    }

    public function getSingular(): string
    {
        return $this->singular ?? Str::singular($this->name);
    }

    public function getBaseNamespace(): string
    {
        $explodedNamespace = explode('\\', $this->namespace);
        return implode('\\', array_pop($explodedNamespace));
    }

    public function getDatabaseNamespace(): string
    {
        return $this->namespace . '\\Database';
    }

    public function getDatabasePsr4Path(): string
    {
        return $this->path . '/database';
    }

    public function getSeedsNamespace(): string
    {
        return $this->namespace . '\\Seeds\\Database';
    }

    public function getSeedsPsr4Path(): string
    {
        return $this->getDatabasePsr4Path() . '/seeds';
    }

    public function getMigrationsPath(): string
    {
        return $this->getDatabasePsr4Path() . '/migrations';
    }

    public function getResourcesPath(): string
    {
        return $this->getPsr4Path() . '/resources';
    }

    public function getLanguagesPath(): string
    {
        return $this->getResourcesPath() . '/lang';
    }

    public function getViewsPath(): string
    {
        return $this->getResourcesPath() . '/views';
    }

    public function getModelNamespace(): string
    {
        // @todo: config('twill.capsules.namespaces.models');
        return $this->namespace . '\\Models';
    }

    public function getModelsDir(): string
    {
        return $this->getPsr4Path() . '/Models';
    }

    public function getRepositoriesNamespace(): string
    {
        // @todo: config('twill.capsules.namespaces.repositories');
        return $this->namespace . '\\Repositories';
    }

    public function getRepositoriesDir(): string
    {
        return $this->getPsr4Path() . '/Repositories';
    }

    public function getControllersNamespace(): string
    {
        // @todo: config('twill.capsules.namespaces.controllers');
        return $this->namespace . '\\Http\\Controllers';
    }

    public function getControllersDir(): string
    {
        return $this->getPsr4Path() . '/Http/Controllers';
    }

    public function getRequestsNamespace(): string
    {
        // @todo: config('twill.capsules.namespaces.requests');
        return $this->namespace . '\\Http\\Requests';
    }

    public function getRequestsDir(): string
    {
        return $this->getPsr4Path() . '/Http/Requests';
    }

    public function getPsr4Path(): string
    {
        // @todo: config('twill.capsules.namespaces.subdir');
        return $this->path;
    }

    public function getViewPrefix(): string
    {
        return "{$this->getModule()}.resources.views.admin";
    }

    public function getRoutesFile(): string
    {
        return $this->getPsr4Path() . '/routes/admin.php';
    }

    public function routesFileExists(): bool
    {
        return file_exists($this->getRoutesFile());
    }

    public function getModel(): string
    {
        return $this->getModelNamespace() . '\\' . $this->getSingular();
    }

    public function getTranslationModel(): string
    {
        return $this->getModelNamespace() . '\\' . $this->getSingular() . 'Translation';
    }

    public function getSlugModel(): string
    {
        return $this->getModelNamespace() . '\\' . $this->getSingular() . 'Slug';
    }

    public function getRevisionModel(): string
    {
        return $this->getModelNamespace() . '\\' . $this->getSingular() . 'Revision';
    }

    public function getRepositoryClass(): string
    {
        return $this->getRepositoriesNamespace() . '\\' . $this->getSingular() . 'Repository';
    }

    public function getControllerClass(): string
    {
        return $this->getControllersNamespace() . '\\' . $this->getSingular() . 'Controller';
    }

    public function getFormRequestClass(): string
    {
        return $this->getRequestsNamespace() . '\\' . $this->getSingular() . 'Request';
    }

    public function getConfigFile(): string
    {
        return $this->path . '/config.php';
    }

    public function getConfig(): array
    {
        if (file_exists($this->getConfigFile())) {
            return require $this->getConfigFile();
        }
        return [];
    }

    public function registerConfig(): void
    {
        $config = Config::get('twill-navigation', []);

        $config[$this->name] = [
            'title' => $this->name,
            'module' => true,
        ];

        Config::set('twill-navigation', $config);
    }

    public function getType(): string {
        return '';
    }
}
