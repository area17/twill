<?php

namespace A17\Twill;

use A17\Twill\Models\Contracts\TwillLinkableModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Session;

/**
 * @todo: This could "pile up" over time. Maybe we can clear it when a new form is being built.
 */
class TwillUtil
{
    /**
     * @var string
     */
    private const SESSION_FIELD = 'twill_util';

    /**
     * @var string
     */
    private const REPEATER_ID_INDEX = 'repeater_ids';

    /**
     * @var string
     */
    private const BLOCK_ID_INDEX = 'block_ids';

    public function hasRepeaterIdFor(int $frontEndId): ?int
    {
        return $this->getFromTempStore(self::REPEATER_ID_INDEX, $frontEndId);
    }

    public function registerRepeaterId(int $frontEndId, int $dbId): self
    {
        $this->pushToTempStore(self::REPEATER_ID_INDEX, $frontEndId, $dbId);

        return $this;
    }

    public function hasBlockIdFor(int $frontEndId): ?int
    {
        return $this->getFromTempStore(self::BLOCK_ID_INDEX, $frontEndId);
    }

    public function registerBlockId(int $frontEndId, int $dbId): self
    {
        $this->pushToTempStore(self::BLOCK_ID_INDEX, $frontEndId, $dbId);

        return $this;
    }

    public function clearTempStore(): void
    {
        Session::remove(self::SESSION_FIELD);
    }

    public function parseInternalLinks(string $content): string
    {
        return preg_replace_callback(
            '/(#twillInternalLink::(.*?)#(\d+))/',
            function (array $data) {
                if (isset($data[2], $data[3])) {
                    $modelClass = $data[2];
                    $id = $data[3];

                    if (array_key_exists($modelClass, Relation::morphMap())) {
                        $modelClass = Relation::morphMap()[$modelClass];
                    }

                    $model = $modelClass::published()->where('id', $id)->first();

                    if (!$model) {
                        return url('404');
                    }

                    if ($model instanceof TwillLinkableModel) {
                        return $model->getFullUrl();
                    }

                    return url($model->slug);
                }
            },
            $content
        );
    }

    private function getFromTempStore(string $key, int $frontendId): ?int
    {
        $data = Session::get(self::SESSION_FIELD, []);

        return $data[$key][$frontendId] ?? null;
    }

    private function pushToTempStore(string $key, int $frontendId, int $dbId): void
    {
        $sessionData = Session::get(self::SESSION_FIELD, []);

        $keyData = $sessionData[$key] ?? [];
        $keyData[$frontendId] = $dbId;

        $sessionData[$key] = $keyData;

        Session::put(self::SESSION_FIELD, $sessionData);
    }

    public function syncUsingPrimaryKey(BelongsToMany $relation, $ids, $detaching = true): array
    {
        return (function ($ids, $detaching = true) {
            $changes = [
                'attached' => [], 'detached' => [], 'updated' => [],
            ];

            // First we need to attach any of the associated models that are not currently
            // in this joining table. We'll spin through the given IDs, checking to see
            // if they exist in the array of current ones, and if not we will insert.
            $current = $this->getCurrentlyAttachedPivots()
                ->pluck('id')->all();

            $records = $this->formatRecordsList($this->parseIds($ids));

            // Next, we will take the differences of the currents and given IDs and detach
            // all of the entities that exist in the "current" array but are not in the
            // array of the new IDs given to the method which will complete the sync.
            if ($detaching) {
                $detach = array_diff($current, array_keys($records));

                if (count($detach) > 0) {
                    $this->newPivotQuery()->whereIn('id', $detach)->delete();

                    $changes['detached'] = $this->castKeys($detach);
                }
            }

            // Now we are finally ready to attach the new records. Note that we'll disable
            // touching until after the entire operation is complete so we don't fire a
            // ton of touch operations until we are totally done syncing the records.
            $changes = array_merge(
                $changes,
                $this->attachNew($records, $current, false)
            );

            // Once we have finished attaching or detaching the records, we will see if we
            // have done any attaching or detaching, and if we have we will touch these
            // relationships if they are configured to touch on any database updates.
            if (
                count($changes['attached']) ||
                count($changes['updated']) ||
                count($changes['detached'])
            ) {
                $this->touchIfTouching();
            }

            return $changes;
        })->call($relation, $ids, $detaching);
    }
}
