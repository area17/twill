<?php

namespace A17\CmsToolkit\Models\Behaviors;

trait HasRevisions
{
    public function revisions()
    {
        return $this->hasMany("App\Models\Revisions\\" . class_basename($this) . "Revision")->orderBy('created_at', 'desc');
    }
}
