@php
    $searchable = $searchable ?? false;
    $itemsInSelectsTables = $itemsInSelectsTables ?? false;
    $listUser = $listUser ?? false;

    $options = method_exists($options, 'map') ? $options->map(function($label, $value) {
        return [
            'value' => $value,
            'label' => $label
        ];
    })->values()->toArray() : $options;
@endphp
<a17-singleselect-permissions
    @if ($searchable) :searchable="true" @endif
    @if ($listUser) :list-user="true" @endif
>
    @foreach ($itemsInSelectsTables as $itemInSelectsTables)
        <div class="multiselectorPermissions__item" data-singleselect-permissions-filterable="{{ $itemInSelectsTables[$labelKey ?? 'title'] }}">
            @php $name = str_replace("%id%", $itemInSelectsTables->id, $namePattern); @endphp
            <a17-singleselect
                @if ($listUser) thumbnail="{{ $itemInSelectsTables->cmsImage('profile', 'default', ['w' => 100, 'h' => 100]) }}" @endif
                label="{{ $itemInSelectsTables[$labelKey ?? 'title'] }}"
                @include('twill::partials.form.utils._field_name')
                :options='{{ json_encode($options) }}'
                :in-table="true"
                :inline="true"
                :grid="false"
                :has-default-store="true"
                in-store="value"
            >
            </a17-singleselect>

        </div>
    @endforeach
</a17-singleselect-permissions>
