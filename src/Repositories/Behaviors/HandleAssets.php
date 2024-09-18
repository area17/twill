<?php

namespace A17\Twill\Repositories\Behaviors;

trait HandleAssets
{
    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return array
     */
    public function getFormFieldsHandleAssets($object, $fields)
    {
        $fields['assets'] = null;

        if ($object->has('medias')) {
            foreach ($object->medias->groupBy('pivot.role') as $role => $mediasByRole) {
                if (
                    in_array($role, $this->model->assetParams ?? [])
                    || in_array($role, config('twill.block_editor.assets', []))
                ) {
                    foreach ($mediasByRole->groupBy('pivot.locale') as $locale => $mediasByLocale) {
                        foreach ($this->getMediaFormItems($mediasByLocale) as $item) {
                            $fields['assets'][$locale][$role][] = $item + ['type' => 'image'];
                        }
                    }
                }
            }
        }

        if ($object->has('files')) {
            foreach ($object->files->groupBy('pivot.role') as $role => $filesByRole) {
                if (
                    in_array($role, $this->model->assetParams ?? [])
                    || in_array($role, config('twill.block_editor.assets', []))
                ) {
                    foreach ($filesByRole->groupBy('pivot.locale') as $locale => $filesByLocale) {
                        foreach ($filesByLocale as $item) {
                            $fields['assets'][$locale][$role][] = $item->toCmsArray() + [
                                'pivot_id' => $item->pivot->id,
                                'position' => $item->pivot->position,
                                'type' => 'file',
                            ];
                        }
                    }
                }
            }
        }

        if (isset($fields['assets'])) {
            foreach ($fields['assets'] as $locale => $filesByLocale) {
                foreach ($filesByLocale as $role => $filesByRole) {
                    $fields['assets'][$locale][$role] = collect($filesByRole)->sortBy('position')->values()->toArray();
                }
            }
        }

        return $fields;
    }
}
