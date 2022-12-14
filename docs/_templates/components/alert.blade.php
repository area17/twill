@props(['type' => 'tip', 'title' => null])
@if ($type === 'tip')
    <div class="bg-purple-light p-30 rounded-[4px] mt-30">
        @if($title != null)
            <h4 class="text-purple f-h4 font-bold mt-0">{{$title}}</h4>
        @else
            <h4 class="text-purple f-h4 font-bold mt-0">Tip</h4>
        @endif
        <div class="ml-3">
            <p class="text-sm font-medium text-green-800">{{$slot}}</p>
        </div>
    </div>
@elseif($type === 'warning')
    <div class="bg-yellow-light p-30 rounded-[4px] mt-30">

        @if($title != null)
            <h4 class="text-yellow f-h4 font-bold mt-0">{{$title}}</h4>
        @else
            <h4 class="text-yellow f-h4 font-bold mt-0">Warning</h4>
        @endif
        <div class="ml-3">
            <p class="text-sm font-medium text-yellow-800">{{$slot}}</p>
        </div>

    </div>
@elseif($type === 'info')
    <div class="rounded-md bg-blue-50 p-4">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm font-medium text-blue-800">{{$slot}}</p>
            </div>
        </div>
    </div>
@elseif($type === 'danger')
    <div class="bg-red-light p-30 rounded-[4px] mt-30">
        @if($title != null)
            <h4 class="text-red f-h4 font-bold mt-0">{{$title}}</h4>
        @else
            <h4 class="text-red f-h4 font-bold mt-0">Danger</h4>
        @endif
        <div class="ml-3">
            <p class="text-sm font-medium text-red-800">{{$slot}}</p>
        </div>

    </div>
@endif
