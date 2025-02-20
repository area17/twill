<?php

namespace A17\Twill\Tests\Integration\Anonymous;

use A17\Twill\Facades\TwillRoutes;
use A17\Twill\Http\Controllers\Admin\ModuleController;
use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Behaviors\HasRelated;
use A17\Twill\Models\Behaviors\HasRevisions;
use A17\Twill\Models\Behaviors\HasSlug;
use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\Model;
use A17\Twill\Models\Revision;
use A17\Twill\Repositories\Behaviors\HandleBlocks;
use A17\Twill\Repositories\Behaviors\HandleJsonRepeaters;
use A17\Twill\Repositories\Behaviors\HandleMedias;
use A17\Twill\Repositories\Behaviors\HandleRevisions;
use A17\Twill\Repositories\Behaviors\HandleSlugs;
use A17\Twill\Repositories\Behaviors\HandleTranslations;
use A17\Twill\Repositories\ModuleRepository;
use A17\Twill\Services\Forms\Form;
use A17\Twill\Services\Listings\TableColumns;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application as FoundationApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;

/**
 * NOTES: You cannot have multiple of the same class loaded. So this might be a bit difficult when writing tests.
 *
 * When a class already exists we cannot unload it. So we simply check if an instance exists then we increment the
 * className. So when you are using anonymousModules always rely on $modelClass, $controllerClass etc.
 */
class AnonymousModule
{
    private ?TableColumns $tableColumns = null;
    private ?TableColumns $additionalTableColumns = null;

    private ?Form $formFields = null;

    private array $setupMethods = [];

    public array $fields = ['title' => []];

    private array $additionalProps = [];

    private array $related = [];

    /**
     * A array of key (method) and value (class) to add as belongsToMany on the model
     *
     * This adds updateBrowser to the repository.
     */
    private array $belongsToMany = [];

    /**
     * Repeaters that should be handled (class name).
     */
    private array $repeaters = [];

    /**
     * A array of key (method) and value (class) to add as belongsTo on the model
     */
    private array $belongsTo = [];

    private bool $withRevisions = false;

    private bool $withMedia = false;

    private ?string $modelClass = null;

    private ?string $revisionClass = null;

    private ?string $slugClass = null;

    private ?string $controllerClass = null;

    private ?string $modelTranslationClass = null;

    private ?string $repositoryClass = null;

    private PsrPrinter $classPrinter;

    /** @var string[] */
    private array $slugAttributes = [];

    protected function __construct(public string $namePlural, public Application $app)
    {
        $this->classPrinter = new PsrPrinter();
    }

    public static function make(string $namePlural, Application $app): self
    {
        return new self($namePlural, $app);
    }

    public function withRevisions(): self
    {
        $this->withRevisions = true;

        return $this;
    }

    public function withBelongsToMany(array $items): self
    {
        $this->belongsToMany = $items;

        return $this;
    }

    public function withRepeaters(array $classes): self
    {
        $this->repeaters = $classes;

        return $this;
    }

    public function withRelated(array $related): self
    {
        $this->related = $related;

        return $this;
    }

    public function withBelongsTo(array $items): self
    {
        $this->belongsTo = $items;

        return $this;
    }

    public function withMedias(): self
    {
        $this->withMedia = true;

        return $this;
    }

    public function withTableColumns(TableColumns $tableColumns): self
    {
        $this->tableColumns = $tableColumns;

        return $this;
    }

    public function withAdditionalTableColumns(TableColumns $tableColumns): self
    {
        $this->additionalTableColumns = $tableColumns;

        return $this;
    }

    public function withFormFields(Form $formFields): self
    {
        $this->formFields = $formFields;

        return $this;
    }

    public function withSetupMethods(array $setupMethods): self
    {
        $this->setupMethods = $setupMethods;

        return $this;
    }

    public function withAdditionalProp(string $prop, mixed $value): self
    {
        $this->additionalProps[$prop] = $value;

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

    public function withSlugAttributes(array $fields): self
    {
        $this->slugAttributes = $fields;

        return $this;
    }

    /**
     * Boots the anonymous module and returns the model class.
     */
    public function boot(): self
    {
        // Shared data.
        $modelName = Str::singular(Str::studly($this->namePlural));

        /*
         * The translation model.
         */
        $this->modelTranslationClass = '\App\Models\Translations\\' . $modelName . 'Translation';

        if (!class_exists($this->modelTranslationClass)) {
            eval($this->classPrinter->printNamespace($this->getTranslationModelClass($this->modelTranslationClass)));
        }

        /*
         * The regular model.
         */
        $this->modelClass = '\App\Models\\' . $modelName;

        if (!class_exists($this->modelClass)) {
            eval($this->classPrinter->printNamespace($this->getModelClass($this->modelClass)));
        }

        /*
         * The revision model.
         */
        $this->revisionClass = '\App\Models\Revisions\\' . $modelName . 'Revision';

        if (!class_exists($this->revisionClass)) {
            eval($this->classPrinter->printNamespace($this->getRevisionClass($this->revisionClass)));
        }

        /*
         * The slug class.
         */
        $this->slugClass = '\App\Models\Slugs\\' . $modelName . 'Slug';
        if (!class_exists($this->slugClass)) {
            eval($this->classPrinter->printNamespace($this->getSlugClass($this->slugClass)));
        }

        /*
         * The controller class.
         */
        $this->controllerClass = '\App\Http\Controllers\Twill\\' . $modelName . 'Controller';

        if (!class_exists($this->controllerClass)) {
            eval($this->classPrinter->printNamespace($this->getControllerClass($this->controllerClass)));
        }

        /*
         * The repository.
         */
        $this->repositoryClass = '\App\Repositories\\' . $modelName . 'Repository';

        if (!class_exists($this->repositoryClass)) {
            eval($this->classPrinter->printNamespace($this->getRepositoryClass($this->repositoryClass)));
        }

        /*
         * Other tasks.
         */
        // Migrate the database.
        $this->migrate();

        // Generate twill module routes.
        $this->buildAnonymousRoutes($this->namePlural, $this->controllerClass);

        /** @var \Illuminate\Routing\Router $router */
        $router = app()->make('router');
        $router->getRoutes()->refreshNameLookups();

        $this->app['config']['twill-navigation.' . $this->namePlural] = [
            'title' => Str::title($this->namePlural),
            'module' => true,
        ];

        return $this;
    }

    public function getModelClassName(): string
    {
        return $this->modelClass;
    }

    public function getRepositoryClassName(): string
    {
        return $this->repositoryClass;
    }

    /**
     * @return Model
     */
    public function getSlugModelClassName(): string
    {
        return $this->slugClass;
    }

    public function getRepository(): ModuleRepository
    {
        return app()->make($this->getRepositoryClassName());
    }

    public function getModelController(): ModuleController
    {
        return app()->make($this->controllerClass);
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

        TwillRoutes::addToRouteRegistry($slug, $customRoutePrefix);

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

    private function getTranslationModelClass(string $classNameWithNamespace): PhpNamespace
    {
        $namespace = Str::beforeLast($classNameWithNamespace, '\\');
        $className = Str::afterLast($classNameWithNamespace, '\\');

        $namespace = new PhpNamespace(ltrim($namespace, '\\'));

        $class = $namespace->addClass($className);

        $fillable = collect($this->fields)
            ->where('translatable', true)
            ->keys()
            ->all();
        $fillable[] = 'active';

        $class->addProperty(
            'fillable',
            $fillable
        );

        $class->addProperty('table', Str::singular($this->namePlural) . '_translations');

        $class->addMethod('isTranslationModel')
            ->setBody('return true;')
            ->setReturnType('bool');

        $class->setExtends(Model::class);

        return $namespace;
    }

    private function getSlugClass(string $classNameWithNamespace): PhpNamespace
    {
        $namespace = Str::beforeLast($classNameWithNamespace, '\\');
        $className = Str::afterLast($classNameWithNamespace, '\\');

        $namespace = new PhpNamespace(ltrim($namespace, '\\'));

        $class = $namespace->addClass($className);

        $class->addProperty('table', Str::singular($this->namePlural) . '_slugs');

        $class->setExtends(Model::class);

        return $namespace;
    }

    private function getRevisionClass(string $classNameWithNamespace): PhpNamespace
    {
        $namespace = Str::beforeLast($classNameWithNamespace, '\\');
        $className = Str::afterLast($classNameWithNamespace, '\\');

        $namespace = new PhpNamespace(ltrim($namespace, '\\'));

        $class = $namespace->addClass($className);

        $class->addProperty('table', Str::singular($this->namePlural) . '_revisions');

        $class->setExtends(Revision::class);

        return $namespace;
    }

    private function getModelClass(string $classNameWithNamespace): PhpNamespace
    {
        $namespace = Str::beforeLast($classNameWithNamespace, '\\');
        $className = Str::afterLast($classNameWithNamespace, '\\');

        $namespace = new PhpNamespace(ltrim($namespace, '\\'));

        $class = $namespace->addClass($className);

        $casts = [];
        foreach (collect($this->fields)->where('type', 'json')->keys() as $field) {
            $casts[$field] = 'array';
        }

        foreach (collect($this->fields)->where('type', 'dateTime')->keys() as $field) {
            $casts[$field] = 'datetime';
        }

        $fillable = array_keys($this->fields);

        foreach (array_keys($this->belongsTo) as $base) {
            $fillable[] = $base . '_id';
        }

        $class->addProperty('fillable', $fillable);
        $class->addProperty('casts', $casts);
        $class->addProperty('table', $this->namePlural);
        $class->addProperty('translationForeignKey', Str::singular($this->namePlural) . '_id');
        $class->addProperty('translationModel', $this->modelTranslationClass);

        $class->addProperty(
            'translatedAttributes',
            collect($this->fields)
                ->where('translatable', true)
                ->keys()
                ->all()
        );

        if ($this->slugAttributes !== []) {
            $class->addProperty(
                'slugAttributes',
                $this->slugAttributes
            );
        }

        foreach ($this->belongsToMany as $name => $target) {
            $method = $class->addMethod($name);
            $method->setBody(
                <<<PHP
return \$this->belongsToMany($target::class);
PHP
            );
        }

        foreach ($this->repeaters as $repeaterClassName) {
            $className = Str::afterLast($repeaterClassName, '\\');
            $namePlural = Str::lower(Str::plural($className));

            $class->addMethod($namePlural)
                ->setReturnType(HasMany::class)
                ->setBody('return $this->hasMany(' . $repeaterClassName . '::class);');
        }

        $class->setExtends(Model::class);
        $class->addTrait(HasSlug::class);
        $class->addTrait(HasBlocks::class);
        $class->addTrait(HasTranslation::class);

        if ($this->related !== []) {
            $class->addTrait(HasRelated::class);
        }

        if ($this->withMedia) {
            $class->addTrait(HasMedias::class);
        }

        if ($this->withRevisions) {
            $class->addTrait(HasRevisions::class);
        }

        return $namespace;
    }

    private function getRepositoryClass(string $classNameWithNamespace): PhpNamespace
    {
        $namespace = Str::beforeLast($classNameWithNamespace, '\\');
        $className = Str::afterLast($classNameWithNamespace, '\\');

        $modelClass = '\App\Models\\' . str_replace('Repository', '', $className);

        $namespace = new PhpNamespace(ltrim($namespace, '\\'));

        $class = $namespace->addClass($className);
        $class->setExtends(ModuleRepository::class);
        $class->addTrait(HandleSlugs::class);
        $class->addTrait(HandleTranslations::class);
        $class->addTrait(HandleBlocks::class);

        if (collect($this->fields)->where('type', 'json')->isNotEmpty()) {
            $class->addTrait(HandleJsonRepeaters::class);
            $class->addProperty('jsonRepeaters', collect($this->fields)->where('type', 'json')->keys()->toArray());
        }

        if ($this->withRevisions) {
            $class->addTrait(HandleRevisions::class);
        }

        if ($this->withMedia) {
            $class->addTrait(HandleMedias::class);
        }

        if ($this->related !== []) {
            $class->addProperty('relatedBrowsers')
                ->setProtected()
                ->setValue($this->related);
        }

        // Add the afterSave method.
        $method = $class->addMethod('afterSave');
        $method->setReturnType('void');
        $method->addParameter('model')
            ->setType(TwillModelContract::class);
        $method->addParameter('fields')
            ->setType('array');

        foreach (array_keys($this->belongsToMany) as $target) {
            $method->addBody('$this->updateBrowser($model, $fields, "' . $target . '");');
        }

        foreach ($this->repeaters as $repeaterClassName) {
            $base = '$this->updateRepeater($model, $fields, \'##PLURAL##\', \'##CLASSNAME##\', \'##PLURAL##\');';

            $className = Str::afterLast($repeaterClassName, '\\');
            $namePlural = Str::lower(Str::plural($className));

            $method->addBody(str_replace(['##PLURAL##', '##CLASSNAME##'], [$namePlural, $className], $base));
        }
        $method->addBody('parent::afterSave($model, $fields);');
        // End after save method.

        $constructor = $class->addMethod('__construct');
        $constructor->addParameter('model')->setType($modelClass);
        $constructor->setBody('$this->model = $model;');

        return $namespace;
    }

    private function getControllerClass(string $classNameWithNamespace): PhpNamespace
    {
        $namespace = Str::beforeLast($classNameWithNamespace, '\\');
        $className = Str::afterLast($classNameWithNamespace, '\\');

        $namespace = new PhpNamespace(ltrim($namespace, '\\'));

        $class = $namespace->addClass($className);
        $class->setExtends(ModuleController::class);

        $class->addProperty('moduleName', Str::plural(Str::lower(str_replace('Controller', '', $className))));
        $class->addProperty('setterProps', [
            'setSetupMethods' => serialize($this->setupMethods),
            'setFormFields' => serialize($this->formFields),
            'setTableColumns' => serialize($this->tableColumns),
            'setAdditionalIndexTableColumns' => serialize($this->additionalTableColumns),
        ]);

        foreach ($this->additionalProps as $key => $value) {
            $class->addProperty($key, $value);
        }

        $constructor = $class->addMethod('__construct')
            ->setBody('parent::__construct($app, $request);')
            ->addBody('if (! isset($this->user) && $request->user()) {')
            ->addBody('  $this->user = $request->user();')
            ->addBody('}')
            ->addBody('if (! isset($this->user)) {')
            ->addBody("  \$this->user = \Illuminate\Support\Facades\Auth::guard('twill_users')->user();")
            ->addBody('}');

        $constructor->addParameter('app')
            ->setType(FoundationApplication::class);

        $constructor->addParameter('request')
            ->setType(Request::class);

        $class->addMethod('setUpController')
            ->setReturnType('void')
            ->setBody("\$data = unserialize(\$this->setterProps['setSetupMethods']);")
            ->addBody('foreach ($data as $method) {')
            ->addBody('  $this->{$method}();')
            ->addBody('}');

        $getFormMethod = $class->addMethod('getForm')
            ->setReturnType(Form::class)
            ->setBody("\$data = unserialize(\$this->setterProps['setFormFields']);")
            ->addBody('if ($data !== null) {')
            ->addBody('  return $data;')
            ->addBody('}')
            ->addBody('return parent::getForm($model);');

        $getFormMethod
            ->addParameter('model')
            ->setType(TwillModelContract::class);

        $class->addMethod('getIndexTableColumns')
            ->setReturnType(TableColumns::class)
            ->setBody("\$data = unserialize(\$this->setterProps['setTableColumns']);")
            ->addBody('if ($data !== null) {')
            ->addBody('  return $data;')
            ->addBody('}')
            ->addBody('return parent::getIndexTableColumns();');

        $class->addMethod('additionalIndexTableColumns')
            ->setReturnType(TableColumns::class)
            ->setBody("\$data = unserialize(\$this->setterProps['setAdditionalIndexTableColumns']);")
            ->addBody('if ($data !== null) {')
            ->addBody('  return $data;')
            ->addBody('}')
            ->addBody('return parent::additionalIndexTableColumns();');

        $class->addMethod('getFormRequestClass')
            ->setBody("\$request = new class() extends \A17\Twill\Http\Requests\Admin\Request {};")
            ->addBody('return $request::class;');

        return $namespace;
    }

    private function migrate(): void
    {
        // Create the migration class.
        $migration = new class(
            $this->namePlural,
            $this->fields,
            $this->belongsToMany,
            $this->belongsTo
        ) extends Migration {
            public string $nameSingular;

            public function __construct(
                public string $namePlural,
                public array $fields,
                public array $belongsToMany,
                public array $belongsTo,
            ) {
                $this->nameSingular = Str::singular($this->namePlural);
            }

            public function up(): void
            {
                // Only create the schema if it does not exist yet.
                if (Schema::hasTable($this->namePlural)) {
                    return;
                }

                foreach ($this->belongsToMany as $target => $model) {
                    $targetSingular = Str::singular($target);
                    Schema::create(
                        $this->nameSingular . '_' . $targetSingular,
                        function (Blueprint $table) use ($targetSingular) {
                            $table->bigIncrements('id');
                            $table->timestamps();
                            $table->integer('position')->unsigned();
                            createDefaultRelationshipTableFields($table, $this->nameSingular, $targetSingular);
                        }
                    );
                }

                Schema::create($this->namePlural, function (Blueprint $table) {
                    createDefaultTableFields($table);

                    foreach ($this->belongsTo as $target) {
                        $table->foreignIdFor($target)->nullable();
                    }

                    foreach (collect($this->fields)->where('translatable', false) as $fieldName => $data) {
                        if (!isset($data['type']) || $data['type'] === 'string') {
                            $table->string($fieldName)
                                ->default($data['default'] ?? null)
                                ->nullable($data['nullable'] ?? true);
                        } elseif ($data['type'] === 'boolean') {
                            $table->boolean($fieldName)
                                ->default($data['default'] ?? false)
                                ->nullable($data['nullable'] ?? true);
                        } elseif ($data['type'] === 'json') {
                            $table->json($fieldName)
                                ->nullable();
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

        $migration->up();
    }
}
