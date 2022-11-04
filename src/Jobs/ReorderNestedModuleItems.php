<?php

namespace A17\Twill\Jobs;

use A17\Twill\Models\Model;
use A17\Twill\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class ReorderNestedModuleItems implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $modelClass;
    protected $ids;
    protected $user;

    public function __construct(Model $model, array $ids, User $user)
    {
        $this->modelClass = get_class($model);
        $this->ids = $ids;
        $this->user = $user;
    }

    public function handle()
    {
        DB::transaction(function () {
            $savedModels = app($this->modelClass)->saveTreeFromIds($this->ids);
            // Log the reordered models
            foreach ($savedModels as $model) {
                activity()->performedOn($model)->causedBy($this->user)->log('reordered');
            }
        }, 3);
    }
}
