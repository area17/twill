<?php

namespace A17\CmsToolkit\Repositories\Behaviors;

use A17\CmsToolkit\Models\File;

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

    public function getFormFieldsHandleFiles($object, $fields)
    {
        $fields['files'] = null;

        if ($object->has('files')) {
            foreach ($object->files->groupBy('pivot.role') as $role => $filesByRole) {
                foreach ($filesByRole->groupBy('pivot.locale') as $locale => $filesByLocale) {
                    foreach ($filesByLocale->groupBy('id') as $id => $filesById) {
                        $fields['files'][$role][$locale][$id] = $filesById->first();
                    }
                }
            }
        }

        return $fields;
    }

    private function getFiles($fields)
    {
        $files = collect();

        if (isset($fields['files'])) {
            foreach ($fields['files'] as $role => $locale) {
                foreach ($locale as $localeName => $localizedFiles) {
                    $fileCollection = collect($localizedFiles);

                    $transposedFileCollection = array_map(function (...$items) use ($fileCollection) {
                        return array_combine($fileCollection->keys()->all(), $items);
                    }, ...$fileCollection->values());

                    foreach ($transposedFileCollection as $file) {
                        $files->push(['id' => $file['id'], 'role' => $role, 'locale' => $localeName]);
                    }
                }
            }
        }

        return $files;
    }
}
