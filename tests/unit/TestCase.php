<?php

namespace A17\Twill\Tests\Unit;

use A17\Twill\TwillServiceProvider;
use A17\Twill\RouteServiceProvider;
use A17\Twill\ValidationServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Get application package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            RouteServiceProvider::class,
            TwillServiceProvider::class,
            ValidationServiceProvider::class,
        ];
    }
}
