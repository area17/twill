<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\File;
use Illuminate\Support\Arr;
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
            $newFile = File::withTrashed()->find($file['id']);
            $pivot = $newFile->newPivot($object, Arr::except($file, ['id']), 'fileables', true);
            $newFile->setRelation('pivot', $pivot);
            $filesCollection->push($newFile);
        });

        $object->setRelation('files', $filesCollection);

        return $object;
    }

    /**
     * @param \A17\Twill\Models\Behaviors\HasFiles $object
     * @param array $fields
     * @return void
     */
    public function afterSaveHandleFiles($object, $fields)
    {
        if ($this->shouldIgnoreFieldBeforeSave('files')) {
            return;
        }

        $object->files()->sync([]);

        $this->getFiles($fields)->each(function ($file) use ($object) {
            $object->files()->attach($file['id'], Arr::except($file, ['id']));
        });
    }

    /**
     * @param $fields
     * @return \Illuminate\Support\Collection
     */
    private function getFiles($fields)
    {
        $files = Collection::make();

        if (isset($fields['medias'])) {
            foreach ($fields['medias'] as $role => $filesForRole) {
                if (str_contains($role, ['[', ']'])) {
                    $start = strpos($role, '[') + 1;
                    $finish = strpos($role, ']', $start);
                    $locale = substr($role, $start, $finish - $start);
                    $role = strtok($role, '[');
                }

                $locale = $locale ?? config('app.locale');
                if (in_array($role, $this->model->filesParams ?? [])
                    || in_array($role, config('twill.block_editor.files', []))) {

                    Collection::make($filesForRole)->each(function ($file) use (&$files, $role, $locale) {
                        $files->push([
                            'id' => $file['id'],
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
}
