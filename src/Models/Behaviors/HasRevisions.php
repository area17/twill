<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Facades\TwillCapsules;

trait HasRevisions
{
    /**
     * Defines the one-to-many relationship for revisions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function revisions()
    {
        return $this->hasMany($this->getRevisionModel())->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to only include the current user's revisions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMine($query)
    {
        return $query->whereHas('revisions', function ($query) {
            $query->where('user_id', auth('twill_users')->user()->id);
        });
    }

    /**
     * Returns an array of revisions for the CMS views.
     *
     * @return array
     */
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

    protected function getRevisionModel()
    {
        $revision = config('twill.namespace') . "\Models\Revisions\\" . class_basename($this) . "Revision";

        if (@class_exists($revision))
        {
            return $revision;
        }

        return TwillCapsules::getCapsuleForModel(class_basename($this))->getRevisionModel();
    }
}
