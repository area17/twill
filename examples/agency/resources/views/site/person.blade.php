@extends('site.layout.main')

@section('title')
    {{ $item->full_name }}
@endsection

@section('main')
    <div class="block tablet-v:grid tablet-v:grid-cols-2 tablet-v:gap-8 tablet-v:pt-6 tablet-h:relative">
        <div>
            <h1 class="text-[36px] tablet-h:text-[40px] laptop:text-[55px] font-medium">
                {{ $item->full_name }}
                <br>
                <span class="text-slate-500">{{ $item->office_name }}</span>
            </h1>

        </div>
        <div class="mt-3 tablet-h:mt-0 tablet-h:absolute right-0 tablet-h:w-1/2">
            {!! TwillImage::make($item, 'main')->crop('default')->render(); !!}
        </div>
    </div>
    <div class="mt-4 text tablet-v:pr-[15%] tablet-h:w-1/2 tablet-h:pr-[4%]">
        {!! $item->biography !!}
    </div>

    <div class="mt-12">
        <h3 class="mb-4">Videos</h3>

        <div class="block tablet-h:grid tablet-h:grid-cols-2 laptop:grid-cols-3 tablet-h:gap-8">
        @foreach($item->videos as $video)
            <article class="personVideo pt-3">
                <a href="{{ $video->video_url }}" class="tablet-v:grid tablet-v:grid-cols-2 tablet-v:gap-8">
                    <div>
                        {!! TwillImage::make($video, 'main')->crop('flexible')->render(); !!}
                    </div>

                    <div>
                        <h4 class="tablet-v:text-[22px]"><span class="">{{ $video->title }}</span></h4>
                        <p class="text-slate-500">{{ $video->date }}</p>
                    </div>
                </a>
            </article>
        @endforeach
    </div>
    </div>
@endsection
