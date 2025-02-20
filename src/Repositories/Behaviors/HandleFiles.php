<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Facades\TwillUtil;
use A17\Twill\Models\Behaviors\HasFiles;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\File;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

trait HandleFiles
{
    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return \A17\Twill\Models\Model
     */
    public function hydrateHandleFiles($object, $fields)
    {
        if ($this->shouldIgnoreFieldBeforeSave('files')) {
            return $object;
        }

        $filesCollection = Collection::make();
        $filesFromFields = $this->getFiles($fields);

        $filesFromFields->each(function ($file) use ($object, $filesCollection) {
            $newFile = File::withTrashed()->find($file['file_id']);
            $pivot = $newFile->newPivot($object, $file, 'fileables', true);
            $newFile->setRelation('pivot', $pivot);
            $filesCollection->push($newFile);
        });

        $object->setRelation('files', $filesCollection);

        return $object;
    }

    /**
     * @param \A17\Twill\Models\Model|HasFiles $object
     * @param array $fields
     * @return void
     */
    public function afterSaveHandleFiles($object, $fields)
    {
        if ($this->shouldIgnoreFieldBeforeSave('files')) {
            return;
        }

        TwillUtil::syncUsingPrimaryKey($object->files(), $this->getFiles($fields));
    }

    /**
     * @param array $fields
     * @return \Illuminate\Support\Collection
     */
    private function getFiles($fields)
    {
        $files = Collection::make();

        if (isset($fields['medias'])) {
            foreach ($fields['medias'] as $role => $filesForRole) {
                if (Str::contains($role, ['[', ']'])) {
                    $start = strpos($role, '[') + 1;
                    $finish = strpos($role, ']', $start);
                    $locale = substr($role, $start, $finish - $start);
                    $role = strtok($role, '[');
                }

                $locale = $locale ?? config('app.locale');
                if (
                    in_array($role, $this->model->filesParams ?? [])
                    || in_array($role, config('twill.block_editor.files', []))
                ) {
                    Collection::make($filesForRole)->each(function ($file, $index) use (&$files, $role, $locale) {
                        $files[$file['pivot_id'] ?? uniqid('file')] = [
                            'file_id' => $file['id'],
                            'role' => $role,
                            'locale' => $locale,
                            'position' => $index + 1
                        ];
                    });
                }
            }
        }

        return $files;
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return array
     */
    public function getFormFieldsHandleFiles($object, $fields)
    {
        $fields['files'] = null;
        if ($object->has('files')) {
            foreach ($object->files->groupBy('pivot.role') as $role => $filesByRole) {
                foreach ($filesByRole->groupBy('pivot.locale') as $locale => $filesByLocale) {
                    $fields['files'][$locale][$role] = $filesByLocale->map(function (File $file) {
                        return $file->toCmsArray() + ['pivot_id' => $file->pivot->id];
                    });
                }
            }
        }

        return $fields;
    }

    /**
     * @param HasFiles|TwillModelContract $object
     * @param HasFiles|TwillModelContract $newObject
     */
    public function afterDuplicateHandleFiles(TwillModelContract $object, TwillModelContract $newObject): void
    {
        $newObject->files()->attach(
            $object->files->mapWithKeys(function ($file) use ($object) {
                return [
                    $file->id => Collection::make($object->files()->getPivotColumns())->mapWithKeys(
                        function ($attribute) use ($file) {
                            return [$attribute => $file->pivot->$attribute];
                        }
                    )->toArray(),
                ];
            })
        );
    }
}
