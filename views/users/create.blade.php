<x-twill::input
    name="name"
    :label="__('twill::lang.user-management.name')"
    :maxlength="70"
/>

@unless($item ?? null)
    <x-twill::input
        name="email"
        :label="__('twill::lang.user-management.email')"
        type="email"
    />

    @can('edit-user-roles')
        @php $userModel = twillModel('user') @endphp

        <x-twill::select
            :name="$userModel::getRoleColumnName()"
            :label="__('twill::lang.user-management.role')"
            :native="true"
            :options="$roleList ?? []"
            :default="$roleList[0]['value'] ?? ''"
            placeholder="Select a role"
        />
    @endcan
@endunless
