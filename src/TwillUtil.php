<?php

namespace A17\Twill;

use Illuminate\Support\Facades\Session;

/**
 * @todo: This could "pile up" over time. Maybe we can clear it when a new form is being built.
 */
class TwillUtil
{
    private const SESSION_FIELD = 'twill_util';
    private const REPEATER_ID_INDEX = 'repeater_ids';

    public function hasRepeaterIdFor(int $frontEndId): ?int {
        return $this->getFromTempStore(self::REPEATER_ID_INDEX, $frontEndId);
    }

    public function registerRepeaterId(int $frontEndId, int $dbId): self
    {
        $this->pushToTempStore(self::REPEATER_ID_INDEX, $frontEndId, $dbId);

        return $this;
    }

    private function getFromTempStore(string $key, int $frontendId): ?int {
        $data = Session::get(self::SESSION_FIELD . '.' . $key, []);

        return $data[$frontendId] ?? null;
    }

    private function pushToTempStore(string $key, int $frontendId, int $dbId): void
    {
        $sessionData = Session::get(self::SESSION_FIELD . '.' . $key, []);

        $keyData = $sessionData[$key] ?? [];
        $keyData[$frontendId] = $dbId;

        $sessionData[$key] = $keyData;

        Session::put(self::SESSION_FIELD, $sessionData);
    }
}
