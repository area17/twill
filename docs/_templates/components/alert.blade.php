@props(['type' => 'success'])
<div class="my-4">
@if ($type === 'success')
    <div class="rounded-md bg-green-50 p-4">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{$slot}}</p>
            </div>
        </div>
    </div>
@elseif($type === 'warning')
    <div class="rounded-md bg-yellow-50 p-4">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm font-medium text-yellow-800">{{$slot}}</p>
            </div>
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
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{$slot}}</p>
            </div>
        </div>
    </div>
@endif
</div>
