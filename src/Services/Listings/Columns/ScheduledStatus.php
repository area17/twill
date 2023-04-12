<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Models\Contracts\TwillModelContract;
use A17\Twill\Services\Listings\TableColumn;
use Carbon\Carbon;

class ScheduledStatus extends TableColumn
{
    public static function make(): static
    {
        $column = new static();
        $column->field('scheduled_status');

        return $column;
    }

    protected function getRenderValue(TwillModelContract $model): string
    {
        $this->html = true;

        $startDate = $model->publish_start_date ? Carbon::parse($model->publish_start_date) : null;
        $endDate = $model->publish_end_date ? Carbon::parse($model->publish_end_date) : null;

        $expired = $model->publish_end_date && $endDate->isPast();
        $scheduled = $model->publish_start_date && $startDate->isFuture();

        $label = null;
        if ($scheduled) {
            $label = twillTrans('twill::lang.publisher.scheduled');
        } elseif ($expired) {
            $label = twillTrans('twill::lang.publisher.expired');
        }

        $format = config('twill.publish_date_display_format', 'F d, Y');

        return view('twill::listings.columns.scheduled-data', [
            'isScheduled' => $scheduled,
            'isExpired' => $expired,
            'label' => $label,
            'hasStartDate' => $model->publish_start_date ?? false,
            'startDate' => $startDate?->format($format),
            'endDate' => $endDate?->format($format),
        ])->render();
    }
}
