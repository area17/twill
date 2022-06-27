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
     * @param string $browser_name
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRelated($browser_name)
    {
        if (!isset($this->relatedCache[$browser_name]) || $this->relatedCache[$browser_name] === null) {
            $this->loadRelated($browser_name);
        }

        return $this->relatedCache[$browser_name];
    }

    /**
     * Eager load related items for a browser field.
     *
     * @param string $browser_name
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function loadRelated($browser_name)
    {
        if (!isset($this->relatedItems)) {
            $this->load('relatedItems');
        }

        return $this->relatedCache[$browser_name] = $this->relatedItems
            ->where('browser_name', $browser_name)
            ->map(function ($item) {
                if (filled($item->related)) {
                    /** @var \A17\Twill\Models\Model $model */
                    $model = $item->related;

                    $model->setRelation('pivot', $item);

                    return $model;
                }

                return null;
            });
    }

    /**
     * Attach items to the model for a browser field.
     *
     * @param array $items
     * @param string $browser_name
     * @return void
     */
    public function saveRelated($items, $browser_name)
    {
        $this->clearRelated($browser_name);

        $position = 1;

        Collection::make($items)->map(function ($item) {
            return Arr::only($item, ['endpointType', 'id']);
        })->each(function ($values) use ($browser_name, &$position) {
            RelatedItem::create([
                'subject_id' => $this->getKey(),
                'subject_type' => $this->getMorphClass(),
                'related_id' => $values['id'],
                'related_type' => $values['endpointType'],
                'browser_name' => $browser_name,
                'position' => $position,
            ]);
            $position++;
        });
    }

    public function clearRelated($browserName): void
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
}
