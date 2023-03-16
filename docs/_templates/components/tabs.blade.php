<div class="flex flex-col mt-20" x-data="{ activeTab: '{{$currenttab ?? ''}}' }">
    <div class="inline-flex">
        @foreach ($items ?? [] as $item)
            <button @click="activeTab = '{{$item}}'"
                    :class="{
                        // 'rounded-r-lg': {{$loop->last ? 'true' : 'false'}},
                        // 'rounded-l-lg': {{$loop->first ? 'true' : 'false'}},
                        '!bg-white !text-primary border-purple border-[2px] pointer-events-none': activeTab === '{{$item}}'
                        }"
                    class="f-body text-white bg-black px-20 py-4 rounded-[4px] mr-20 font-bold"
            >
                {{$item}}
            </button>
        @endforeach
    </div>
    <div>
        {{$slot}}
    </div>
</div>
