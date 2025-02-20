<?php

namespace App;

use A17\Twill\Models\Model;

class TestPresenter
{
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function createdAt(): string
    {
        return 'PresenterValueFromTestPresenter';
    }
}
