<?php

namespace Sb4yd3e\Twill\Models\Behaviors;

use Sb4yd3e\Twill\Models\RelatedItem;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasRelated
{
    protected $relatedCache;

    public function relatedItems()
    {
        return $this->morphMany(RelatedItem::class, 'subject')->orderBy('position');
    }

    public function getRelated($browser_name)
    {
        if ($this->relatedCache[$browser_name] === null) {
            $this->loadRelated($browser_name);
        }

        return $this->relatedCache[$browser_name];
    }

    public function loadRelated($browser_name)
    {
        $this->load('relatedItems');

        return $this->relatedCache[$browser_name] = $this->relatedItems
            ->where('browser_name', $browser_name)
            ->map(function ($item) {
                return $item->related;
            });
    }

    public function sync($items, $browser_name)
    {
        RelatedItem::where([
            'browser_name' => $browser_name,
            'subject_id' => $this->getKey(),
            'subject_type' => $this->getMorphClass(),
        ])->delete();

        $position = 1;

        collect($items)->map(function ($item) {
            return array_only($item, ['endpointType', 'id']);
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
}
