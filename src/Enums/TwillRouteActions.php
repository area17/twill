<?php

namespace A17\Twill\Enums;

/**
 * Currently just constants until we go php8.1 only
 */
class TwillRouteActions
{
    // PAGES
    public const INDEX = 'index';
    public const CREATE = 'create';
    public const EDIT = 'edit';
    public const PREVIEW = 'preview';

    // Actions
    public const UPDATE = 'update';
    public const STORE = 'store';

    public const RESTORE_REVISION = 'restoreRevision';

    public const DUPLICATE = 'duplicate';
    public const DESTROY = 'destroy';

}
