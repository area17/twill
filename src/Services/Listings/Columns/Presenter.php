<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Listings\TableColumn;
use Illuminate\Support\Str;

class Presenter extends TableColumn
{
    protected function getRenderValue(TwillModelContract $model): string
    {
        $presenter = $model->presentAdmin();
        if (method_exists($presenter, $this->field)) {
            return $presenter->{$this->field}();
        }

        if (method_exists($presenter, Str::camel($this->field))) {
            return $presenter->{Str::camel($this->field)}();
        }

        // Fallback to the property.
        return $model->{$this->field};
    }
}
