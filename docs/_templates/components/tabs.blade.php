<div class="flex flex-col mt-20" x-data="{ activeTab: '{{$currenttab ?? ''}}' }">
    <div class="inline-flex">
        @foreach ($items ?? [] as $item)
            <button @click="activeTab = '{{$item}}'"
                    :class="{
                        // 'rounded-r-lg': {{$loop->last ? 'true' : 'false'}},
                        // 'rounded-l-lg': {{$loop->first ? 'true' : 'false'}},
                        '!bg-tab-active !text-tab-active !border-tab-active pointer-events-none': activeTab === '{{$item}}'
                        }"
                    class="f-body text-tab bg-tab px-20 py-4 border-[2px] border-tab rounded mr-20 font-bold hover:bg-tab-hover">
                {{$item}}
            </button>
        @endforeach
    </div>
    <div>
        {{$slot}}
    </div>
</div>
