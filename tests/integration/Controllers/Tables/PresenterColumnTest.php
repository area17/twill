<?php

namespace A17\Twill\Tests\Integration\Controllers\Tables;

use A17\Twill\Services\Listings\Columns\Presenter;
use A17\Twill\Tests\Integration\ModulesTestBase;
use Illuminate\Support\Facades\Artisan;

class PresenterColumnTest extends ModulesTestBase
{
    public function setUp(): void
    {
        parent::setUp();
        ray(1);
        ray($this->getAllUris());
        ray(
            file_get_contents(
                '/Users/rob/Sites/twill-dev/twill/vendor/orchestra/testbench-core/laravel/routes/twill.php'
            )
        );

        $this->author = $this->createAuthor();
    }

    public function testColumn(): void
    {
        ray(2);
        $column = Presenter::make()->field('createdAt');

        $this->assertEquals('PresenterValueFromTestPresenter', $column->renderCell($this->author));
    }

    public function testColumnCanCastCasing(): void {
        $column = Presenter::make()->field('created_at');

        $this->assertEquals('PresenterValueFromTestPresenter', $column->renderCell($this->author));
    }

    public function testColumnCanFallback(): void {
        $column = Presenter::make()->field('name');

        $this->assertEquals($this->author->name, $column->renderCell($this->author));
    }
}
