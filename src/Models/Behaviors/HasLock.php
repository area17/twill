<?php

namespace A17\CmsToolkit\Models\Behaviors;

use Sofa\ModelLocking\Locking;

trait HasLock
{
    use Locking;

    protected static function bootHasLock()
    {
    }

}
