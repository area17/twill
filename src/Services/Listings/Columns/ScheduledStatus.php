<?php

namespace A17\Twill\Services\Listings\Columns;

use A17\Twill\Services\Listings\TableColumn;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ScheduledStatus extends TableColumn
{
    public static function make(): static
    {
        $column = new static();
        $column->field('scheduled_status');

        return $column;
    }

    public function getRenderValue(Model $model): string
    {
        $this->html = true;

        $startDate = Carbon::parse($model->publish_start_date);
        $endDate = Carbon::parse($model->publish_end_date);

        $expired = $model->publish_end_date && $endDate->isPast();
        $scheduled = $model->publish_start_date && $startDate->isFuture();

        $label = null;
        if ($scheduled) {
            $label = __('twill::lang.publisher.scheduled');
        } elseif ($expired) {
            $label = __('twill::lang.publisher.expired');
        }

        $format = config('twill.publish_date_display_format', 'F d, Y');

        return view('twill::listings.columns.scheduled-data', [
            'isScheduled' => $scheduled,
            'isExpired' => $expired,
            'label' => $label,
            'hasStartDate' => $model->publish_start_date,
            'startDate' => $startDate->format($format),
            'endDate' => $endDate->format($format),
        ])->render();
    }
}
