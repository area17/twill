<div class="flex flex-col" x-data="{open: {{($open ?? false) ? 'true' : 'false'}} }">
    <div
        class="flex h-12 cursor-pointer items-center justify-between px-6 text-gray-600 bg-gray-200 @if($nested ?? false) bg-blue-50 @endif"
        x-on:click="open =! open">
        {{$title}}
        <div class="text-sm">
            <span x-show="open">close</span>
            <span x-show="!open">open</span>
        </div>
    </div>
    {{-- Border will be fixed by removing old css. --}}
    <div class="bg-white p-6 pt-0 border border-gray-200" x-show="open" x-transition>
        {{$slot}}
    </div>
</div>
