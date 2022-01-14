<x-layout :title="$blog->title">
    <div class="max-w-7xl mx-auto mt-12">
        <h1 class="text-5xl text-center">{{$blog->title}}</h1>
        <div class="flex justify-center p-3 items-center">
            <div class="mr-2">Categories:</div>
            @forelse($blog->getRelated('categories')->where('published', true) as $category)
                <div class="rounded-full bg-gray-100 px-5 py-1">
                    {{$category->title}}
                </div>
            @empty
                No categories
            @endforelse
        </div>
        <div class="mt-12">
            {!! $blog->renderBlocks(false) !!}
        </div>
    </div>
</x-layout>
