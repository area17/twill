<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Exceptions\ColumnMissingPropertyException;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Listings\TableColumn;

class Browser extends TableColumn
{
    private ?string $browser = null;

    public function browser(string $browser): static
    {
        $this->browser = $browser;
        return $this;
    }

    protected function getRenderValue(TwillModelContract $model): string
    {
        if (null === $this->browser) {
            throw new ColumnMissingPropertyException('Browser column missing browser value: ' . $this->field);
        }

        /** @var \A17\Twill\Models\Behaviors\HasRelated $model */
        return $model->getRelated($this->browser)
            ->pluck($this->field)
            ->join(', ');
    }
}
