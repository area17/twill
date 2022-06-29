<?php

namespace A17\Twill\Tests\Integration\Controllers;

use A17\Twill\Tests\Integration\ModulesTestBase;
use App\Http\Controllers\Twill\AuthorController;
use App\Http\Controllers\Twill\CategoryController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasicControllerSettersTest extends ModulesTestBase
{
    /**
     * @dataProvider simplePropertyOptions
     */
    public function testIndexDataProperties(
        string $indexOption,
        string $method,
        bool $default
    ): void {
        $controller = $this->getCategoryController();
        $this->assertEquals($default, $controller->getIndexOptionTest($indexOption));

        $controller = $this->getCategoryController([$method => null]);
        $this->assertEquals(!$default, $controller->getIndexOptionTest($indexOption));
    }

    public function simplePropertyOptions(): array
    {
        return [
            'create' => [
                'indexOption' => 'create',
                'method' => 'disableCreate',
                'default' => true,
            ],
            'sortable' => [
                'indexOption' => 'sortable',
                'method' => 'disableSortable',
                'default' => true,
            ],
            'edit' => [
                'indexOption' => 'edit',
                'method' => 'disableEdit',
                'default' => true,
            ],
            'skipCreateModal' => [
                'indexOption' => 'skipCreateModal',
                'method' => 'enableSkipCreateModal',
                'default' => false,
            ],
            'publish' => [
                'indexOption' => 'publish',
                'method' => 'disablePublish',
                'default' => true,
            ],
            'bulkPublish' => [
                'indexOption' => 'bulkPublish',
                'method' => 'disableBulkPublish',
                'default' => true,
            ],
            'feature' => [
                'indexOption' => 'feature',
                'method' => 'enableFeature',
                'default' => false,
            ],
            'bulkFeature' => [
                'indexOption' => 'bulkFeature',
                'method' => 'enableBulkFeature',
                'default' => false,
            ],
            'restore' => [
                'indexOption' => 'restore',
                'method' => 'disableRestore',
                'default' => true,
            ],
            'bulkRestore' => [
                'indexOption' => 'bulkRestore',
                'method' => 'disableBulkRestore',
                'default' => true,
            ],
            'forceDelete' => [
                'indexOption' => 'forceDelete',
                'method' => 'disableForceDelete',
                'default' => true,
            ],
            'bulkForceDelete' => [
                'indexOption' => 'bulkForceDelete',
                'method' => 'disableBulkForceDelete',
                'default' => true,
            ],
            'delete' => [
                'indexOption' => 'delete',
                'method' => 'disableDelete',
                'default' => true,
            ],
            'duplicate' => [
                'indexOption' => 'duplicate',
                'method' => 'enableDuplicate',
                'default' => false,
            ],
            'bulkDelete' => [
                'indexOption' => 'bulkDelete',
                'method' => 'disableBulkDelete',
                'default' => true,
            ],
            'reorder' => [
                'indexOption' => 'reorder',
                'method' => 'enableReorder',
                'default' => false,
            ],
            'permalink' => [
                'indexOption' => 'permalink',
                'method' => 'disablePermalink',
                'default' => true,
            ],
            'bulkEdit' => [
                'indexOption' => 'bulkEdit',
                'method' => 'disableBulkEdit',
                'default' => true,
            ],
            'editInModal' => [
                'indexOption' => 'editInModal',
                'method' => 'enableEditInModal',
                'default' => false,
            ],
            'showImage' => [
                'indexOption' => 'showImage',
                'method' => 'enableShowImage',
                'default' => false,
            ],
            'includeScheduledInList' => [
                'indexOption' => 'includeScheduledInList',
                'method' => 'disableIncludeScheduledInList',
                'default' => true,
            ],
        ];
    }

    public function getCategoryController(
        array $configSettersMethods = [],
        ?Request $request = null
    ): CategoryController {
        $request = $request ?? Request::create(route('twill.personnel.authors.index'));

        return new class($this->app, $request, $configSettersMethods) extends CategoryController {

            // Reset them so we use default only for this test class.
            protected $indexOptions = [];

            public function __construct(
                Application $app,
                Request $request,
                public array $configSettersMethods = []
            ) {
                parent::__construct($app, $request);

                $this->user = Auth::user();
            }

            public function getIndexOptionTest(string $option): mixed
            {
                return $this->getIndexOption($option);
            }

            protected function setUpController(): void
            {
                foreach ($this->configSettersMethods as $method => $values) {
                    if (is_array($values)) {
                        $this->{$method}(...$values);
                    } elseif ($values) {
                        $this->{$method}($values);
                    } else {
                        $this->{$method}();
                    }
                }
            }
        };
    }

}
