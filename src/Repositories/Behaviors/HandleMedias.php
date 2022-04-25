<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Media;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HandleMedias
{
    /**
     * @return \A17\Twill\Models\Model
     * @param mixed[] $fields
     */
    public function hydrateHandleMedias(\A17\Twill\Models\Model $object, array $fields)
    {
        if ($this->shouldIgnoreFieldBeforeSave('medias')) {
            return $object;
        }

        $mediasCollection = Collection::make();
        $mediasFromFields = $this->getMedias($fields);

        $mediasFromFields->each(function ($media) use ($object, $mediasCollection): void {
            $newMedia = Media::withTrashed()->find(is_array($media['id']) ? Arr::first($media['id']) : $media['id']);
            $pivot = $newMedia->newPivot($object, Arr::except($media, ['id']), config('twill.mediables_table', 'twill_mediables'), true);
            $newMedia->setRelation('pivot', $pivot);
            $mediasCollection->push($newMedia);
        });

        $object->setRelation('medias', $mediasCollection);

        return $object;
    }

    /**
     * @return void
     * @param mixed[] $fields
     */
    public function afterSaveHandleMedias(\A17\Twill\Models\Model $object, array $fields)
    {
        if ($this->shouldIgnoreFieldBeforeSave('medias')) {
            return;
        }

        $object->medias()->sync([]);

        $this->getMedias($fields)->each(function ($media) use ($object): void {
            $object->medias()->attach($media['id'], Arr::except($media, ['id']));
        });
    }

    /**
     * @return \Illuminate\Support\Collection
     * @param mixed[] $fields
     */
    private function getMedias(array $fields)
    {
        $medias = Collection::make();

        if (isset($fields['medias'])) {
            foreach ($fields['medias'] as $role => $mediasForRole) {
                if (config('twill.media_library.translated_form_fields', false) && Str::contains($role, ['[', ']'])) {
                    $start = strpos($role, '[') + 1;
                    $finish = strpos($role, ']', $start);
                    $locale = substr($role, $start, $finish - $start);
                    $role = strtok($role, '[');
                }

                $locale = $locale ?? config('app.locale');

                if (array_key_exists($role, $this->model->getMediasParams())
                    || array_key_exists($role, config('twill.block_editor.crops', []))
                    || array_key_exists($role, config('twill.settings.crops', []))) {
                    Collection::make($mediasForRole)->each(function ($media) use (&$medias, $role, $locale): void {
                        $customMetadatas = $media['metadatas']['custom'] ?? [];
                        if (isset($media['crops']) && ! empty($media['crops'])) {
                            foreach ($media['crops'] as $cropName => $cropData) {
                                $medias->push([
                                    'id' => $media['id'],
                                    'crop' => $cropName,
                                    'role' => $role,
                                    'locale' => $locale,
                                    'ratio' => $cropData['name'],
                                    'crop_w' => $cropData['width'],
                                    'crop_h' => $cropData['height'],
                                    'crop_x' => $cropData['x'],
                                    'crop_y' => $cropData['y'],
                                    'metadatas' => json_encode($customMetadatas),
                                ]);
                            }
                        } else {
                            foreach ($this->getCrops($role) as $cropName => $cropDefinitions) {
                                $medias->push([
                                    'id' => $media['id'],
                                    'crop' => $cropName,
                                    'role' => $role,
                                    'locale' => $locale,
                                    'ratio' => Arr::first($cropDefinitions)['name'],
                                    'crop_w' => null,
                                    'crop_h' => null,
                                    'crop_x' => null,
                                    'crop_y' => null,
                                    'metadatas' => json_encode($customMetadatas),
                                ]);
                            }
                        }
                    });
                }
            }
        }

        return $medias;
    }

    /**
     * @return array
     * @param mixed[] $fields
     */
    public function getFormFieldsHandleMedias(\A17\Twill\Models\Model $object, array $fields)
    {
        $fields['medias'] = null;

        if ($object->has('medias')) {
            foreach ($object->medias->groupBy('pivot.role') as $role => $mediasByRole) {
                if (config('twill.media_library.translated_form_fields', false)) {
                    foreach ($mediasByRole->groupBy('pivot.locale') as $locale => $mediasByLocale) {
                        foreach ($this->getMediaFormItems($mediasByLocale) as $item) {
                            $fields['medias'][$locale][$role][] = $item;
                        }
                    }
                } else {
                    foreach ($this->getMediaFormItems($mediasByRole) as $item) {
                        $fields['medias'][$role][] = $item;
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * @return array
     */
    private function getMediaFormItems(\Illuminate\Database\Eloquent\Collection $medias)
    {
        $itemsForForm = [];

        foreach ($medias->groupBy('id') as $mediasById) {
            $item = $mediasById->first();

            $itemForForm = $item->toCmsArray();

            $itemForForm['metadatas']['custom'] = json_decode($item->pivot->metadatas, true);

            foreach ($mediasById->groupBy('pivot.crop') as $crop => $mediaByCrop) {
                $media = $mediaByCrop->first();
                $itemForForm['crops'][$crop] = [
                    'name' => $media->pivot->ratio,
                    'width' => $media->pivot->crop_w,
                    'height' => $media->pivot->crop_h,
                    'x' => $media->pivot->crop_x,
                    'y' => $media->pivot->crop_y,
                ];
            }

            $itemsForForm[] = $itemForForm;
        }

        return $itemsForForm;
    }

    /**
     * @return array
     */
    public function getCrops(string $role)
    {
        return $this->model->getMediasParams()[$role];
    }
}
