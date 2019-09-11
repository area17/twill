<?php

namespace A17\Twill\Models\Behaviors;

trait HasRevisions
{
    public function revisions()
    {
        return $this->hasMany(config('twill.namespace') . "\Models\Revisions\\" . class_basename($this) . "Revision")->orderBy('created_at', 'desc');
    }

    public function scopeMine($query)
    {
        return $query->whereHas('revisions', function ($query) {
            $query->where('user_id', auth('twill_users')->user()->id);
        });
    }

    public function revisionsArray()
    {
        return $this->revisions->map(function ($revision) {
            return [
                'id' => $revision->id,
                'author' => $revision->user->name ?? 'Unknown',
                'datetime' => $revision->created_at->toIso8601String(),
            ];
        })->toArray();
    }
}
