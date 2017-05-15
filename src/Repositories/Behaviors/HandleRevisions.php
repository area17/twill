<?php

namespace A17\CmsToolkit\Repositories\Behaviors;

use Auth;

trait HandleRevisions
{
    protected $except = [
        '_method',
        '_token',
        'continue',
        'finish',
    ];

    public function beforeSaveHandleRevisions($object, $fields)
    {
        $requestPayload = array_except($fields, $this->except);
        $lastRevisionPayload = json_decode($object->revisions->first()->payload ?? "{}", true);

        if ($this->payloadChanged($requestPayload, $lastRevisionPayload)) {
            $object->revisions()->create([
                'payload' => json_encode($requestPayload),
                'user_id' => Auth::user()->id ?? null,
            ]);
        }

        return $fields;
    }

    public function previewForRevision($id, $revisionId)
    {
        $object = $this->model->findOrFail($id);

        $fields = json_decode($object->revisions->where('id', $revisionId)->first()->payload, true);

        return $this->hydrateObject($object, $fields);
    }

    public function previewForCompare($id)
    {
        $object = $this->model->findOrFail($id);

        $fields = json_decode($object->revisions->first()->payload, true);

        return $this->hydrateObject($object, $fields);
    }

    private function payloadChanged($requestPayload, $revisionPayload)
    {
        $requestPayloadValues = array_values($requestPayload);
        $revisionPayloadValues = array_values($revisionPayload);

        return array_sort_recursive($requestPayloadValues) !== array_sort_recursive($revisionPayloadValues);
    }
}
