<?php

namespace A17\Twill\Models;

use FileService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\Relation;
use A17\Twill\Models\Behaviors\HasSlug;

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
            'owners' => $this->getOwnerDetails(),
        ];
    }

    public function getTable()
    {
        return config('twill.files_table', 'twill_files');
    }

    public function getOwners()
    {
        $morphMap = Relation::morphMap();

        $owners = collect(
            DB::table(config('twill.fileables_table', 'twill_fileables'))
                ->where('file_id', $this->id)->get()
            );

        return $owners->map(function ($owner) use ($morphMap){
            $resolvedClass =  array_key_exists($owner->fileable_type, $morphMap) ? $morphMap[ $owner->fileable_type ] : $owner->fileable_type;

            return resolve($resolvedClass)::find($owner->fileable_id);

        });
    }

    public function getOwnerDetails()
    {
        $owners =  $this->getOwners();

        return collect(($owners))->filter(function ($value){
            return is_object($value);
        })->map(function ($item){
            $module = Str::plural(lcfirst((new \ReflectionClass($item))->getShortName()));

            if ($item instanceof Block){
                $model=$item->blockable;

                $module = $model ? Str::plural(lcfirst((new \ReflectionClass($model))->getShortName())): null;

                return ($model && $module) ? [
                    'id' => $model->id,
                    'slug' => classHasTrait($model, HasSlug::class) ? $model->slug : null,
                    'name' => $model->{$model->titleKey},
                    'titleKey' => $model->titleKey,
                    'model'=>$model,
                    'module'=>$module,
                    'edit' => moduleRoute($module, config('twill.block_editor.browser_route_prefixes.' . $module), 'edit', $model->id),
                ] : [];

            }

            return [
                'id' => $item->id,
                'slug' => classHasTrait($item, HasSlug::class) ? $item->slug : null,
                'name' => $item->{$item->titleKey},
                'titleKey' => $item->titleKey,
                'model'=>$item,
                'module'=>$module,
                'edit' => moduleRoute($module, config('twill.block_editor.browser_route_prefixes.' . $module), 'edit', $item->id),
            ];

        })->filter()->values()->toArray();

    }
}
