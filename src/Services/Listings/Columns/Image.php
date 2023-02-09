<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Behaviors\HasMedias;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Listings\TableColumn;
use InvalidArgumentException;

class Image extends TableColumn
{
    protected ?string $role = null;

    protected ?string $crop = null;

    protected ?array $mediaParams = null;

    protected bool $rounded = false;

    public static function make(): static
    {
        $column = parent::make();
        $column->specificType = 'thumbnail';
        $column->shrink();

        return $column;
    }

    /**
     * The image role that is defined in your model. Can be left out as it will take the first one available.
     */
    public function role(?string $role): static
    {
        $this->role = $role;
        return $this;
    }

    /**
     * A specific crop to use for the image. Can be left out as it will take the first one available.
     */
    public function crop(?string $crop): static
    {
        $this->crop = $crop;
        return $this;
    }

    /**
     * Optional array of media parameters for more control over the rendering.
     */
    public function mediaParams(array $params): static
    {
        $this->mediaParams = $params;
        return $this;
    }

    /**
     * If enabled the image will be rounded instead of square.
     */
    public function rounded(bool $rounded = true): static
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

    protected function getRenderValue(TwillModelContract $model): string
    {
        if (!classHasTrait($model::class, HasMedias::class)) {
            throw new InvalidArgumentException('Cannot use image column on model not implementing HasMedias trait');
        }

        if ($renderFunction = $this->render) {
            return $renderFunction($model);
        }

        return $this->getThumbnail($model);
    }

    public function getThumbnail(TwillModelContract $model): ?string
    {
        $role = $this->role ?? head(array_keys($model->getMediasParams()));
        $crop = $this->crop ?? head(array_keys($model->getMediasParams()[$role]));
        $params = $this->mediaParams ?? ['w' => 80, 'h' => 80, 'fit' => 'crop'];

        return $model->cmsImage($role, $crop, $params);
    }
}
