<?php

namespace A17\Twill\Models;

use FileService;
use Illuminate\Support\Facades\DB;

class File extends Model
{
    public $timestamps = true;

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
        return DB::table(config('twill.fileables_table', 'twill_fileables'))->where('file_id', $this->id)->count() === 0;
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

    public function getTable()
    {
        return config('twill.files_table', 'twill_files');
    }
}
