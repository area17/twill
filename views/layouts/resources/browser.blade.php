@extends('cms-toolkit::layouts.modal')

@php
    $search = $search ?? true;
    $filtering = $filtering ?? true;
@endphp

@section('content')
    <div class="grid-frame">
        <div class="grid grid_full" data-behavior="select_medias_modal_simple">
            @if($search || $filtering)
                <div class="grid_header">
                    <div class="filter">
                        <form method="GET" accept-charset="UTF-8" novalidate="novalidate">
                            @if ($search)
                                <input type="text" name="fSearch" placeholder="Search" autocomplete="off" size="20" value="{{ $fSearch or '' }}">
                            @endif
                            @if ($filtering)
                                @foreach($filters as $filter)
                                    @if (isset(${$filter.'List'}))
                                        {!! Form::select($filter, ${$filter.'List'} , ${$filter} ?? null) !!}
                                    @endif
                                @endforeach
                            @endif
                            @yield('extra_filters')
                            <input type="submit" class="btn btn-small" value="Filter">
                            @hasSection('clear_link')
                                @yield('clear_link')
                            @else
                                <a href="{{ Request::url() }}">Clear</a>
                            @endif
                        </form>
                    </div>
                </div>
            @endif
            <div class="grid_list grid_list_rows" data-behavior="media_paginator" data-paginator-current="{{ $items->currentPage() }}" data-paginator-total="{{ $items->lastPage() }}" data-paginator-url="{{ Request::url() }}">
                <div class="grid_list_rows_table" data-feed @if(count($items) === 0) style="height: 100%" @endif>
                    @resourceView($moduleName, 'browser_list')
                </div>
            </div>
            <footer class="grid_footer">
                @if(count($items))
                    <button class="btn btn-primary" data-media-insert="" type="button">Insert</button>
                @endif
                <button class="btn" data-behavior="close_parent_modal" type="button">Cancel</button>
            </footer>
        </div>
    </div>
@endsection
