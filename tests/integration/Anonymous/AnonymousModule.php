<?php

namespace A17\Twill\Tests\Integration\Anonymous;

use A17\Twill\Http\Controllers\Admin\ModuleController;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\Model;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Services\Listings\TableColumns;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use A17\Twill\Repositories\ModuleRepository;
use Nette\PhpGenerator\PhpFile;

class AnonymousModule
{
    private ?TableColumns $tableColumns = null;
    private ?Form $formFields = null;
    private array $setupMethods = [];
    public array $fields = ['title' => []];

    protected function __construct(public string $namePlural, public Application $app)
    {
    }

    public static function make(string $namePlural, Application $app): self
    {
        return new self($namePlural, $app);
    }

    public function withTableColumns(TableColumns $tableColumns)
    {
        $this->tableColumns = $tableColumns;
        return $this;
    }

    public function withFormFields(Form $formFields)
    {
        $this->formFields = $formFields;
        return $this;
    }

    public function withSetupMethods(array $setupMethods)
    {
        $this->setupMethods = $setupMethods;
        return $this;
    }

    /**
     * $fields  is an array that needs the field name as key, and an array as value.
     *
     * The array can contain:
     * ['default' => 'The default value'] => default is null for string, false for boolean
     * ['type' => 'The field type (string|boolean)'] => default is string
     * ['nullable' => 'The field type (string|boolean)'] => default is false
     */
    public function withFields(array $fields): self
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Boots the anonymous module and returns the model class.
     */
    public function boot(): string
    {
        $createClass = false;
        // The module class is the one thing we really need.
        if ($createClass) {
            // When createclass is set to true we create an actual module file. This is usefull when you need to test
            // relational content.
            // This is not used as it is not 100% done.
            $file = new PhpFile();

            $modelName = Str::singular(Str::studly($this->namePlural));
            $modelClassName = 'App\Models\\' . $modelName;
            $modelClass = "\\$modelClassName";

            $class = $file->addClass('App\Models\\' . $modelName);

            $class->addProperty('fillable', array_keys($this->fields));
            $class->addProperty(
                'translatedAttributes',
                collect($this->fields)
                    ->where('translatable', true)
                    ->keys()
                    ->all()
            );
            $class->setExtends(Model::class);

            $bp = base_path();
            $classTargetDir = $bp . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Models';
            if (!file_exists($classTargetDir)) {
                mkdir($classTargetDir, 0777, true);
            }

            $targetFile = $classTargetDir . DIRECTORY_SEPARATOR . $modelName . '.php';
            if (!file_exists($targetFile)) {
                file_put_contents($targetFile, (string)$file);
            }
            include_once($targetFile);
        } else {
            // For simpler modules we can just keep the anonymous classes.
            $modelClass = new class(
                [],
                $this->fields,
                $this->namePlural
            ) extends Model {
                public static array $setProps = [];

                public function __construct(
                    array $attributes = [],
                    array $fields = [],
                    string $namePlural = '',
                ) {
                    if ($namePlural !== '') {
                        self::$setProps['fillable'] = array_keys($fields);
                        self::$setProps['table'] = $namePlural;
                        self::$setProps['dates'] = collect($fields)
                            ->where('type', 'dateTime')
                            ->keys()
                            ->all();
                        self::$setProps['translatedAttributes'] = collect($fields)
                            ->where('translatable', true)
                            ->keys()
                            ->all();
                    } else {
                        foreach (self::$setProps as $prop => $value) {
                            $this->{$prop} = $value;
                        }
                    }
                    parent::__construct($attributes);
                }
            };

            $modelClass = $modelClass::class;
        }

        // Create the migration class.
        $migration = new class($this->namePlural, $this->fields) extends Migration {
            public string $nameSingular;

            public function __construct(public string $namePlural, public array $fields)
            {
                $this->nameSingular = Str::singular($this->namePlural);
            }

            public function up(): void
            {
                // Only create the schema if it does not exist yet.
                if (Schema::hasTable($this->namePlural)) {
                    return;
                }

                Schema::create($this->namePlural, function (Blueprint $table) {
                    createDefaultTableFields($table);

                    foreach (collect($this->fields)->where('translatable', false) as $fieldName => $data) {
                        if (!isset($data['type']) || $data['type'] === 'string') {
                            $table->string($fieldName)
                                ->default($data['default'] ?? null)
                                ->nullable($data['nullable'] ?? true);
                        } elseif ($data['type'] === 'boolean') {
                            $table->boolean($fieldName)
                                ->default($data['default'] ?? false)
                                ->nullable($data['nullable'] ?? true);
                        } elseif ($data['type'] === 'dateTime') {
                            $table->dateTime($fieldName)
                                ->default($data['default'] ?? null)
                                ->nullable($data['nullable'] ?? true);
                        }
                    }
                });

                Schema::create($this->nameSingular . '_translations', function (Blueprint $table) {
                    createDefaultTranslationsTableFields($table, $this->nameSingular);

                    foreach (collect($this->fields)->where('translatable', true) as $fieldName => $data) {
                        $table->string($fieldName);
                    }
                });

                Schema::create($this->nameSingular . '_slugs', function (Blueprint $table) {
                    createDefaultSlugsTableFields($table, $this->nameSingular);
                });

                Schema::create($this->nameSingular . '_revisions', function (Blueprint $table) {
                    createDefaultRevisionsTableFields($table, $this->nameSingular);
                });
            }

            public function down(): void
            {
                Schema::dropIfExists($this->nameSingular . '_revisions');
                Schema::dropIfExists($this->nameSingular . '_translations');
                Schema::dropIfExists($this->nameSingular . '_slugs');
                Schema::dropIfExists($this->namePlural);
            }
        };

        // Cleanup only when requested.
        //        $migration->down();
        $migration->up();

        // Build the controller class.
        $controller = new class(
            $this->app,
            new Request(),
            $this->namePlural,
            $modelClass,
            $this->tableColumns,
            $this->formFields,
            $this->setupMethods
        ) extends ModuleController {
            public static array $setProps;
            public string $modelClass = '';

            public function __construct(
                Application $app,
                Request $request,
                public $moduleName = null,
                ?string $modelClass = null,
                ?TableColumns $tableColumns = null,
                ?Form $formFields = null,
                ?array $setupMethods = null
            ) {
                if ($modelClass) {
                    self::$setProps['setTableColumns'] = $tableColumns;
                    self::$setProps['setFormFields'] = $formFields;
                    self::$setProps['setSetupMethods'] = $setupMethods ?? [];
                    self::$setProps['moduleName'] = $this->moduleName;
                    self::$setProps['moduleClass'] = $modelClass;
                    $this->modelClass = $modelClass;
                } else {
                    foreach (self::$setProps as $prop => $value) {
                        if (!str_starts_with('set', $prop)) {
                            // For regular props, we can write them to the model instantly.
                            $this->{$prop} = $value;
                        }
                    }
                }

                parent::__construct($app, $request);

                if (!isset($this->user) && $request->user()) {
                    $this->user = $request->user();
                }
                if (!isset($this->user)) {
                    $this->user = Auth::guard('twill_users')->user();
                }
            }

            public function setUpController(): void
            {
                foreach (self::$setProps['setSetupMethods'] as $method) {
                    $this->{$method}();
                }
            }

            public function getForm(TwillModelContract $model): Form
            {
                if (self::$setProps['setFormFields'] !== null) {
                    return self::$setProps['setFormFields'];
                }
                return parent::getForm($model);
            }

            public function getIndexTableColumns(): TableColumns
            {
                if (self::$setProps['setTableColumns'] !== null) {
                    return self::$setProps['setTableColumns'];
                }
                return parent::getIndexTableColumns();
            }

            public function getFormRequestClass()
            {
                $request = new class() extends \A17\Twill\Http\Requests\Admin\Request {
                };

                return $request::class;
            }

            public function getRepositoryClass($model)
            {
                $repository = new class(null, $this->modelClass) extends ModuleRepository {
                    public static $setProps = [];

                    public function __construct($model = null, $modelType = null)
                    {
                        if ($modelType) {
                            self::$setProps['modelType'] = $modelType;
                        }

                        if ($model) {
                            $this->model = $model;
                        } else {
                            $this->model = new self::$setProps['modelType']();
                        }
                    }
                };

                return $repository::class;
            }
        };

        $controllerClass = $controller::class;

        // Generate twill module routes.
        $this->buildAnonymousRoutes($this->namePlural, $controllerClass);

        /** @var \Illuminate\Routing\Router $router */
        $router = app()->make('router');
        $router->getRoutes()->refreshNameLookups();

        $this->app['config']['twill-navigation.' . $this->namePlural] = [
            'title' => Str::title($this->namePlural),
            'module' => true,
        ];

        // return the model class.
        return $modelClass;
    }

    protected function buildAnonymousRoutes(string $slug, string $className): void
    {
        $slugs = explode('.', $slug);
        $prefixSlug = str_replace('.', '/', $slug);
        Arr::last($slugs);

        $customRoutes = [
            'reorder',
            'publish',
            'bulkPublish',
            'browser',
            'feature',
            'bulkFeature',
            'tags',
            'preview',
            'restore',
            'bulkRestore',
            'forceDelete',
            'bulkForceDelete',
            'bulkDelete',
            'restoreRevision',
            'duplicate',
        ];
        $defaults = [
            'reorder',
            'publish',
            'bulkPublish',
            'browser',
            'feature',
            'bulkFeature',
            'tags',
            'preview',
            'restore',
            'bulkRestore',
            'forceDelete',
            'bulkForceDelete',
            'bulkDelete',
            'restoreRevision',
            'duplicate',
        ];

        if (isset($options['only'])) {
            $customRoutes = array_intersect(
                $defaults,
                (array)$options['only']
            );
        } elseif (isset($options['except'])) {
            $customRoutes = array_diff(
                $defaults,
                (array)$options['except']
            );
        }

        // Check if name will be a duplicate, and prevent if needed/allowed
        $customRoutePrefix = $slug;

        $adminAppPath = config('twill.admin_app_path');

        foreach ($customRoutes as $route) {
            $routeSlug = "$adminAppPath/{$prefixSlug}/{$route}";
            $mapping = [
                'as' => $customRoutePrefix . ".{$route}",
            ];

            if (in_array($route, ['browser', 'tags'])) {
                Route::get($routeSlug, [$className => $route])->name('twill.' . $mapping['as'])
                    ->middleware(['web', 'twill_auth:twill_users']);
            }

            if ($route === 'restoreRevision') {
                Route::get($routeSlug . '/{id}', [$className => $route])->name('twill.' . $mapping['as'])
                    ->middleware(['web', 'twill_auth:twill_users']);
            }

            if (
                in_array($route, [
                    'publish',
                    'feature',
                    'restore',
                    'forceDelete',
                ])
            ) {
                Route::put(
                    $routeSlug,
                    function (Request $request, Application $app) use ($className, $route) {
                        return (new $className($app, $request))->{$route}();
                    }
                )
                    ->name('twill.' . $mapping['as'])
                    ->middleware(['web', 'twill_auth:twill_users']);
            }

            if ($route === 'duplicate' || $route === 'preview') {
                Route::put($routeSlug . '/{id}', [$className => $route])->name('twill.' . $mapping['as'])
                    ->middleware(['web', 'twill_auth:twill_users']);
            }

            if (
                in_array($route, [
                    'reorder',
                    'bulkPublish',
                    'bulkFeature',
                    'bulkDelete',
                    'bulkRestore',
                    'bulkForceDelete',
                ])
            ) {
                Route::post($routeSlug, [$className => $route])->name('twill.' . $mapping['as'])
                    ->middleware(['web', 'twill_auth:twill_users']);
            }
        }

        Route::group(
            [],
            function () use ($slug, $className, $adminAppPath) {
                $arrayToAdd = [
                    'index' => [
                        'path' => '/',
                        'method' => 'index',
                        'type' => 'GET',
                    ],
                    'edit' => [
                        'path' => '/{' . Str::singular($slug) . '}/edit',
                        'method' => 'edit',
                        'type' => 'GET',
                    ],
                    'create' => [
                        'path' => '/create',
                        'method' => 'create',
                        'type' => 'POST',
                    ],
                    'store' => [
                        'path' => '/store',
                        'method' => 'store',
                        'type' => 'POST',
                    ],
                    'destroy' => [
                        'path' => '/{' . Str::singular($slug) . '}',
                        'method' => 'destroy',
                        'type' => 'DELETE',
                    ],
                    'update' => [
                        'path' => '/{' . Str::singular($slug) . '}',
                        'method' => 'update',
                        'type' => 'PUT',
                    ],
                ];

                foreach ($arrayToAdd as $name => $data) {
                    $method = $data['method'];
                    if ($data['type'] === 'GET') {
                        Route::get(
                            $adminAppPath . '/' . $slug . $data['path'],
                            function (Request $request, Application $app, $model = null) use (
                                $className,
                                $method
                            ) {
                                return (new $className($app, $request))->{$method}($model);
                            }
                        )
                            ->middleware(['web', 'twill_auth:twill_users'])
                            ->name('twill.' . $slug . '.' . $name);
                    } elseif ($data['type'] === 'POST') {
                        Route::post(
                            $adminAppPath . '/' . $slug . $data['path'],
                            function (Request $request, Application $app, $model = null) use (
                                $className,
                                $method
                            ) {
                                return (new $className($app, $request))->{$method}($model);
                            }
                        )
                            ->middleware(['web', 'twill_auth:twill_users'])
                            ->name('twill.' . $slug . '.' . $name);
                    } elseif ($data['type'] === 'PUT') {
                        Route::put(
                            $adminAppPath . '/' . $slug . $data['path'],
                            function (Request $request, Application $app, $model = null) use (
                                $className,
                                $method
                            ) {
                                return (new $className($app, $request))->{$method}($model);
                            }
                        )
                            ->middleware(['web', 'twill_auth:twill_users'])
                            ->name('twill.' . $slug . '.' . $name);
                    } elseif ($data['type'] === 'DELETE') {
                        Route::delete(
                            $adminAppPath . '/' . $slug . $data['path'],
                            function (Request $request, Application $app, $model = null) use (
                                $className,
                                $method
                            ) {
                                return (new $className($app, $request))->{$method}($model);
                            }
                        )
                            ->middleware(['web', 'twill_auth:twill_users'])
                            ->name('twill.' . $slug . '.' . $name);
                    }
                }
            }
        );
    }

}
