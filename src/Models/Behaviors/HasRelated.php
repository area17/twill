<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\RelatedItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

trait HasRelated
{
    protected array $relatedCache;

    /**
     * Defines the one-to-many relationship for related items.
     */
    public function relatedItems(): MorphMany
    {
        return $this->morphMany(RelatedItem::class, 'subject')->orderBy('position');
    }

    /**
     * Returns the related items for a browser field.
     */
    public function getRelated(string $browserName): Collection
    {
        if (! isset($this->relatedCache[$browserName]) || $this->relatedCache[$browserName] === null) {
            $this->loadRelated($browserName);
        }

        return $this->relatedCache[$browserName];
    }

    public function getFirstRelated(string $browserName): mixed
    {
        return $this->getRelated($browserName)->first();
    }

    /**
     * Eager load related items for a browser field.
     */
    public function loadRelated(string $browserName): Collection
    {
        if (! isset($this->relatedItems)) {
            $this->load('relatedItems');
        }

        return $this->relatedCache[$browserName] = $this->relatedItems
            ->where('browser_name', $browserName)
            ->map(function ($item) {
                /** @var \A17\Twill\Models\Model $model */
                if ($model = $item->related) {
                    $model->setRelation('pivot', $item);
                    $item->unsetRelation('related');

                    return $model;
                }

                return null;
            })->filter();
    }

    /**
     * Attach items to the model for a browser field.
     *
     * @param array<int, Model|array> $items
     */
    public function saveRelated(array|Collection|Model $items, string $browserName): void
    {
        $items = is_array($items) || $items instanceof Collection ? $items : [$items];

        /** @var Collection<int, RelatedItem> $itemsToProcess */
        $itemsToProcess = $this->relatedItems()->where('browser_name', $browserName)->get();

        foreach ($items as $position => $item) {
            if ($item instanceof Model) {
                $id = $item->getKey();
                $type = $item->getMorphClass();
            } else {
                $id = $item['id'];
                $type = $item['endpointType'];
            }

            $firstMatchKey = $itemsToProcess
                ->where(fn (RelatedItem $item) => $item->related_id == $id && $item->related_type === $type)
                // We should only have one item always as you cannot select the same items twice.
                ->keys()
                ->first();

            // This needs to be a strict "null" comparison, as it would otherwise pass on key 0.
            if ($firstMatchKey !== null) {
                $match = $itemsToProcess[$firstMatchKey];
                $match->position = $position + 1;
                if ($match->isDirty('position')) {
                    $match->save();
                }
            } else {
                RelatedItem::create([
                    'subject_id' => $this->getKey(),
                    'subject_type' => $this->getMorphClass(),
                    'related_id' => $id,
                    'related_type' => $type,
                    'browser_name' => $browserName,
                    'position' => $position + 1,
                ]);
            }

            // Unset the item, this way we have only items to delete left.
            $itemsToProcess->offsetUnset($firstMatchKey);
        }

        RelatedItem::whereIn('id', $itemsToProcess->pluck('id')->toArray())->delete();
    }

    public function clearRelated(string $browserName): void
    {
        RelatedItem::where([
            'browser_name' => $browserName,
            'subject_id' => $this->getKey(),
            'subject_type' => $this->getMorphClass(),
        ])->delete();
    }

    public function clearAllRelated(): void
    {
        $this->relatedItems()->delete();
    }

    public function setRelatedCache($browser, $items): void
    {
        $this->relatedCache[$browser] = $items;
    }
}
