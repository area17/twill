<?php

namespace A17\CmsToolkit\Repositories\Behaviors;

use DateTime;

trait HandleDates
{

    public function getFormFieldsHandleDates($object, $fields)
    {
        foreach ($object->getDates() as $f) {
            if (isset($fields[$f]) && !empty($fields[$f])) {
                $fields = $this->getDatesField($fields, $f);
            }
        }

        return $fields;
    }

    public function prepareFieldsBeforeCreateHandleDates($fields)
    {
        return $this->prepareFieldsBeforeSaveHandleDates(null, $fields);
    }

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

    public function getDatesField($fields, $f)
    {
        if (($dateTime = DateTime::createFromFormat("Y-m-d H:i:s", $fields[$f]))) {
            $fields[$f] = $dateTime->format("m/d/Y H:i");
        }

        return $fields;
    }

    public function prepareDatesField($fields, $f)
    {
        if (($datetime = DateTime::createFromFormat("m/d/Y H:i", $fields[$f]))) {
            $fields[$f] = $datetime->format("Y-m-d H:i:s");
        } else {
            $fields[$f] = null;
        }

        return $fields;
    }
}
