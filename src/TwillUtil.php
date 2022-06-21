<?php

namespace A17\Twill;

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

    public function hasRepeaterIdFor(int $frontEndId): ?int {
        return $this->getFromTempStore(self::REPEATER_ID_INDEX, $frontEndId);
    }

    public function registerRepeaterId(int $frontEndId, int $dbId): self
    {
        $this->pushToTempStore(self::REPEATER_ID_INDEX, $frontEndId, $dbId);

        return $this;
    }

    public function hasBlockIdFor(int $frontEndId): ?int {
        return $this->getFromTempStore(self::BLOCK_ID_INDEX, $frontEndId);
    }

    public function registerBlockId(int $frontEndId, int $dbId): self
    {
        $this->pushToTempStore(self::BLOCK_ID_INDEX, $frontEndId, $dbId);

        return $this;
    }

    private function getFromTempStore(string $key, int $frontendId): ?int {
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
}
