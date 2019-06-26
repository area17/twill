<?php

namespace A17\Twill\Models;

use Illuminate\Support\Facades\DB;
use FileService;

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

    public function toCmsArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->filename,
            'src' => FileService::getUrl($this->uuid),
            'original' => FileService::getUrl($this->uuid),
            'size' => $this->size,
        ];
    }
}
