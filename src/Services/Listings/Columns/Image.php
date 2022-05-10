<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Model as TwillModel;
use A17\Twill\Services\Listings\TableColumn;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class Image extends TableColumn
{
    protected ?string $role = null;
    protected ?string $crop = null;
    protected ?array $mediaParams = null;
    protected bool $rounded = false;

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

    public function getRenderValue(Model $model): string
    {
        if (!classHasTrait($model::class, HasMedias::class)) {
            throw new InvalidArgumentException('Cannot use image column on model not implementing HasMedias trait');
        }

        if ($renderFunction = $this->render) {
            return $renderFunction($model);
        }

        return $this->getThumbnail($model);
    }

    public function getThumbnail(TwillModel $model): ?string
    {
        $role = $this->role ?? head(array_keys($model->getMediasParams()));
        $crop = $this->crop ?? head(array_keys($model->getMediasParams()[$role]));
        $params = $this->mediaParams ?? ['w' => 80, 'h' => 80, 'fit' => 'crop'];

        return $model->cmsImage($role, $crop, $params);
    }
}
