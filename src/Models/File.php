<?php

namespace A17\CmsToolkit\Models;

use Cartalyst\Tags\TaggableInterface;
use Cartalyst\Tags\TaggableTrait;
use DB;

class File extends Model implements TaggableInterface
{
    use TaggableTrait;

    public $timestamps = true;

    public $table = 'files';

    protected $fillable = [
        'uuid',
        'filename',
        'size',
    ];

    protected static function boot()
    {
        parent::boot();
        static::setTagsModel(Tag::class);
    }

    public function getSizeAttribute($value)
    {
        return bytesToHuman($value);
    }

    public function canDeleteSafely()
    {
        return DB::table('fileables')->where('file_id', $this->id)->count() === 0;
    }
}
