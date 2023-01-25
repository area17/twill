<?php

namespace A17\Twill;

use A17\Twill\Models\Contracts\TwillLinkableModel;
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
            '/(#twillInternalLink::(.*)#(\d))/',
            function (array $data) {
                if (isset($data[2], $data[3])) {
                    $modelClass = $data[2];
                    $id = $data[3];

                    $model = $modelClass::published()->where('id', $id)->first();
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
}
