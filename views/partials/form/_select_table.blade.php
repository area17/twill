@php
    $searchable = $searchable ?? false;
    $itemsInSelectsTables = $itemsInSelectsTables ?? false;
@endphp
<a17-singleselect-table
    @if ($searchable) :required="true" @endif
>
    @foreach ($itemsInSelectsTables as $itemInSelectsTables)
        <div class="multiselectorTable__item" data-singleselect-table-filterable="{{ $itemInSelectsTables[$labelKey ?? 'title'] }}">
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
</a17-singleselect-table>
