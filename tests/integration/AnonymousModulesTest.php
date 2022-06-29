<?php

namespace A17\Twill\Tests\Integration;

use A17\Twill\Facades\TwillRoutes;
use A17\Twill\Repositories\ModuleRepository;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AnonymousModulesTest extends TestCase
{
    public function testCreateAndList(): void
    {
        $this->generateTestModule('servers', ['title' => ['translatable' => false]]);

        $this->actingAs($this->superAdmin(), 'twill_users')->getJson(route('twill.servers.index'))
            ->assertJsonPath('tableData', []);

        $this->actingAs($this->superAdmin(), 'twill_users')->post(route('twill.servers.store'), [
            'title' => 'Test title',
        ])->assertJsonPath('redirect', 'http://twill.test/servers/1/edit');

        $this->actingAs($this->superAdmin(), 'twill_users')->getJson(route('twill.servers.index'))
            ->assertJsonPath('tableData.0.title', '<a href="http://twill.test/servers/1/edit">Test title</a>');
    }

    public function generateTestModule(string $namePlural, array $fields): string
    {
        $modelClass = new class([], $fields, $namePlural) extends \A17\Twill\Models\Model {
            public static $setProps = [];

            public function __construct(array $attributes = [], array $fields = [], string $namePlural = '')
            {
                if (empty(self::$setProps)) {
                    self::$setProps['fillable'] = array_keys($fields);
                    self::$setProps['table'] = $namePlural;
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

        // Create the migration class.
        $migration = new class($namePlural, $fields) extends \Illuminate\Database\Migrations\Migration {
            public string $nameSingular;

            public function __construct(public string $namePlural, public array $fields)
            {
                $this->nameSingular = \Illuminate\Support\Str::singular($this->namePlural);
            }

            public function up(): void
            {
                Schema::create($this->namePlural, function (Blueprint $table) {
                    createDefaultTableFields($table);

                    foreach (collect($this->fields)->where('translatable', false) as $fieldName => $data) {
                        // @todo: implement relations.
                        $table->string($fieldName);
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

        // always cleanup first.
        $migration->down();
        $migration->up();

        // Build the controller class.
        $controller = new class($this->app, new Request(), $namePlural, $modelClass::class) extends
            \A17\Twill\Http\Controllers\Admin\ModuleController {
            public static array $setProps;
            public string $modelClass = '';

            public function __construct(
                Application $app,
                Request $request,
                public $moduleName = null,
                $modelClass = null
            ) {
                if (empty(self::$setProps)) {
                    self::$setProps['moduleName'] = $this->moduleName;
                    self::$setProps['moduleClass'] = $modelClass;
                    $this->modelClass = $modelClass;
                } else {
                    foreach (self::$setProps as $prop => $value) {
                        $this->{$prop} = $value;
                    }
                }
                parent::__construct($app, $request);

                if (!isset($this->user) && $request->user()) {
                    $this->user = $request->user();
                }
            }

            public function getFormRequestClass()
            {
                $repository = new class() extends \A17\Twill\Http\Requests\Admin\Request {
                };

                return $repository::class;
            }

            public function getRepositoryClass($model)
            {
                $repository = new class(null, $this->modelClass) extends ModuleRepository {
                    public static $setProps = [];

                    public function __construct($model = null, $modelType = null)
                    {
                        if (empty(self::$setProps)) {
                            self::$setProps['modelType'] = $modelType;
                        }

                        if ($model) {
                            $this->model = $model;
                        } else {
                            $this->model = self::$setProps['modelType']::make();
                        }
                    }
                };

                return $repository::class;
            }
        };

        $controllerClass = $controller::class;

        // Generate twill module routes.
        self::buildAnonymousRoutes($namePlural, $controllerClass);

        /** @var \Illuminate\Routing\Router $router */
        $router = app()->make('router');
        $router->getRoutes()->refreshNameLookups();

        // return the model class.
        return $modelClass::class;
    }

    public static function buildAnonymousRoutes(string $slug, string $className): void
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

        foreach ($customRoutes as $route) {
            $routeSlug = "{$prefixSlug}/{$route}";
            $mapping = [
                'as' => $customRoutePrefix . ".{$route}",
            ];

            if (in_array($route, ['browser', 'tags'])) {
                Route::get($routeSlug, [$className => $route])->name('twill.' . $mapping['as']);
            }

            if ($route === 'restoreRevision') {
                Route::get($routeSlug . '/{id}', [$className => $route])->name('twill.' . $mapping['as']);
            }

            if (
                in_array($route, [
                    'publish',
                    'feature',
                    'restore',
                    'forceDelete',
                ])
            ) {
                Route::put($routeSlug, [$className => $route])->name('twill.' . $mapping['as']);
            }

            if ($route === 'duplicate' || $route === 'preview') {
                Route::put($routeSlug . '/{id}', [$className => $route])->name('twill.' . $mapping['as']);
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
                Route::post($routeSlug, [$className => $route])->name('twill.' . $mapping['as']);
            }
        }

        Route::group(
            [],
            function () use ($slug, $className) {
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
                ];

                foreach ($arrayToAdd as $name => $data) {
                    $method = $data['method'];
                    if ($data['type'] === 'GET') {
                        Route::get(
                            $slug . $data['path'],
                            function (\Illuminate\Http\Request $request, Application $app) use (
                                $className,
                                $method
                            ) {
                                return (new $className($app, $request))->{$method}();
                            }
                        )->name('twill.' . $slug . '.' . $name);
                    } elseif ($data['type'] === 'POST') {
                        Route::post(
                            $slug . $data['path'],
                            function (\Illuminate\Http\Request $request, Application $app) use (
                                $className,
                                $method
                            ) {
                                return (new $className($app, $request))->{$method}();
                            }
                        )->name('twill.' . $slug . '.' . $name);
                    } elseif ($data['type'] === 'DELETE') {
                        Route::delete(
                            $slug . $data['path'],
                            function (\Illuminate\Http\Request $request, Application $app) use (
                                $className,
                                $method
                            ) {
                                return (new $className($app, $request))->{$method}();
                            }
                        )->name('twill.' . $slug . '.' . $name);
                    }
                }
            }
        );
    }
}
