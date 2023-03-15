@props(['type' => 'tip', 'title' => null])
@if ($type === 'tip')
    <div class="bg-tip p-30 rounded-[4px] mt-30">
        @if($title != null)
            <h4 class="text-tip f-h4 font-bold !mt-0 uppercase">{{$title}}</h4>
        @else
            <h4 class="text-tip f-h4 font-bold !mt-0 uppercase">Tip</h4>
        @endif
        <div class="ml-3">
            {{$slot}}
        </div>
    </div>
@elseif($type === 'warning')
    <div class="bg-warn p-30 rounded-[4px] mt-30">

        @if($title != null)
            <h4 class="text-warn f-h4 font-bold !mt-0 uppercase">{{$title}}</h4>
        @else
            <h4 class="text-warn f-h4 font-bold !mt-0 uppercase">Warning</h4>
        @endif
        <div class="ml-3 text-sm font-medium">
            {{$slot}}
        </div>

    </div>
@elseif($type === 'info')
    <div class="rounded-md bg-blue-50 p-4">
        <div class="flex">
            <div class="ml-3">
                {{$slot}}
            </div>
        </div>
    </div>
@elseif($type === 'danger')
    <div class="bg-danger p-30 rounded-[4px] mt-30">
        @if($title != null)
            <h4 class="text-danger f-h4 font-bold !mt-0 uppercase">{{$title}}</h4>
        @else
            <h4 class="text-danger f-h4 font-bold !mt-0 uppercase">Danger</h4>
        @endif
        <div class="ml-3 text-sm font-medium">
            {{$slot}}
        </div>
    </div>
@endif
