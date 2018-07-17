<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\File;

trait HandleFiles
{
    public function hydrateHandleFiles($object, $fields)
    {
        if ($this->shouldIgnoreFieldBeforeSave('files')) {
            return $object;
        }

        $filesCollection = collect();
        $filesFromFields = $this->getFiles($fields);

        $filesFromFields->each(function ($file) use ($object, $filesCollection) {
            $newFile = File::withTrashed()->find($file['id']);
            $pivot = $newFile->newPivot($object, array_except($file, ['id']), 'fileables', true);
            $newFile->setRelation('pivot', $pivot);
            $filesCollection->push($newFile);
        });

        $object->setRelation('files', $filesCollection);

        return $object;
    }

    public function afterSaveHandleFiles($object, $fields)
    {
        if ($this->shouldIgnoreFieldBeforeSave('files')) {
            return;
        }

        $object->files()->sync([]);

        $this->getFiles($fields)->each(function ($file) use ($object) {
            $object->files()->attach($file['id'], array_except($file, ['id']));
        });
    }

    private function getFiles($fields)
    {
        $files = collect();

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
                    collect($filesForRole)->each(function ($file) use (&$files, $role, $locale) {
                        $files->push([
                            'id'     => $file['id'],
                            'role'   => $role,
                            'locale' => $locale,
                        ]);
                    });
                }
            }
        }

        return $files;
    }

    public function getFormFieldsHandleFiles($object, $fields)
    {
        $fields['files'] = null;

        if ($object->has('files')) {
            foreach ($object->files->groupBy('pivot.role') as $role => $filesByRole) {
                foreach ($filesByRole->groupBy('pivot.locale') as $locale => $filesByLocale) {
                    $fields['files'][$locale][$role] = $filesByRole->map(function ($file) {
                        return $file->toCmsArray();
                    });
                }
            }
        }

        return $fields;
    }
}
