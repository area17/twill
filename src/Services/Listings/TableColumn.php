<?php

namespace A17\Twill\Services\Listings;

use Illuminate\Support\Str;

abstract class TableColumn
{
    public function __construct(
        public string $key,
        public ?string $field = null,
        public ?string $title = null,
        public bool $sortable = false,
        public bool $defaultSort = false,
    ) {
        if (!$this->title) {
            $this->title = Str::headline($field ?? $key);
        }
    }

}
