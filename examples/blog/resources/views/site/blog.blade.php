<x-layout :title="$item->title">
    <div class="max-w-7xl mx-auto mt-12">
        <h1 class="text-5xl text-center">{{$item->title}}</h1>
        <img src="{{ $item->image('cover', 'default') }}" />
        <div class="flex justify-center p-3 items-center">
            <div class="mr-2">Categories:</div>
            @forelse($item->getRelated('categories')->where('published', true) as $category)
                <div class="rounded-full bg-gray-100 px-5 py-1">
                    {{$category->title}}
                </div>
            @empty
                No categories
            @endforelse
        </div>
        <div class="mt-12">
            {!! $item->renderBlocks(false) !!}
        </div>
    </div>
</x-layout>
