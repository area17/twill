<x-layout title="Home">
    <div class="max-w-7xl mx-auto flex flex-col">
        @forelse($blogs as $blog)
            <div class="flex flex-col p-8">
                <a class="cursor-pointer" href="{{route('blog.detail', ['slug' => $blog->slug])}}">
                    <div class="text-3xl">{{$blog->title}}</div>
                    <div class="">{{$blog->description}}</div>
                    <div class="flex">
                        <div class="mr-2">Tags:</div>
                        @foreach ($blog->tags as $tag)
                            <div class="text-green-700">{{$tag->name}}</div>
                        @endforeach
                    </div>
                    <div class="text-gray-500">Created at: {{$blog->created_at->format('Y-m-d')}}</div>
                    <div class="text-blue-500">Read</div>
                </a>
            </div>
        @empty
            There are no blogs
        @endforelse
    </div>
</x-layout>
