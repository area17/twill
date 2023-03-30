<?php

namespace A17\Twill\Models;

use A17\Twill\Models\Behaviors\HasDates;
use FileService;
use Illuminate\Support\Facades\DB;

class File extends Model
{
    use HasDates;

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

    public function scopeUnused ($query)
    {
        $usedIds = DB::table(config('twill.fileables_table'))->get()->pluck('file_id');
        return $query->whereNotIn('id', $usedIds->toArray())->get();
    }

    public function toCmsArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->filename,
            'src' => FileService::getUrl($this->uuid),
            'original' => FileService::getUrl($this->uuid),
            'size' => $this->size,
            'filesizeInMb' => number_format($this->attributes['size'] / 1048576, 2),
        ];
    }

    public function getTable()
    {
        return config('twill.files_table', 'twill_files');
    }
}
