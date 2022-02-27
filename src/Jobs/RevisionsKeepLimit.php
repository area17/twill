<?php

namespace A17\Twill\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use A17\Twill\Models\Model;
use Exception;

class RevisionsKeepLimit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $modelClass;
    protected $id = 0;
    protected $maxAttempts = 3;

    public function __construct(Model $model, $id)
    {
        $this->modelClass = get_class($model);
        $this->id = $id;
    }

    public function handle()
    {

        // Check if revision limits are enabled
        if($this->checkIfLimitEnabled()){

            $maxRevisions = $this->getMaxRevisions();

            // New instance of our model
            $model = app($this->modelClass);

            // Check if returned type is integer
            if(is_int($maxRevisions)){

                // Wrap in trasanction closure
                return DB::transaction(function () use($model, $maxRevisions) {

                    // Call delete method on HasRevisions trait
                    $model->deleteSpecificRevisions($this->id, $maxRevisions);

                }, $this->maxAttempts);
            }

            // Check if this is bool value
            if($maxRevisions === true){

                $table = $this->getTableName();
                $revisions = config('twill.revisions', []);

                // Check if this models table is used on revisions limit list
                if(array_key_exists($table, $revisions)){

                    
                    // Check if revisions table name value contains valid integer type
                    if(!is_int($revisions[$table])){
                        throw new Exception("twill.revisions.{$table} accepts type integer. Provided: " . gettype($revisions[$table]));
                    }

                    // Wrap in trasanction closure
                    return DB::transaction(function () use($model, $revisions, $table) {

                        // Call delete function on HasRevisions trait
                        $model->deleteSpecificRevisions($this->id, $revisions[$table]);

                    }, $this->maxAttempts);

                }

            }

            return;

        }

        return;  
    }


    /**
     * Check if limit is enabled on revisions
     *
     * @return bool
     */
    protected function checkIfLimitEnabled()
    {
        // Check config
        return config('twill.max_revisions', false) ? true : false;
    }


    /**
     * Return number of revisions or bool 
     * Note: if boolean is returned we will check limits on specific models
     * 
     * @throws Exception
     * @return bool|int
     */
    protected function getMaxRevisions()
    {
        $maxRevisions = config('twill.max_revisions', true);
        return is_bool($maxRevisions) ? 
                        true : 
                        (is_int($maxRevisions) ? 
                            $maxRevisions : 
                            throw new Exception("twill.max_attempts accepts type integer or boolean. Provided: " . gettype($maxRevisions)));
    }


    /**
     * Returns Twill module table name
     *
     * @return string
     */
    protected function getTableName()
    {
        return app($this->modelClass)->getTable();
    }
    
}
