<?php

namespace A17\CmsToolkit\Models;

use Cartalyst\Tags\TaggableInterface;
use Cartalyst\Tags\TaggableTrait;
use DB;

class Media extends Model implements TaggableInterface
{
    use TaggableTrait;

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

    protected static function boot()
    {
        parent::boot();
        static::setTagsModel(Tag::class);
    }

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
}
