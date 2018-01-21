<?php

namespace A17\CmsToolkit\Models\Behaviors;

trait HasRevisions
{
    public function revisions()
    {
        return $this->hasMany("App\Models\Revisions\\" . class_basename($this) . "Revision")->orderBy('created_at', 'desc');
    }

    public function scopeMine($query)
    {
        return $query->whereHas('revisions', function ($query) {
            $query->where('user_id', auth()->user()->id);
        });
    }
}
