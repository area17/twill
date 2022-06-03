<?php

namespace A17\Twill;

use A17\Twill\Enums\PermissionLevel;

class TwillPermissions
{
    public function levelIs(string $level): bool
    {
        if (!PermissionLevel::isValid($level)) {
            throw new \Exception('Invalid permission level. Check TwillPermissions for available levels');
        }

        return config('twill.enabled.permissions-management') && config('twill.permissions.level') === $level;
    }

    public function levelIsOneOf(array $levels): bool
    {
        foreach ($levels as $level) {
            if (!PermissionLevel::isValid($level)) {
                throw new \Exception('Invalid permission level. Check TwillPermissions for available levels');
            }
        }
        return in_array(config('twill.permissions.level'), $levels, true);
    }
}
