<?php

namespace A17\Twill\Repositories\Behaviors;

use Carbon\Carbon;

trait HandleDates
{
    /**
     * @return array
     * @param mixed[] $fields
     */
    public function prepareFieldsBeforeCreateHandleDates(array $fields)
    {
        return $this->prepareFieldsBeforeSaveHandleDates(null, $fields);
    }

    /**
     * @return array
     * @param mixed[] $fields
     */
    public function prepareFieldsBeforeSaveHandleDates(?\A17\Twill\Models\Model $object, array $fields)
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

    /**
     * @return array
     * @param mixed[] $fields
     */
    public function prepareDatesField(array $fields, string $f)
    {
        $fields[$f] = ($date = Carbon::parse($fields[$f])) ? $date->format("Y-m-d H:i:s") : null;

        return $fields;
    }
}
