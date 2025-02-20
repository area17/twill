<?php

namespace A17\Twill\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use A17\Twill\Models\Model;

class ReorderNestedModuleItems implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    protected $modelClass;

    protected $ids;

    public function __construct(Model $model, array $ids)
    {
        $this->modelClass = get_class($model);
        $this->ids = $ids;
    }

    public function handle()
    {
        DB::transaction(function () {
            app($this->modelClass)->saveTreeFromIds($this->ids);
        }, 3);
    }
}
