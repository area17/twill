<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\Media;
use A17\Twill\Services\MediaLibrary\ImageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasMedias
{
    protected $cropParamsKeys = [
        'crop_x',
        'crop_y',
        'crop_w',
        'crop_h',
    ];

    public function getMediasParams(): array
    {
        return (isset($this->mediasParams) && is_array($this->mediasParams))
            ? $this->mediasParams
            : config('twill.default_crops');
    }

    public static function bootHasMedias(): void
    {
        self::deleted(static function (Model $model) {
            if (!method_exists($model, 'isForceDeleting') || $model->isForceDeleting()) {
                /* @var \A17\Twill\Models\Behaviors\HasMedias $model */
                $model->medias()->detach();
            }
        });
    }

    /**
     * Defines the many-to-many relationship for media objects.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function medias()
    {
        return $this->morphToMany(
            Media::class,
            'mediable',
            config('twill.mediables_table', 'twill_mediables')
        )->withPivot([
            'crop',
            'role',
            'crop_w',
            'crop_h',
            'crop_x',
            'crop_y',
            'lqip_data',
            'ratio',
            'metadatas',
            'locale',
        ])->withTimestamps()->orderBy(config('twill.mediables_table', 'twill_mediables') . '.id', 'asc');
    }

    private function findMedia($role, $crop = 'default')
    {
        $media = $this->medias->first(function ($media) use ($role, $crop) {
            if (config('twill.media_library.translated_form_fields', false)) {
                $localeScope = $media->pivot->locale === app()->getLocale();
            }

            return $media->pivot->role === $role && $media->pivot->crop === $crop && ($localeScope ?? true);
        });

        if (!$media && config('twill.media_library.translated_form_fields', false)) {
            $media = $this->medias->first(function ($media) use ($role, $crop) {
                return $media->pivot->role === $role && $media->pivot->crop === $crop;
            });
        }

        return $media;
    }

    /**
     * Checks if an image has been attached for a role and crop.
     *
     * @param string $role Role name.
     * @param string $crop Crop name.
     * @return bool
     */
    public function hasImage($role, $crop = 'default')
    {
        $media = $this->findMedia($role, $crop);

        return !empty($media);
    }

    /**
     * Returns the URL of the attached image for a role and crop.
     *
     * @param string $role Role name.
     * @param string $crop Crop name.
     * @param array $params Parameters compatible with the current image service, like `w` or `h`.
     * @param bool $has_fallback Indicate that you can provide a fallback. Will return `null` instead of the default image fallback.
     * @param bool $cms Indicate that you are displaying this image in the CMS views.
     * @param Media|null $media Provide a media object if you already retrieved one to prevent more SQL queries.
     * @return string|null
     */
    public function image(
        $role,
        $crop = 'default',
        $params = [],
        $has_fallback = false,
        $cms = false,
        Media|null|bool $media = null
    ) {
        if (!$media) {
            $media = $this->findMedia($role, $crop);
        }

        if ($media) {
            $crop_params = Arr::only($media->pivot->toArray(), $this->cropParamsKeys);

            if ($cms) {
                return ImageService::getCmsUrl($media->uuid, $crop_params + $params);
            }

            return ImageService::getUrlWithCrop($media->uuid, $crop_params, $params);
        }

        if ($has_fallback) {
            return null;
        }

        return ImageService::getTransparentFallbackUrl();
    }

    /**
     * Returns an array of URLs of all attached images for a role and crop.
     *
     * @param string $role Role name.
     * @param string $crop Crop name.
     * @param array $params Parameters compatible with the current image service, like `w` or `h`.
     * @return array
     */
    public function images($role, $crop = 'default', $params = [])
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

    /**
     * Returns an array of URLs of all attached images for a role, including all crops.
     *
     * @param string $role Role name.
     * @param array $params Parameters compatible with the current image service, like `w` or `h`.
     * @return array
     */
    public function imagesWithCrops($role, $params = [])
    {
        $medias = $this->medias->filter(function ($media) use ($role) {
            return $media->pivot->role === $role;
        });

        $urls = [];

        foreach ($medias as $media) {
            $paramsForCrop = $params[$media->pivot->crop] ?? [];
            $urls[$media->id][$media->pivot->crop] = $this->image(
                $role,
                $media->pivot->crop,
                $paramsForCrop,
                false,
                false,
                $media
            );
        }

        return $urls;
    }

    /**
     * Returns an array of meta information for the image attached for a role and crop.
     *
     * @param string $role Role name.
     * @param string $crop Crop name.
     * @param array $params Parameters compatible with the current image service, like `w` or `h`.
     * @param Media|null $media Provide a media object if you already retrieved one to prevent more SQL queries.
     * @return array
     */
    public function imageAsArray($role, $crop = 'default', $params = [], $media = null)
    {
        if ($media === null) {
            $media = $this->findMedia($role, $crop);
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

    /**
     * Returns an array of meta information for all images attached for a role and crop.
     *
     * @param string $role Role name.
     * @param string $crop Crop name.
     * @param array $params Parameters compatible with the current image service, like `w` or `h`.
     * @return array
     */
    public function imagesAsArrays($role, $crop = 'default', $params = [])
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

    /**
     * Returns an array of meta information for all images attached for a role, including all crops.
     *
     * @param string $role Role name.
     * @param array $params Parameters compatible with the current image service, like `w` or `h`.
     * @return array
     */
    public function imagesAsArraysWithCrops($role, $params = [])
    {
        $medias = $this->medias->filter(function ($media) use ($role) {
            return $media->pivot->role === $role;
        });

        $arrays = [];

        foreach ($medias as $media) {
            $paramsForCrop = $params[$media->pivot->crop] ?? [];
            $arrays[$media->id][$media->pivot->crop] = $this->imageAsArray(
                $role,
                $media->pivot->crop,
                $paramsForCrop,
                $media
            );
        }

        return $arrays;
    }

    /**
     * Returns the alt text of the image attached for a role.
     *
     * @param string $role Role name.
     * @param Media|null $media Provide a media object if you already retrieved one to prevent more SQL queries.
     * @return string
     */
    public function imageAltText($role, $media = null)
    {
        if ($media === null) {
            $media = $this->medias->first(function ($media) use ($role) {
                if (config('twill.media_library.translated_form_fields', false)) {
                    $localeScope = $media->pivot->locale === app()->getLocale();
                }

                return $media->pivot->role === $role && ($localeScope ?? true);
            });
        }

        if ($media) {
            return $media->getMetadata('altText', 'alt_text');
        }

        return '';
    }

    /**
     * Returns the caption of the image attached for a role.
     *
     * @param string $role Role name.
     * @param Media|null $media Provide a media object if you already retrieved one to prevent more SQL queries.
     * @return string
     */
    public function imageCaption($role, $media = null)
    {
        if ($media === null) {
            $media = $this->medias->first(function ($media) use ($role) {
                if (config('twill.media_library.translated_form_fields', false)) {
                    $localeScope = $media->pivot->locale === app()->getLocale();
                }

                return $media->pivot->role === $role && ($localeScope ?? true);
            });
        }

        if ($media) {
            return $media->getMetadata('caption');
        }

        return '';
    }

    /**
     * Returns the video URL of the image attached for a role.
     *
     * @param string $role Role name.
     * @param Media|null $media Provide a media object if you already retrieved one to prevent more SQL queries.
     * @return string
     */
    public function imageVideo($role, $media = null)
    {
        if ($media === null) {
            $media = $this->medias->first(function ($media) use ($role) {
                if (config('twill.media_library.translated_form_fields', false)) {
                    $localeScope = $media->pivot->locale === app()->getLocale();
                }

                return $media->pivot->role === $role && ($localeScope ?? true);
            });
        }

        if ($media) {
            $metadatas = (object)json_decode($media->pivot->metadatas);
            $language = app()->getLocale();

            return $metadatas->video->$language ?? (is_object($metadatas->video) ? '' : ($metadatas->video ?? ''));
        }

        return '';
    }

    /**
     * Returns the media object attached for a role and crop.
     *
     * @param string $role Role name.
     * @param string $crop Crop name.
     * @return Media|null
     */
    public function imageObject($role, $crop = 'default')
    {
        return $this->findMedia($role, $crop);
    }

    /**
     * Returns the LQIP base64 encoded string for a role.
     * Use this in conjunction with the `RefreshLQIP` Artisan command.
     *
     * @param string $role Role name.
     * @param string $crop Crop name.
     * @param array $params Parameters compatible with the current image service, like `w` or `h`.
     * @param bool $has_fallback Indicate that you can provide a fallback. Will return `null` instead of the default image fallback.
     * @return string|null
     * @see \A17\Twill\Commands\RefreshLQIP
     */
    public function lowQualityImagePlaceholder($role, $crop = 'default', $params = [], $has_fallback = false)
    {
        $media = $this->findMedia($role, $crop);

        if ($media) {
            return $media->pivot->lqip_data ?? ImageService::getTransparentFallbackUrl();
        }

        if ($has_fallback) {
            return null;
        }

        return ImageService::getTransparentFallbackUrl();
    }

    /**
     * Returns the URL of the social image for a role and crop.
     *
     * @param string $role Role name.
     * @param string $crop Crop name.
     * @param array $params Parameters compatible with the current image service, like `w` or `h`.
     * @param bool $has_fallback Indicate that you can provide a fallback. Will return `null` instead of the default image fallback.
     * @return string|null
     */
    public function socialImage($role, $crop = 'default', $params = [], $has_fallback = false)
    {
        $media = $this->findMedia($role, $crop);

        if ($media) {
            $crop_params = Arr::only($media->pivot->toArray(), $this->cropParamsKeys);

            return ImageService::getSocialUrl($media->uuid, $crop_params + $params);
        }

        if ($has_fallback) {
            return null;
        }

        return ImageService::getSocialFallbackUrl();
    }

    /**
     * Returns the URL of the CMS image for a role and crop.
     *
     * @param string $role Role name.
     * @param string $crop Crop name.
     * @param array $params Parameters compatible with the current image service, like `w` or `h`.
     * @return string
     */
    public function cmsImage($role, $crop = 'default', $params = [])
    {
        return $this->image($role, $crop, $params, false, true, false) ?? ImageService::getTransparentFallbackUrl();
    }

    /**
     * Returns the URL of the default CMS image for this model.
     *
     * @param array $params Parameters compatible with the current image service, like `w` or `h`.
     * @return string
     */
    public function defaultCmsImage($params = [])
    {
        $media = $this->medias->first();

        if ($media) {
            return $this->image(null, null, $params, true, true, $media) ?? ImageService::getTransparentFallbackUrl();
        }

        return ImageService::getTransparentFallbackUrl();
    }

    /**
     * Returns the media objects associated with a role and crop.
     *
     * @param string $role Role name.
     * @param string $crop Crop name.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function imageObjects($role, $crop = 'default')
    {
        return $this->medias->filter(function ($media) use ($role, $crop) {
            return $media->pivot->role === $role && $media->pivot->crop === $crop;
        });
    }
}
