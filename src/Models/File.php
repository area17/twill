<?php

namespace A17\CmsToolkit\Models;

use DB;

class File extends Model
{
    public $timestamps = true;

    public $table = 'files';

    protected $fillable = [
        'uuid',
        'filename',
        'size',
    ];

    public function getSizeAttribute($value)
    {
        return bytesToHuman($value);
    }

    public function canDeleteSafely()
    {
        return DB::table('fileables')->where('file_id', $this->id)->count() === 0;
    }
}
