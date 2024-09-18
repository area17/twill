<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\Media;

trait HasAssets
{
    public function assets($role, $crop = 'default', $locale = null, $params = [])
    {
        $locale = $locale ?? app()->getLocale();

        $medias = $this->medias->filter(function ($media) use ($role, $crop, $locale) {
            return $media->pivot->role === $role && $media->pivot->crop === $crop && $media->pivot->locale === $locale;
        });

        $files = $this->files->filter(function ($file) use ($role, $locale) {
            return $file->pivot->role === $role && $file->pivot->locale === $locale;
        });

        $assets = $medias->merge($files);

        $urls = $assets->map(function ($asset) use ($role, $crop, $params, $locale) {
            $type = $asset instanceof Media ? 'image' : 'file';
            if ($type === 'image') {
                $url = $this->image($role, $crop, $params, false, false, $asset);
            } else {
                $url = $this->file($role, $locale, $asset);
            }

            return [
                'type' => $type,
                'url' => $url,
                'position' => $asset->pivot->position,
            ];
        });

        return $urls->sortBy('position')->values()->toArray();
    }
}
