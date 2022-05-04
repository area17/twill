<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Model;
use A17\Twill\Services\Listings\TableColumn;

class ImageColumn extends TableColumn
{
    protected ?string $presenter = null;
    protected ?string $role = null;
    protected ?string $crop = null;
    protected ?array $mediaParams = null;
    protected bool $rounded = false;

    public function setPresenter(string $presenter): self
    {
        $this->presenter = $presenter;
        return $this;
    }

    public function role(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function crop(string $crop): self
    {
        $this->crop = $crop;
        return $this;
    }

    public function mediaParams(array $params): self
    {
        $this->mediaParams = $params;
        return $this;
    }

    public function rounded(bool $rounded = true): self
    {
        $this->rounded = $rounded;
        return $this;
    }

    public function toColumnArray(array $visibleColumns = [], bool $sortable = true): array
    {
        $data = parent::toColumnArray($visibleColumns, $sortable);
        $data['variation'] = $this->rounded ? 'rounded' : 'square';

        return $data;
    }

    public function getThumbnail(Model $model): ?string
    {
        if ($this->presenter) {
            return $model->presentAdmin()->{$this->presenter};
        }

        $role = $this->role ?? head(array_keys($model->getMediasParams()));
        $crop = $this->crop ?? head(array_keys($model->getMediasParams()[$role]));
        $params = $this->mediaParams ?? ['w' => 80, 'h' => 80, 'fit' => 'crop'];

        return $model->cmsImage($role, $crop, $params);
    }

    public function getRenderValue(Model $model): string
    {
        if ($renderFunction = $this->render) {
            return $renderFunction($model);
        }

        return $this->getThumbnail($model);
    }
}
