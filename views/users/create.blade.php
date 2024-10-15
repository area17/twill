<x-twill::input
    name="name"
    :label="twillTrans('twill::lang.user-management.name')"
    :maxlength="70"
/>

@unless($item ?? null)
    <x-twill::input
        name="email"
        :label="twillTrans('twill::lang.user-management.email')"
        type="email"
    />

    @php $userModel = twillModel('user') @endphp

    <x-twill::select
        :name="$userModel::getRoleColumnName()"
        :label="twillTrans('twill::lang.user-management.role')"
        :native="true"
        :options="$roleList ?? []"
        :default="$roleList[0]['value'] ?? ''"
        placeholder="Select a role"
    />
@endunless
