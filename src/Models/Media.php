<?php

namespace A17\Twill\Models;

use A17\Twill\Services\MediaLibrary\ImageService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Media extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'filename',
        'alt_text',
        'caption',
        'width',
        'height',
    ];

    public function __construct(array $attributes = [])
    {
        $this->fillable(array_merge($this->fillable, Collection::make(config('twill.media_library.extra_metadatas_fields'))->map(function ($field) {
            return $field['name'];
        })->toArray()));

        Collection::make(config('twill.media_library.translatable_metadatas_fields'))->each(function ($field) {
            $this->casts[$field] = 'json';
        });

        parent::__construct($attributes);
    }

    public function scopeUnused($query)
    {
        $usedIds = DB::table(config('twill.mediables_table'))->pluck('media_id');

        return $query->whereNotIn('id', $usedIds->toArray())->get();
    }

    public function getDimensionsAttribute()
    {
        return $this->width . 'x' . $this->height;
    }

    public function altTextFrom($filename)
    {
        $filename = pathinfo($filename, PATHINFO_FILENAME);
        if (Str::endsWith($filename, '@2x')) {
            $filename = substr($filename, 0, -2);
        }

        return ucwords(preg_replace('/[^a-zA-Z0-9]/', ' ', sanitizeFilename($filename)));
    }

    public function canDeleteSafely()
    {
        return DB::table(config('twill.mediables_table', 'twill_mediables'))->where('media_id', $this->id)->count() === 0;
    }

    public function isReferenced()
    {
        return DB::table(config('twill.mediables_table', 'twill_mediables'))->where('media_id', $this->id)->count() > 0;
    }

    public function toCmsArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->filename,
            'thumbnail' => ImageService::getCmsUrl($this->uuid, ['h' => '256']),
            'original' => ImageService::getRawUrl($this->uuid),
            'medium' => ImageService::getUrl($this->uuid, ['h' => '430']),
            'width' => $this->width,
            'height' => $this->height,
            'tags' => $this->tags->map(function ($tag) {
                return $tag->name;
            }),
            'deleteUrl' => $this->canDeleteSafely() ? moduleRoute('medias', 'media-library', 'destroy', $this->id) : null,
            'updateUrl' => route('twill.media-library.medias.single-update'),
            'updateBulkUrl' => route('twill.media-library.medias.bulk-update'),
            'deleteBulkUrl' => route('twill.media-library.medias.bulk-delete'),
            'metadatas' => [
                'default' => [
                    'caption' => $this->caption,
                    'altText' => $this->alt_text,
                    'video' => null,
                ] + Collection::make(config('twill.media_library.extra_metadatas_fields'))->mapWithKeys(function ($field) {
                    return [
                        $field['name'] => $this->{$field['name']},
                    ];
                })->toArray(),
                'custom' => [
                    'caption' => null,
                    'altText' => null,
                    'video' => null,
                ],
            ],
        ];
    }

    public function getMetadata($name, $fallback = null)
    {
        $metadatas = (object) json_decode($this->pivot->metadatas);
        $language = app()->getLocale();

        if ($metadatas->$name->$language ?? false) {
            return $metadatas->$name->$language;
        }

        $fallbackLocale = config('translatable.fallback_locale');

        if (in_array($name, config('twill.media_library.translatable_metadatas_fields', [])) && config('translatable.use_property_fallback', false) && ($metadatas->$name->$fallbackLocale ?? false)) {
            return $metadatas->$name->$fallbackLocale;
        }

        $fallbackValue = $fallback ? $this->$fallback : $this->$name;

        $fallback = $fallback ?? $name;

        if (in_array($fallback, config('twill.media_library.translatable_metadatas_fields', []))) {
            $fallbackValue = $fallbackValue[$language] ?? '';

            if ($fallbackValue === '' && config('translatable.use_property_fallback', false)) {
                $fallbackValue = $this->$fallback[config('translatable.fallback_locale')] ?? '';
            }
        }

        if (is_object($metadatas->$name ?? null)) {
            return $fallbackValue ?? '';
        }

        return $metadatas->$name ?? $fallbackValue ?? '';
    }

    public function replace($fields)
    {
        $prevHeight = $this->height;
        $prevWidth = $this->width;

        if ($this->update($fields) && $this->isReferenced()) {
            DB::table(config('twill.mediables_table', 'twill_mediables'))->where('media_id', $this->id)->get()->each(function ($mediable) use ($prevWidth, $prevHeight) {
                if ($prevWidth != $this->width) {
                    $mediable->crop_x = 0;
                    $mediable->crop_w = $this->width;
                }

                if ($prevHeight != $this->height) {
                    $mediable->crop_y = 0;
                    $mediable->crop_h = $this->height;
                }

                DB::table(config('twill.mediables_table', 'twill_mediables'))->where('id', $mediable->id)->update((array) $mediable);
            });
        }
    }

    public function delete()
    {
        if ($this->canDeleteSafely()) {
            return parent::delete();
        }

        return false;
    }

    public function getTable()
    {
        return config('twill.medias_table', 'twill_medias');
    }
}
