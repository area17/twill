<?php

namespace A17\Twill\Enums;

class PermissionLevel
{
    public const LEVEL_ROLE = 'role';
    public const LEVEL_ROLE_GROUP = 'roleGroup';
    public const LEVEL_ROLE_GROUP_ITEM = 'roleGroupItem';

    public static function options(): array
    {
        return [
            self::LEVEL_ROLE,
            self::LEVEL_ROLE_GROUP,
            self::LEVEL_ROLE_GROUP_ITEM,
        ];
    }

    public static function isValid(string $level): bool
    {
        return in_array($level, self::options());
    }
}
