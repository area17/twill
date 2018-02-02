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

    public function images($role, $crop = "default", $params = [])
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

    public function imageAsArray($role, $crop = "default", $params = [], $media = null)
    {
        if (!$media) {
            $media = $this->medias->first(function ($media) use ($role, $crop) {
                return $media->pivot->role === $role && $media->pivot->crop === $crop;
            });
        }

        if ($media) {
            return [
                'src' => $this->image($role, $crop, $params, false, false, $media),
                'width' => $media->pivot->crop_w ?? $media->width,
                'height' => $media->pivot->crop_h ?? $media->height,
                'alt' => $this->imageAltText($role, $media),
                'caption' => $this->imageCaption($role, $media),
                'video' => $this->imageVideo($role, $media),
            ];
        }

        return [];
    }

    public function imagesAsArrays($role, $crop = "default", $params = [])
    {
        $medias = $this->medias->filter(function ($media) use ($role, $crop) {
            return $media->pivot->role === $role && $media->pivot->crop === $crop;
        });

        $arrays = [];

        foreach ($medias as $media) {
            $arrays[] = $this->imageAsArray($role, $crop, $params, $media);
        }

        return $arrays;
    }

    public function imageAltText($role, $media = null)
    {
        if (!$media) {
            $media = $this->medias->first(function ($media) use ($role) {
                return $media->pivot->role === $role;
            });
        }

        if ($media) {
            $metadatas = json_decode($media->pivot->metadatas);
            $language = app()->getLocale();
            return $metadatas->altText->$language ?? $metadatas->altText ?? $media->alt_text;
        }

        return '';
    }

    public function imageCaption($role, $media = null)
    {
        if (!$media) {
            $media = $this->medias->first(function ($media) use ($role) {
                return $media->pivot->role === $role;
            });
        }

        if ($media) {
            $metadatas = json_decode($media->pivot->metadatas);
            $language = app()->getLocale();
            return $metadatas->caption->$language ?? $metadatas->caption ?? $media->caption;
        }

        return '';
    }

    public function imageVideo($role, $media = null)
    {
        if (!$media) {
            $media = $this->medias->first(function ($media) use ($role) {
                return $media->pivot->role === $role;
            });
        }

        if ($media) {
            $metadatas = json_decode($media->pivot->metadatas);
            $language = app()->getLocale();
            return $metadatas->video->$language ?? $metadatas->video ?? '';
        }

        return '';
    }

    public function imageObject($role, $crop = "default")
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

    public function imageObjects($role, $crop = "default")
    {
        return $this->medias->filter(function ($media) use ($role, $crop) {
            return $media->pivot->role === $role && $media->pivot->crop === $crop;
        });
    }
}
