@php
    $searchable = $searchable ?? false;
    $itemsInSelectsTables = $itemsInSelectsTables ?? false;
    $listUser = $listUser ?? false;
@endphp
<a17-singleselect-permissions
    @if ($searchable) :searchable="true" @endif
    @if ($listUser) :list-user="true" @endif
>
    @foreach ($itemsInSelectsTables as $itemInSelectsTables)
        <div class="multiselectorPermissions__item" data-singleselect-permissions-filterable="{{ $itemInSelectsTables[$labelKey ?? 'title'] }}">
            @formField('select', [
                'name' => str_replace("%id%", $itemInSelectsTables->id, $name),
                'label' => $itemInSelectsTables[$labelKey ?? 'title'],
                'unpack' => true,
                'inTable' => true,
                'inGrid' => false,
                'options' => $options
            ])
        </div>
    @endforeach
</a17-singleselect-permissions>
