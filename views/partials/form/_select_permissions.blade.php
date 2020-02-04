@php
    $searchable = $searchable ?? false;
    $itemsInSelectsTables = $itemsInSelectsTables ?? false;
    $listUser = $listUser ?? false;

    $options = method_exists($options, 'map') ? $options->map(function($label, $value, $disabled) {
        return [
            'value' => $value,
            'label' => $label,
            'disabled' => $disabled ?? false
        ];
    })->values()->toArray() : $options;

    $isUserForm = get_class($item) === "A17\Twill\Models\User";
    if (get_class($item) === "A17\Twill\Models\Group") {
        $fctUpdatePermissionOptions = "updatePermissionGroupOptions";
    } else {
        $fctUpdatePermissionOptions = "updatePermissionOptions";
    }
@endphp
<a17-singleselect-permissions
    @if ($searchable) :searchable="true" @endif
    @if ($listUser) :list-user="true" @endif
>
    @foreach ($itemsInSelectsTables as $itemInSelectsTables)
        <div class="multiselectorPermissions__item" data-singleselect-permissions-filterable="{{ $itemInSelectsTables[$labelKey ?? 'title'] }}">
            @php
                $name = str_replace("%id%", $itemInSelectsTables->id, $namePattern);
                $default = $default ?? false;
            @endphp
            @if ($listUser)
                <a17-avatar
                    name="{{ $itemInSelectsTables[$labelKey ?? 'title'] }}"
                    thumbnail="{{ $itemInSelectsTables->cmsImage('profile', 'default', ['w' => 100, 'h' => 100]) }}"
                >
                </a17-avatar>
            @endif
            <label for="{{ $name }}">{{ $itemInSelectsTables[$labelKey ?? 'title'] }}</label>
            <a17-singleselect
                @include('twill::partials.form.utils._field_name')
                :options='{{ json_encode($fctUpdatePermissionOptions($options, $isUserForm ? $item : $itemInSelectsTables, $isUserForm ? $itemInSelectsTables : $item)) }}'
                @if ($default) selected="{{ $default }}" @endif
                :in-table="true"
                :inline="true"
                :grid="false"
                :has-default-store="true"
                in-store="value"
            >
            </a17-singleselect>
        </div>

        @unless((!isset($item->$name) && null == $formFieldsValue = getFormFieldsValue($form_fields, $name)))
            @push('vuexStore')
                @include('twill::partials.form.utils._selector_input_store')
            @endpush
        @endunless
    @endforeach
</a17-singleselect-permissions>
