<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables\Filters;

use A17\Twill\Services\Listings\Filters\QuickFilters;
use A17\Twill\Services\Listings\Filters\TableFilters;
use A17\Twill\Tests\Integration\ModulesTestBase;
use App\Http\Controllers\Twill\AuthorController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilterTestBase extends ModulesTestBase
{
    /**
     * QuickFilters and extendQuickFilters cannot be used together.
     */
    public function controllerWithFiltersAndQuickFilters(
        array $filters = [],
        array $quickFilters = [],
        array $extendQuickFilters = [],
        array $active = []
    ): AuthorController {
        $request = Request::create(route('twill.personnel.authors.index'), 'GET', ['filter' => json_encode($active)]);

        return new class($this->app, $request, $filters, $quickFilters, $extendQuickFilters) extends AuthorController {
            public function __construct(
                Application $app,
                Request $request,
                public array $testFilters,
                public array $testQuickFilters,
                public array $testExtendQuickFilters
            ) {
                parent::__construct($app, $request);

                $this->user = Auth::user();
            }

            public function setSearchColumnsTest(...$args): void
            {
                $this->setSearchColumns(...$args);
            }

            public function quickFilters(): QuickFilters
            {
                if ($this->testQuickFilters !== []) {
                    return QuickFilters::make($this->testQuickFilters);
                }
                $quickFilters = parent::quickFilters();

                if ($this->testExtendQuickFilters !== []) {
                    $quickFilters = $quickFilters->concat($this->testExtendQuickFilters);
                }

                return $quickFilters;
            }

            public function filters(): TableFilters
            {
                return TableFilters::make($this->testFilters);
            }
        };
    }
}
