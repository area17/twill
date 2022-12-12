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
        foreach ($this->model->getDates() as $f) {
            if (isset($fields[$f])) {
                if (!empty($fields[$f])) {
                    $fields = $this->prepareDatesField($fields, $f);
                } else {
                    $fields[$f] = null;
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
