<li class="py-4">
    <div class="flex items-center space-x-4">
        @if ($image ?? false)
            <div class="flex-shrink-0">
                {{$image}}
            </div>
        @endif
        <div class="flex-1 min-w-0">
            @if ($title ?? false)
                <p class="text-sm font-medium text-gray-900 truncate">{{$title}}</p>
            @endif
            @if ($description ?? false)
                <p class="text-sm text-gray-500 truncate">{{$description}}</p>
            @endif
        </div>
        <div>
            @if ($link ?? false)
                <a href="{{$link}}"
                   class="inline-flex items-center shadow-sm px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50"> {{$linkText ?? 'view'}} </a>
            @endif
            @if ($onDelete ?? false)
                <button wire:click="{{ $onDelete }}"
                        class="inline-flex items-center shadow-sm px-2.5 py-0.5 border border-gray-300 text-sm leading-5 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50"> @lang('Delete') </button>
            @endif
        </div>
    </div>
</li>
