@php
    $isAfterUpload = request('new_uploads_ids');
@endphp

@extends('cms-toolkit::layouts.modal')

@section('content')
<div class="grid-frame">
    <div class="grid" data-behavior="select_medias_modal" data-url-get-multiple="{{ route('admin.file-library.files.bulk-edit') }}">
        @unless ($isAfterUpload)
            <div class="grid_header">
                <div class="filter" style="float: right;">
                    <form action="#" method="get">
                        <label>Filter by</label>
                        {{ Form::select('fTag', $fTagList , $fTag ?? null) }}
                        <input type="text" name="fSearch" placeholder="Search" value="{{ $fSearch or '' }}" />
                        <input class="btn btn-small" type="submit" value="Search"/>
                        <a href="{{ Request::url() }}">Clear</a>
                    </form>
                </div>
            </div>
        @endunless
        <div class="grid_list grid_list_rows" @unless($isAfterUpload) data-paginator-current="{{ $items->currentPage() }}" data-paginator-total="{{ $items->lastPage() }}" data-behavior="media_paginator" data-paginator-url="{{ Request::fullUrl() }}" @endunless>
            @unless ($isAfterUpload || !$currentUser->can('edit'))
                @include('cms-toolkit::medias.uploader')
            @endunless
            <div class="grid_list_rows_table" data-feed>
                @include('cms-toolkit::files.list')
            </div>
        </div>

        <form data-form class="grid_sidebar simple_form blank" method="PUT" data-selected-ids="[{{ request('new_uploads_ids') }}]" action="{{ route('admin.file-library.files.single-update') }}" data-action-multiple="{{ route('admin.file-library.files.bulk-update') }}" data-behavior="update_medias navigate_away">
            <span class="hint">No file selected</span>
            <div data-form-container></div>
        </form>
        <footer class="grid_footer">
            @if ($isAfterUpload)
                <a href="{{ route('admin.file-library.files.index', Input::except('new_uploads_ids')) }}" class="btn btn-primary" data-media-save-and-redirect >Save</a>
                <a href="{{ route('admin.file-library.files.index', Input::except('new_uploads_ids')) }}" class="btn">Skip</a>
            @else
                <button type="button" class="btn btn-primary" data-media-insert >Add file</button>
                <button type="button" class="btn" data-behavior="close_parent_modal">Close</button>
            @endif
            <span class="btn"><span data-media-nb-items >0</span> file(s) selected
                <a href="#" data-media-clear >Clear selection</a>
            </span>
        </footer>
    </div>
</div>
@stop
