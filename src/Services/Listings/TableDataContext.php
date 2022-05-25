<?php

namespace A17\Twill\Services\Listings;

class TableDataContext
{
    public function __construct(
        public string $titleColumnKey,
        public string $identifierColumn,
        public string $moduleName,
        public string $routePrefix,
        public string $endpointType,
        public bool $hasMedia,
        public array $repeaterFields,
    ) {
    }
}
