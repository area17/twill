@extends('cms-toolkit::layouts.modal')

@php
    $search = $search ?? true;
    $filtering = $filtering ?? true;
@endphp

@push('extra_js')
    <script>
        /* select_medias_modal_simple

          Select medias inside the media library – simplest behavior
          Use this behavior when you dont want to have the edit capabilities

          Actions :
          – Select one or multiple medias
          - Save the selected items and close the media library

        */

        a17cms.Behaviors.select_medias_modal_simple = function($grid) {

          var $modal = parent.a17cms.Helpers.modal.get_active();

          // Parent window vars
          if($modal.length == 0) {
            alert('The library need to be loaded into an iframe, via a modal window.');
            return false;
          }

          var modal_datas = $modal.data();
          var multiple_data = {};

          // Important : Role is mandatory
          // Identifies the role of the media in the entity ('featured', 'body', etc.)
          var role = modal_datas.role ? modal_datas.role : "";

          // Role is necessary
          if(role === "") {
            alert('Role is undefined : the media library is not plugged with the parent page!');
            return false;
          }

          var isSingleSelection = (modal_datas.singleSelection === undefined) ? false : modal_datas.singleSelection;


          $grid.on('click', 'a[data-id]', function(event) {
            event.preventDefault();

            var $bt = $(this);
            var bt_datas = $bt.data();
            var datas = { "data": [], "role": role };
            datas.data.push(bt_datas);

            if(isSingleSelection) {
              // if single insert, clicking triggers insert
              a17cms.Helpers.call_function_in_parent_window(modal_datas.callback, datas);
              parent.$.event.trigger({ type: "modal_close"});
            } else {
              // if multiple select, lets sort the data we want to send out
              // and toggle some selected state
              if (multiple_data[bt_datas.id]) {
                $bt.removeAttr("data-selected");
                delete multiple_data[bt_datas.id];
              } else {
                $bt.attr("data-selected","selected");
                multiple_data[bt_datas.id] = datas;
              }
            }
          });

          // if multiple select, send the content to the callback func on click of btn
          if(isSingleSelection) $('[data-media-insert]').hide();

          $('[data-media-insert]').on('click',function(event){
            event.preventDefault();
            a17cms.Helpers.call_function_in_parent_window(modal_datas.callback, multiple_data);
            multiple_data = {};
            parent.$.event.trigger({ type: "modal_close"});
          });
        }

    </script>
@endpush

@section('content')
    <div class="grid-frame">
        <div class="grid grid_full" data-behavior="select_medias_modal_simple">
            @if($search || $filtering)
                <div class="grid_header">
                    <div class="filter">
                        <form method="GET" accept-charset="UTF-8" novalidate="novalidate">
                            @if ($search)
                                <input type="text" name="fSearch" placeholder="Search" autocomplete="off" size="20" value="{{ $fSearch or '' }}">
                                @foreach(Input::except('fSearch') as $name => $value)
                                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                                @endforeach
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
                                <a href="{{ Request::fullUrlWithQuery(['fSearch' => null] + collect($filters)->mapWithKeys(function ($filter) {
                                    return [$filter => null];
                                })->toArray()) }}">Clear</a>
                            @endif
                        </form>
                    </div>
                </div>
            @endif
            <div class="grid_list grid_list_rows" data-behavior="media_paginator" data-paginator-current="{{ method_exists($items, 'total') ? $items->currentPage() : 1 }}" data-paginator-total="{{ method_exists($items, 'total') ? $items->lastPage() : 1 }}" data-paginator-url="{{ Request::url() }}">
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
