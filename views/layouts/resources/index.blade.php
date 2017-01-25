@php
    $create = $create ?? true;
    $search = $search ?? true;
    $sort = $sort ?? false;
    $publish = $publish ?? true;
    $edit = $edit ?? true;
    $delete = $delete ?? true;
@endphp

@extends('cms-toolkit::layouts.main')

@section('content')
    @if($search || !empty($filters))
        <div class="filter">
            <form method="GET" accept-charset="UTF-8" novalidate="novalidate" class="{{ $filtersOn ? 'on' : '' }}">
                @if ($search)
                    <input type="text" name="fSearch" placeholder="Search" autocomplete="off" size="20" value="{{ $fSearch or '' }}">
                @endif
                @foreach($filters as $filter)
                    @if (isset(${$filter.'List'}))
                        {!! Form::select($filter, ${$filter.'List'} , ${$filter} ?? null) !!}
                    @endif
                @endforeach
                @yield('extra_filters')
                <input type="submit" class="btn btn-small" value="Filter">
                @hasSection('clear_link')
                    @yield('clear_link')
                @else
                    <a href="{{ Request::url() }}">Clear</a>
                @endif
            </form>
        </div>
    @endunless

    <section class="box">
        <header class="header_small">
            <h3><b>@if (isset($title)) {{ (!$sort ? (method_exists($items, 'total') ? $items->total() : count($items))  : count($items)) . ' ' . $title }}@endif</b></h3>
        </header>
        <div class="table_container">
            @if ($sort && $currentUser->can('sort'))
                <table data-behavior="sortable" data-sortable-update-url="{{ moduleRoute($moduleName, $routePrefix, 'sort') }}">
            @else
                <table>
            @endif
                @if(count($items))
                    <thead>
                        <tr>
                            @if ($sort && $currentUser->can('sort'))
                                <th class="tool">—</th>
                            @endif
                            @if ($publish)
                                <th class="tool">{{ $publish_title or '—'}}</th>
                            @endif
                            @if (view()->exists('admin.' . $moduleName . '._before_index_headers'))
                                @include('admin.' . $moduleName . '._before_index_headers')
                            @endif
                            @foreach ($columns as $column)
                                <th class="{{ isset($column['col']) ? 'colw-' . $column['col'] : '' }}">
                                @if(isset($column['sort']) && $column['sort'])
                                    @resourceView($moduleName, 'sort_link')
                                @else
                                    {{ $column['title'] }}</th>
                                @endif
                            @endforeach
                            @resourceView($moduleName, 'after_index_headers')
                            @if ($edit && $currentUser->can('edit'))
                                <th class="tool">—</th>
                            @endif
                            @if ($delete && $currentUser->can('delete'))
                                <th class="tool">—</th>
                            @endif
                        </tr>
                    </thead>
                @endif
                <tbody>
                    @forelse ($items as $item)
                        <tr data-id="{{ $item->id }}" @if(isset($sortDisabledWhen) && $item->$sortDisabledWhen) class="sortable-inactive" @endif>
                            @if ($sort && $currentUser->can('sort'))
                                @resourceView($moduleName, 'sort_action')
                            @endif
                            @if ($publish)
                                @resourceView($moduleName, 'publish_action')
                            @endif
                            @if (view()->exists('admin.' . $moduleName . '._before_index_columns'))
                                @include('admin.' . $moduleName . '._before_index_columns')
                            @endif
                            @foreach ($columns as $column)
                                @php
                                    $columnOptions = $column;
                                @endphp
                                <td class="{{ isset($column['thumb']) && $column['thumb'] ? 'thumb' : '' }}">
                                    @if (isset($column['show_link']) && $column['show_link'] && $currentUser->can('list'))
                                        @resourceView($moduleName, 'column_with_show_link')
                                    @elseif (isset($column['edit_link']) && $column['edit_link'] && $currentUser->can('edit'))
                                        @resourceView($moduleName, 'column_with_edit_link')
                                    @elseif(isset($column['thumb']) && $column['thumb'])
                                        <img src="{{ $item->cmsImage(
                                                isset($column['variant']) ? $column['variant']['role'] : head(array_keys($item->mediasParams)),
                                                isset($column['variant']) ? $column['variant']['crop'] : head(array_keys(head($item->mediasParams))),
                                                isset($column['variant']) && isset($column['variant']['params']) ? $column['variant']['params'] : ['w' => 80, 'h' => 80, 'fit' => 'crop']
                                            ) }}" width="80" height="80">
                                    @else
                                        @resourceView($moduleName, 'column')
                                    @endif
                                </td>
                            @endforeach
                            @resourceView($moduleName, 'after_index_columns')
                            @if ($edit && $currentUser->can('edit'))
                                @resourceView($moduleName, 'edit_action')
                            @endif
                            @if ($delete && $currentUser->can('delete'))
                                @resourceView($moduleName, 'delete_action')
                            @endif
                        </tr>
                    @empty
                        <tr class="empty_table">
                            <td colspan="8">
                                <h2>No {{ (isset($title)) ? $title : $moduleName }}</h2>
                                @if ($create && $currentUser->can('edit'))
                                    @resourceView($moduleName, 'create_action')
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @unless ($sort || !method_exists($items, 'total'))
            @resourceView($moduleName, 'paginator')
        @endunless
    </section>
@stop

@section('footer')
    <footer id="footer">
    <ul>
        @if ($create && $currentUser->can('edit'))
            <li>
                @resourceView($moduleName, 'create_action')
            </li>
        @endif
    </ul>
    </footer>
@stop
