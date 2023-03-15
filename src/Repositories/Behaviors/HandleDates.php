<?php

namespace A17\Twill\Repositories\Behaviors;

use A17\Twill\Models\Contracts\TwillModelContract;
use Carbon\Carbon;

trait HandleDates
{
    public function prepareFieldsBeforeCreateHandleDates(array $fields): array
    {
        return $this->prepareFieldsBeforeSaveHandleDates(null, $fields);
    }

    public function prepareFieldsBeforeSaveHandleDates(?TwillModelContract $object, array $fields): array
    {
        foreach ($fields as $name => $fieldData) {
            if ($this->model->hasCast($name, ['date', 'datetime'])) {
                if (!empty($fields[$name])) {
                    $fields = $this->prepareDatesField($fields, $name);
                } else {
                    $fields[$name] = null;
                }
            }
        }

        return $fields;
    }

    public function prepareDatesField(array $fields, string $field): array
    {
        $fields[$field] = Carbon::parse($fields[$field]);

        return $fields;
    }
}
