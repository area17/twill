<?php

namespace A17\Twill\Services\Cloud;

use Exception;

class Azure
{
    /**
     * @return never
     */
    public function filesystemFactory($prefix): void
    {
        throw new Exception('Azure is not implemented yey.');
    }
}
