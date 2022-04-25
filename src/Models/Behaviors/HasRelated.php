<?php

namespace A17\Twill\Models\Behaviors;

use A17\Twill\Models\RelatedItem;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HasRelated
{
    protected $relatedCache;

    /**
     * Defines the one-to-many relationship for related items.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function relatedItems()
    {
        return $this->morphMany(RelatedItem::class, 'subject')->orderBy('position');
    }

    /**
     * Returns the related items for a browser field.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRelated(string $browser_name)
    {
        if (!isset($this->relatedCache[$browser_name]) || $this->relatedCache[$browser_name] === null) {
            $this->loadRelated($browser_name);
        }

        return $this->relatedCache[$browser_name];
    }

    /**
     * Eager load related items for a browser field.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function loadRelated(string $browser_name)
    {
        if (!(property_exists($this, 'relatedItems') && $this->relatedItems !== null)) {
            $this->load('relatedItems');
        }

        return $this->relatedCache[$browser_name] = $this->relatedItems
            ->where('browser_name', $browser_name)
            ->map(function ($item) {
                return $item->related;
            });
    }

    /**
     * Attach items to the model for a browser field.
     *
     * @return void
     * @param mixed[] $items
     */
    public function saveRelated(array $items, string $browser_name)
    {
        RelatedItem::where([
            'browser_name' => $browser_name,
            'subject_id' => $this->getKey(),
            'subject_type' => $this->getMorphClass(),
        ])->delete();

        $position = 1;

        Collection::make($items)->map(function ($item): array {
            return Arr::only($item, ['endpointType', 'id']);
        })->each(function ($values) use ($browser_name, &$position): void {
            RelatedItem::create([
                'subject_id' => $this->getKey(),
                'subject_type' => $this->getMorphClass(),
                'related_id' => $values['id'],
                'related_type' => $values['endpointType'],
                'browser_name' => $browser_name,
                'position' => $position,
            ]);
            ++$position;
        });
    }
}
