<?php

namespace A17\Twill\Repositories\Behaviors;

use Carbon\Carbon;

trait HandleDates
{
    /**
     * @param array $fields
     * @return array
     */
    public function prepareFieldsBeforeCreateHandleDates($fields)
    {
        return $this->prepareFieldsBeforeSaveHandleDates(null, $fields);
    }

    /**
     * @param \A17\Twill\Models\Model|null $object
     * @param array $fields
     * @return array
     */
    public function prepareFieldsBeforeSaveHandleDates($object, $fields)
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
     * @param array $fields
     * @param string $f
     * @return array
     */
    public function prepareDatesField($fields, $f)
    {
        if ($date = Carbon::parse($fields[$f])) {
            $fields[$f] = $date->format("Y-m-d H:i:s");
        } else {
            $fields[$f] = null;
        }

        return $fields;
    }
}
