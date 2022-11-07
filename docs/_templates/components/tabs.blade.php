<div class="flex flex-col" x-data="{ activeTab: '{{$currenttab ?? ''}}' }">
    <div class="inline-flex rounded-lg my-3 bg-opacity-30">
        @foreach ($items ?? [] as $item)
            <button @click="activeTab = '{{$item}}'"
                    :class="{
                        'rounded-r-lg': {{$loop->last ? 'true' : 'false'}},
                        'rounded-l-lg': {{$loop->first ? 'true' : 'false'}},
                        'border-grey-500 bg-gray-500 !text-white': activeTab === '{{$item}}'
                        }"
                    class="py-[10px] sm:py-2 my-1 px-[12px] sm:px-6 inline-flex items-center justify-center font-medium border border-gray-50 text-center focus:bg-primary text-black text-sm sm:text-base capitalize bg-white"
            >
                {{$item}}
            </button>
        @endforeach
    </div>
    <div>
        {{$slot}}
    </div>
</div>
