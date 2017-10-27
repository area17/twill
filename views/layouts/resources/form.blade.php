@extends('cms-toolkit::layouts.main')

@php
    $hide_lang_switcher = $hide_lang_switcher ?? true;
    $with_view_link = $with_view_link ?? false;
@endphp

@section('content')
    @formField('lang_switcher', ['hidden' => $hide_lang_switcher])
    @yield('form')
@stop

@section('footer')
    @can('edit')
        <ul>
            <li><input type="submit" name="continue" value="Save" class="btn btn-primary"></li>
            <li><input type="submit" name="finish" value="Save and close" class="btn"></li>
            <li><input type="submit" name="cancel" value="Cancel" class="btn"></li>
            {{-- <li><a href="{{ $back_link }}" class="btn">Cancel</a></li> --}}

            @if (isset($item) && $item->isNotLockedByCurrentUser())
            <li>
                <a class="btn btn-link" href="#">Edit Lock: {{ $item->lockedBy()->name }}</a>
            </li>
            @endif

            @if ($with_view_link ?? false)
                <li class="float-right"><a class="btn" target="_blank" href="{{ $item->url }}">Open on site &#8599;</a></li>
            @endif
            @if ($with_preview_link ?? false)
                <li class="float-right"><a class="btn" target="_blank" href="{{ $item->previewUrl }}">Open preview &#8599;</a></li>
                <li class="float-right"><a class="btn btn-copy-preview" data-clipboard-text="{{ $item->previewUrl }}">Copy preview link</a></li>
                <script src="/assets/admin/vendor/clipboard.min.js"></script>
                <script>
                    $( document ).ready(function() {
                        var clipboard = new Clipboard('.btn-copy-preview');
                        clipboard.on('success', function(e) {
                            $.event.trigger({
                                type: "notification_open",
                                message: "Preview link copied!"
                            });
                            e.clearSelection();
                        });
                    });
                </script>
            @endif
        </ul>
    @endcan
    {!! Form::close() !!}
@stop
