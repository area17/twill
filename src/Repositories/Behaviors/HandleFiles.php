<?php

namespace A17\CmsToolkit\Repositories\Behaviors;

trait HandleFiles
{
    public function afterSaveHandleFiles($object, $fields)
    {
        $object->files()->sync([]);

        if (isset($fields['files'])) {
            foreach ($fields['files'] as $role => $locale) {
                foreach ($locale as $localeName => $localizedFiles) {
                    $fileCollection = collect($localizedFiles);

                    $transposedFileCollection = array_map(function (...$items) use ($fileCollection) {
                        return array_combine($fileCollection->keys()->all(), $items);
                    }, ...$fileCollection->values());

                    foreach ($transposedFileCollection as $file) {
                        $object->files()->attach($file['id'], ['role' => $role, 'locale' => $localeName]);
                    }
                }
            }
        }
    }

    public function getFormFieldsHandleFiles($object, $fields)
    {

        if (old('files')) {
            $currentFiles = $object->files()->get();
            $this->afterSaveHandleFiles($object, ['files ' => old('files')]);
        }

        if ($object->has('files')) {

            foreach ($object->files->groupBy('pivot.role') as $role => $filesByRole) {
                foreach ($filesByRole->groupBy('pivot.locale') as $locale => $filesByLocale) {
                    foreach ($filesByLocale->groupBy('id') as $id => $filesById) {
                        $fields['files'][$role][$locale][$id] = $filesById->first();
                    }
                }
            }
        }

        if (old('files')) {
            $object->files()->sync([]);
            foreach ($currentFiles as $file) {
                $object->files()->attach($file->id, $file->pivot->toArray());
            }
        }

        return $fields;
    }
}
