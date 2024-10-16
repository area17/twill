<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\Media;
use Illuminate\Database\Eloquent\Collection;

trait HasAssets
{
    public function assetsObjs($role, $crop = 'default')
    {
        $assets = $this->getAssets($role, $crop);
        return $assets->sortBy(fn($asset) => $asset->pivot->position)->values();
    }

    public function assets($role, $crop = 'default', $params = []): array
    {
        $assets = $this->getAssets($role, $crop);

        $urls = $assets->map(function ($asset) use ($role, $crop, $params) {
            $type = $asset instanceof Media ? 'image' : 'file';
            if ($type === 'image') {
                $url = $this->image($role, $crop, $params, false, false, $asset);
            } else {
                $url = $this->file($role, null, $asset);
            }

            return [
                'type' => $type,
                'url' => $url,
                'position' => $asset->pivot->position,
            ];
        });

        return $urls->sortBy('position')->values()->toArray();
    }

    /**
     * @param $role
     * @param mixed $crop
     * @return Collection|mixed
     */
    private function getAssets($role, mixed $crop): mixed
    {
        $medias = $this->medias->filter(function ($media) use ($role, $crop) {
            if (config('twill.media_library.translated_asset_fields', false)) {
                $localeScope = $media->pivot->locale === app()->getLocale();
            }

            return $media->pivot->role === $role && $media->pivot->crop === $crop && ($localeScope ?? true);
        });

        $files = $this->files->filter(function ($file) use ($role) {
            if (config('twill.media_library.translated_asset_fields', false)) {
                $localeScope = $file->pivot->locale === app()->getLocale();
            }

            return $file->pivot->role === $role && ($localeScope ?? true);
        });

        return $medias->merge($files);
    }
}
