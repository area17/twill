<?php

namespace A17\Twill;

use A17\Twill\Enums\PermissionLevel;
use A17\Twill\Models\Enums\UserRole;
use A17\Twill\Models\Permission;
use A17\Twill\View\Components\Navigation\NavigationLink;
use Illuminate\Support\Facades\Auth;
use MyCLabs\Enum\Enum;

class TwillPermissions
{
    public string $roleEnum = UserRole::class;

    public function enabled(): bool
    {
        return config('twill.enabled.permissions-management');
    }

    /**
     * @return Enum
     */
    public function roles(): string
    {
        return $this->roleEnum;
    }

    /**
     * The role enumeration class. Must extend MyCLabs\Enum\Enum.
     * See A17\Twill\Models\Enums\UserRole for an example.
     */
    public function setRoleEnum(string $roleEnum): void
    {
        $this->roleEnum = $roleEnum;
    }

    /**
     * Return the module name if the module has permissions, otherwise return false.
     */
    public function getPermissionModule(string $moduleName): bool|string
    {
        $submodule = Permission::permissionableModules()->filter(function ($module) use ($moduleName) {
            return strpos($module, '.') && explode('.', $module)[1] === $moduleName;
        })->first();

        if (Permission::permissionableModules()->contains($moduleName)) {
            return $moduleName;
        }

        if ($submodule) {
            return $submodule;
        }

        return false;
    }

    public function levelIs(string $level): bool
    {
        if (!PermissionLevel::isValid($level)) {
            throw new \Exception('Invalid permission level. Check TwillPermissions for available levels');
        }

        return $this->enabled() && config('twill.permissions.level') === $level;
    }

    public function levelIsOneOf(array $levels): bool
    {
        foreach ($levels as $level) {
            if (!PermissionLevel::isValid($level)) {
                throw new \Exception('Invalid permission level. Check TwillPermissions for available levels');
            }
        }
        return $this->enabled() && in_array(config('twill.permissions.level'), $levels, true);
    }

    public function showUserSecondaryNavigation(): void
    {
        \A17\Twill\Facades\TwillNavigation::addSecondaryNavigationForCurrentRequest(
            NavigationLink::make()->title(twillTrans('twill::lang.user-management.users'))
                ->forModule('users')
                ->onlyWhen(fn() => Auth::user()->can('edit-users'))
        );

        \A17\Twill\Facades\TwillNavigation::addSecondaryNavigationForCurrentRequest(
            NavigationLink::make()->title(twillTrans('twill::lang.permissions.roles.title'))
                ->forModule('roles')
                ->onlyWhen(
                    fn() => config('twill.enabled.permissions-management') && Auth::user()->can('edit-user-roles')
                )
        );

        \A17\Twill\Facades\TwillNavigation::addSecondaryNavigationForCurrentRequest(
            NavigationLink::make()->title(twillTrans('twill::lang.permissions.groups.title'))
                ->forModule('groups')
                ->onlyWhen(
                    fn() => config('twill.enabled.permissions-management') && Auth::user()->can('edit-user-groups')
                )
        );
    }
}
