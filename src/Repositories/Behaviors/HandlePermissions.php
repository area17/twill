<?php

namespace A17\Twill\Repositories\Behaviors;

trait HandlePermissions
{
    protected function allUsersInGroupAuthorized($group, $object)
    {
        return $group->users()->count() > 0 && $group->users()->whereDoesntHave('permissions', function ($query) use ($object) {
            $query->where([
                ['permissionable_type', get_class($object)],
                ['permissionable_id', $object->id],
            ]);
        })->get()->count() === 0;
    }

    public function isPublicItemExists()
    {
        if ($this->model->isFillable('public')) {
            return $this->model->publishedInListings()->exists();
        }
        return false;
    }
}
