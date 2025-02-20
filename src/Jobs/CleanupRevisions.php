<?php

namespace A17\Twill\Jobs;

use A17\Twill\Facades\TwillConfig;
use A17\Twill\Models\Behaviors\HasRevisions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use A17\Twill\Models\Model;

class CleanupRevisions implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    protected int $maxAttempts = 3;
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function handle(): void
    {
        if (!classHasTrait($this->model, HasRevisions::class)) {
            return;
        }

        $maxRevisions = null;

        if (isset($this->model->limitRevisions) || $maxRevisions = TwillConfig::getRevisionLimit()) {
            // It could be that $maxRevisions is null because it's only set on the model.
            DB::transaction(function () use ($maxRevisions) {
                $this->model->deleteSpecificRevisions($maxRevisions ?? 0);
            }, $this->maxAttempts);
        }
    }
}
