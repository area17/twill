<?php

namespace A17\CmsToolkit\Models;

use DB;
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

    public function getDimensionsAttribute()
    {
        return $this->width . 'x' . $this->height;
    }

    public function altTextFrom($filename)
    {
        $filename = pathinfo($filename, PATHINFO_FILENAME);
        if (ends_with($filename, '@2x')) {
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
        ];
    }
}
