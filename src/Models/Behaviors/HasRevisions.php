<?php

namespace A17\CmsToolkit\Models\Behaviors;

trait HasRevisions
{
    public function revisions()
    {
        return $this->hasMany("App\Models\Revisions\\" . class_basename($this) . "Revision")->orderBy('created_at', 'desc');
    }

    public function revisionsForPublisher()
    {
        return $this->revisions->map(function ($revision) {
            return [
                'id' => $revision->id,
                'author' => $revision->user->name,
                'datetime' => $revision->created_at->format('M j, Y g:i A'),
            ];
        })->toArray();
    }
}
