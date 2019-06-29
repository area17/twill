<?php

namespace A17\Twill\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ImageService;

class Media extends Model
{
    public $timestamps = true;

    public $table = 'medias';

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

        parent::__construct($attributes);
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
        return DB::table('mediables')->where('media_id', $this->id)->count() === 0;
    }

    public function toCmsArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->filename,
            'thumbnail' => ImageService::getCmsUrl($this->uuid, ["h" => "256"]),
            'original' => ImageService::getRawUrl($this->uuid),
            'medium' => ImageService::getUrl($this->uuid, ["h" => "430"]),
            'width' => $this->width,
            'height' => $this->height,
            'tags' => $this->tags->map(function ($tag) {
                return $tag->name;
            }),
            'deleteUrl' => $this->canDeleteSafely() ? moduleRoute('medias', 'media-library', 'destroy', $this->id) : null,
            'updateUrl' => route('admin.media-library.medias.single-update'),
            'updateBulkUrl' => route('admin.media-library.medias.bulk-update'),
            'deleteBulkUrl' => route('admin.media-library.medias.bulk-delete'),
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
        $fallback = $fallback ? $this->$fallback : $this->$name;

        return $metadatas->$name->$language ?? (
            is_object($metadatas->$name ?? null)
            ? ($fallback ?? '')
            : ($metadatas->$name ?? $fallback)
        );
    }
}
