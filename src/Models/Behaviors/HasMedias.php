<?php

namespace A17\CmsToolkit\Models\Behaviors;

use A17\CmsToolkit\Models\Media;
use ImageService;

trait HasMedias
{

    public function medias()
    {
        return $this->morphToMany(Media::class, 'mediable')->withPivot([
            'crop',
            'role',
            'crop_w',
            'crop_h',
            'crop_x',
            'crop_y',
            'lqip_data',
            'ratio',
            'metadatas',
        ])->withTimestamps()->orderBy('mediables.id', 'asc');
    }

    public function image($role, $crop = "default", $params = [], $has_fallback = false, $cms = false, $media = null)
    {

        if (!$media) {
            $media = $this->medias->first(function ($media) use ($role, $crop) {
                return $media->pivot->role === $role && $media->pivot->crop === $crop;
            });
        }

        if ($media) {
            $crop_params = ['rect' => $media->pivot->crop_x . ',' . $media->pivot->crop_y . ',' . $media->pivot->crop_w . ',' . $media->pivot->crop_h] + $params;

            if ($cms) {
                return ImageService::getCmsUrl($media->uuid, $crop_params);
            }

            return ImageService::getUrl($media->uuid, $crop_params);
        }

        if ($has_fallback) {
            return null;
        }

        return ImageService::getTransparentFallbackUrl();
    }

    public function images($role, $crop, $params = [])
    {
        $medias = $this->medias->filter(function ($media) use ($role, $crop) {
            return $media->pivot->role === $role && $media->pivot->crop === $crop;
        });

        $urls = [];

        foreach ($medias as $media) {
            $urls[] = $this->image($role, $crop, $params, false, false, $media);
        }

        return $urls;
    }

    public function imageAltText($role)
    {
        $media = $this->medias->first(function ($media) use ($role) {
            return $media->pivot->role === $role;
        });

        return $media->alt_text ?? null;
    }

    public function imageCaption($role)
    {
        $media = $this->medias->first(function ($media) use ($role) {
            return $media->pivot->role === $role;
        });

        return $media->caption ?? null;
    }

    public function imageObject($role, $crop)
    {
        return $this->medias->first(function ($media) use ($role, $crop) {
            return $media->pivot->role === $role && $media->pivot->crop === $crop;
        });
    }

    public function lowQualityImagePlaceholder($role, $crop = "default", $params = [], $has_fallback = false)
    {
        $media = $this->medias->first(function ($media) use ($role, $crop) {
            return $media->pivot->role === $role && $media->pivot->crop === $crop;
        });

        if ($media) {
            return $media->pivot->lqip_data ?? ImageService::getTransparentFallbackUrl();
        }

        if ($has_fallback) {
            return null;
        }

        return ImageService::getTransparentFallbackUrl();

    }

    public function socialImage($role, $crop = "default", $params = [], $has_fallback = false)
    {
        $media = $this->medias->first(function ($media) use ($role, $crop) {
            return $media->pivot->role === $role && $media->pivot->crop === $crop;
        });

        if ($media) {
            $crop_params = ['rect' => $media->pivot->crop_x . ',' . $media->pivot->crop_y . ',' . $media->pivot->crop_w . ',' . $media->pivot->crop_h] + $params;

            return ImageService::getSocialUrl($media->uuid, $crop_params);
        }

        if ($has_fallback) {
            return null;
        }

        return ImageService::getSocialFallbackUrl();
    }

    public function cmsImage($role, $crop = "default", $params = [])
    {
        return $this->image($role, $crop, $params, false, true, false) ?? ImageService::getTransparentFallbackUrl($params);
    }

    public function imageObjects($role, $crop)
    {
        return $this->medias->filter(function ($media) use ($role, $crop) {
            return $media->pivot->role === $role && $media->pivot->crop === $crop;
        });
    }
}
