<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Services\Listings\TableColumn;

class ImageColumn extends TableColumn
{
    public function __construct(
        string $key,
        ?string $title = null,
        public ?string $thumb = null,
        public ?string $thumbPresent = null,
        public ?string $thumbPresenter = null,
        public ?array $variant = null
    ) {
        parent::__construct(key: $key, title: $title);
    }

    public function getThumbnail(Model $model): ?string
    {
        if ($this->thumb && $this->thumbPresent && $this->thumbPresenter) {
            return $model->presentAdmin()->{$this->thumbPresenter};
        }

        $role = $this->variant ? $this->variant['role'] : head(array_keys($model->getMediasParams()));
        $crop = $this->variant ? $this->variant['crop'] : head(array_keys(head($model->getMediasParams())));
        $params = $this->variant && isset($this->variant['params'])
            ? $this->variant['params']
            : ['w' => 80, 'h' => 80, 'fit' => 'crop'];

        return $model->cmsImage($role, $crop, $params);
    }
}
