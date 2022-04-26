<div class="m-4">
    <h1>
        {{$title}}
    </h1>
    <div class="flex gap-2 flex-col">
        @foreach($options as $option)
            <div class="p-4 hover:bg-gray-100 cursor-pointer flex justify-between @if($this->isSelected($option)) bg-green-100 @endif"
                 wire:click="toggle({{json_encode($option)}})"
            >
                <div>
                    {{$option['name']}}
                </div>
                <div>
                    {{$option['endpointType']}}
                </div>
                <div>
                    @unless ($this->isSelected($option))
                        @lang('select')
                    @else
                        @lang('deselect')
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if ($max > 0)
    <div>
        Selected {{count($selected)}}/{{$max}}
    </div>
    @endif

    {!! $paginator !!}

    <div>
        <button wire:click="submitBrowserData">@lang('Save selection')</button>
    </div>
</div>
