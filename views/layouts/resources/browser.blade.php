@extends('cms-toolkit::layouts.modal')

@section('content')
    <div class="grid-frame">
        <div class="grid grid_full" data-behavior="select_medias_modal_simple">
            <div class="grid_header">
                <div class="filter">
                    <form action="#" method="get">
                        <input name="fSearch" placeholder="Search" type="text"/>
                        <input class="btn btn-small" type="submit" value="Search"/>
                    </form>
                </div>
            </div>
            <div class="grid_list grid_list_rows" data-behavior="media_paginator" data-paginator-current="{{ $items->currentPage() }}" data-paginator-total="{{ $items->lastPage() }}" data-paginator-url="{{ Request::url() }}">
                <div class="grid_list_rows_table" data-feed>
                    @include('cms-toolkit::layouts.resources.browser_list')
                </div>
            </div>
            <footer class="grid_footer">
                <button class="btn btn-primary" data-media-insert="" type="button">Insert</button>
                <button class="btn" data-behavior="close_parent_modal" type="button">Cancel</button>
            </footer>
        </div>
    </div>
@stop
