@props(['type' => 'tip', 'title' => null])
@if ($type === 'tip')
    <div class="alert bg-tip p-30 rounded mt-30">
        @if($title != null)
            <h4 class="text-tip f-h4 font-bold !mt-0 uppercase">{{$title}}</h4>
        @else
            <h4 class="text-tip f-h4 font-bold !mt-0 uppercase">Tip</h4>
        @endif
        <div class="markdown ml-3">
            {{$slot}}
        </div>
    </div>
@elseif($type === 'warning')
    <div class="alert bg-warn p-30 rounded mt-30">

        @if($title != null)
            <h4 class="text-warn f-h4 font-bold !mt-0 uppercase">{{$title}}</h4>
        @else
            <h4 class="text-warn f-h4 font-bold !mt-0 uppercase">Warning</h4>
        @endif
        <div class="markdown ml-3">
            {{$slot}}
        </div>

    </div>
@elseif($type === 'info')
    <div class="alert bg-info p-30 rounded mt-30">
    {{-- <div class="rounded-md bg-blue-50 p-4"> --}}
        @if($title != null)
            <h4 class="text-info f-h4 font-bold !mt-0 uppercase">{{$title}}</h4>
        @else
            <h4 class="text-info f-h4 font-bold !mt-0 uppercase">Note</h4>
        @endif
        <div class="markdown ml-3">
            {{$slot}}
        </div>
    </div>
@elseif($type === 'danger')
    <div class="alert bg-danger p-30 rounded mt-30">
        @if($title != null)
            <h4 class="text-danger f-h4 font-bold !mt-0 uppercase">{{$title}}</h4>
        @else
            <h4 class="text-danger f-h4 font-bold !mt-0 uppercase">Danger</h4>
        @endif
        <div class="markdown ml-3">
            {{$slot}}
        </div>
    </div>
@endif
