<?php

namespace A17\Twill\View\Components\Fields;

use A17\Twill\Models\Group;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class SelectPermissions extends TwillFormComponent
{
    public bool $isUserForm;

    public string $fctUpdatePermissionOptions;

    public function __construct(
        public Collection $itemsInSelectsTables,
        public string $labelKey,
        public string $namePattern,
        public bool $listUser = false,
        public bool $searchable = false,
        public ?array $options = null,
    ) {
        parent::__construct(
            name: 'permissions',
            label: 'Permissions',
        );

        $this->options = $options ?? [
                [
                    'value' => '',
                    'label' => 'None',
                ],
                [
                    'value' => 'view-item',
                    'label' => 'View',
                ],
                [
                    'value' => 'edit-item',
                    'label' => 'Edit',
                ],
                [
                    'value' => 'manage-item',
                    'label' => 'Manage',
                ],
            ];

        $this->isUserForm = get_class($this->item) === twillModel('user');
        $this->fctUpdatePermissionOptions = $this->item instanceof Group ? "updatePermissionGroupOptions" : "updatePermissionOptions";
    }

    public function render(): View
    {
        return view('twill::partials.form._select_permissions', $this->data());
    }
}
