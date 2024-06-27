<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return void
     */
    public function afterSaveHandleFiles($object, $fields)
    {
        if ($this->shouldIgnoreFieldBeforeSave('files')) {
            return;
        }

        $filesToDelete = [];

        $previousFiles = $object->files;
        $files = $this->getFiles($fields);

        if (!$previousFiles->isEmpty()) {
            $previousFiles->each(function ($previousFile) use (&$files, &$filesToDelete) {
                $matchingFile = $files->first(function ($newFile) use ($previousFile) {
                    return $newFile['file_id'] == $previousFile->pivot->file_id
                        && $newFile['role'] == $previousFile->pivot->role
                        && $newFile['locale'] == $previousFile->pivot->locale;
                });

                if ($matchingFile) {
                    $files = $files->reject(function ($newFile) use ($matchingFile) {
                        return $newFile['file_id'] == $matchingFile['file_id']
                            && $newFile['role'] == $matchingFile['role']
                            && $newFile['locale'] == $matchingFile['locale'];
                    });
                } else {
                    $filesToDelete[] = $previousFile->pivot->id;
                }
            });
        }

        $files->each(function ($file) use ($object) {
            $object->files()->attach($file['file_id'], Arr::except($file, ['file_id']));
        });

        if (! empty($filesToDelete)) {
            DB::table(config('twill.fileables_table', 'twill_fileables'))
                ->whereIn('id', $filesToDelete)->delete();
        };
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
                    Collection::make($filesForRole)->each(function ($file) use (&$files, $role, $locale) {
                        $files->push([
                            'file_id' => $file['id'],
                            'role' => $role,
                            'locale' => $locale,
                        ]);
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
                    $fields['files'][$locale][$role] = $filesByLocale->map(function ($file) {
                        return $file->toCmsArray();
                    });
                }
            }
        }

        return $fields;
    }

    public function afterDuplicateHandleFiles(TwillModelContract $object, TwillModelContract $newObject): void
    {
        $newObject->files()->sync(
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
