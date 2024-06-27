<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Models\Media;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait HandleMedias
{
    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return \A17\Twill\Models\Model
     */
    public function hydrateHandleMedias($object, $fields)
    {
        if ($this->shouldIgnoreFieldBeforeSave('medias')) {
            return $object;
        }

        $mediasCollection = Collection::make();
        $mediasFromFields = $this->getMedias($fields);

        $mediasFromFields->each(function ($media) use ($object, $mediasCollection) {
            $newMedia = Media::withTrashed()->find($media['media_id']);
            $pivot = $newMedia->newPivot(
                $object,
                $media,
                config('twill.mediables_table', 'twill_mediables'),
                true
            );
            $newMedia->setRelation('pivot', $pivot);
            $mediasCollection->push($newMedia);
        });

        $object->setRelation('medias', $mediasCollection);

        return $object;
    }

    /**
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return void
     */
    public function afterSaveHandleMedias($object, $fields)
    {
        if ($this->shouldIgnoreFieldBeforeSave('medias')) {
            return;
        }

        $mediasToUpdate = [];
        $mediasToDelete = [];

        $previousMedias = $object->medias;
        $medias = $this->getMedias($fields);

        if (!$previousMedias->isEmpty()) {
            $previousMedias->each(function ($previousMedia) use (&$medias, &$mediasToUpdate, &$mediasToDelete) {
                $matchingMedia = $medias->first(function ($newMedia) use ($previousMedia) {
                    return $newMedia['media_id'] == $previousMedia->pivot->media_id
                        && $newMedia['role'] == $previousMedia->pivot->role
                        && $newMedia['crop'] == $previousMedia->pivot->crop
                        && $newMedia['locale'] == $previousMedia->pivot->locale;
                });

                if ($matchingMedia) {
                    if (
                        $matchingMedia['ratio'] != $previousMedia->pivot->ratio
                        || $matchingMedia['crop_w'] != $previousMedia->pivot->crop_w
                        || $matchingMedia['crop_h'] != $previousMedia->pivot->crop_h
                        || $matchingMedia['crop_x'] != $previousMedia->pivot->crop_x
                        || $matchingMedia['crop_y'] != $previousMedia->pivot->crop_y
                        || json_decode($matchingMedia['metadatas']) != json_decode($previousMedia->pivot->metadatas)
                    ) {
                        $mediasToUpdate[$previousMedia->pivot->id] = $matchingMedia;
                    }

                    $medias = $medias->reject(function ($newMedia) use ($matchingMedia) {
                        return $newMedia['media_id'] == $matchingMedia['media_id']
                            && $newMedia['role'] == $matchingMedia['role']
                            && $newMedia['crop'] == $matchingMedia['crop']
                            && $newMedia['locale'] == $matchingMedia['locale'];
                    });
                } else {
                    $mediasToDelete[] = $previousMedia->pivot->id;
                }
            });
        }

        $medias->each(function ($media) use ($object) {
            $object->medias()->attach($media['media_id'], Arr::except($media, ['media_id']));
        });

        $mediablesTable = config('twill.mediables_table', 'twill_mediables');

        if (! empty($mediasToDelete)) {
            DB::table($mediablesTable)->whereIn('id', $mediasToDelete)->delete();
        };

        foreach ($mediasToUpdate as $pivotId => $media) {
            DB::table($mediablesTable)->where('id', $pivotId)->update($media);
        }
    }

    /**
     * @param array $fields
     * @return \Illuminate\Support\Collection
     */
    private function getMedias($fields)
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

                if ($this->hasRole($role) || $this->hasJsonRepeaterRole($role)) {
                    Collection::make($mediasForRole)->each(function ($media) use (&$medias, $role, $locale) {
                        $customMetadatas = $media['metadatas']['custom'] ?? [];
                        if (isset($media['crops']) && !empty($media['crops'])) {
                            foreach ($media['crops'] as $cropName => $cropData) {
                                $medias->push([
                                    'media_id' => $media['id'],
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
                                    'media_id' => $media['id'],
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
     * @param \A17\Twill\Models\Model $object
     * @param array $fields
     * @return array
     */
    public function getFormFieldsHandleMedias($object, $fields)
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
     * @param \Illuminate\Database\Eloquent\Collection $medias
     * @return array
     */
    private function getMediaFormItems($medias)
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
     * @param string $role
     * @return array
     */
    public function getCrops($role)
    {
        return $this->model->getMediasParams()[$role];
    }

    public function afterDuplicateHandleMedias(TwillModelContract $original, TwillModelContract $newObject): void
    {
        foreach ($original->medias as $media) {
            $newPushData = [
                'crop' => $media->pivot->crop,
                'role' => $media->pivot->role,
                'ratio' => $media->pivot->ratio,
                'crop_w' => $media->pivot->crop_w,
                'crop_h' => $media->pivot->crop_h,
                'crop_x' => $media->pivot->crop_x,
                'crop_y' => $media->pivot->crop_y,
                'metadatas' => $media->pivot->metadatas,
                'locale' => $media->pivot->locale,
            ];

            $newObject->medias()->attach($media->id, $newPushData);
        }
    }

    private function hasRole($role): bool
    {
        return array_key_exists($role, $this->model->getMediasParams())
        || array_key_exists($role, TwillBlocks::getAllCropConfigs())
        || array_key_exists($role, config('twill.settings.crops', []));
    }

    private function hasJsonRepeaterRole($role): bool
    {
        if (! Str::contains($role, '|')) {
            return false;
        }

        $role = last(explode('|', $role));
        return $this->hasRole($role);
    }
}
