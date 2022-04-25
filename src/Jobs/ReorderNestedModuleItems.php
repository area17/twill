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


    public function __construct(Model $model, protected array $ids)
    {
        $this->modelClass = $model::class;
    }

    public function handle(): void
    {
        DB::transaction(function (): void {
            app($this->modelClass)->saveTreeFromIds($this->ids);
        }, 3);
    }
}
