<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Facades\TwillCapsules;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasRevisions
{
    /**
     * Defines the one-to-many relationship for revisions.
     */
    public function revisions(): HasMany
    {
        return $this->hasMany($this->getRevisionModel())->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to only include the current user's revisions.
     */
    public function scopeMine(Builder $query): Builder
    {
        return $query->whereHas('revisions', function ($query) {
            $query->where('user_id', auth('twill_users')->user()->id);
        });
    }

    /**
     * Returns an array of revisions for the CMS views.
     */
    public function revisionsArray(): array
    {
        $currentRevision = null;

        return $this->revisions
            ->map(function ($revision, $index) use (&$currentRevision) {
                if (!$currentRevision && !$revision->isDraft()) {
                    $currentRevision = $revision;
                }

                return [
                    'id' => $revision->id,
                    'author' => $revision->user->name ?? 'Unknown',
                    'datetime' => $revision->created_at->toIso8601String(),
                    'label' => $currentRevision === $revision ? twillTrans('twill::lang.publisher.current') : '',
                ];
            })
            ->toArray();
    }

    /**
     * Deletes revisions from specific collection position
     * Used to keep max revision on specific Twill's module
     */
    public function deleteSpecificRevisions(int $maxRevisions): void
    {
        if (isset($this->limitRevisions) && $this->limitRevisions > 0) {
            $maxRevisions = $this->limitRevisions;
        }

        $this->revisions()->get()->slice($maxRevisions)->each->delete();
    }

    protected function getRevisionModel(): string
    {
        $revision = config('twill.namespace') . "\Models\Revisions\\" . class_basename($this) . "Revision";

        if (@class_exists($revision)) {
            return $revision;
        }

        return TwillCapsules::getCapsuleForModel(class_basename($this))->getRevisionModel();
    }
}
