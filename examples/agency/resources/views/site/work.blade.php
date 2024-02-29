@extends('site.layout.main')

@section('title')
    {{ $item->title }}
@endsection

@section('main')
        <div class="grid tablet-h:grid-cols-2">
            <div>
                <h1 class="text-[35px] font-semibold">{{$item->title}}</h1>
                <h3 class="text-[17px] text-slate-500">
                @foreach($item->disciplines as $id => $discipline)
                    @if($id === 0)
                        <span>{{ $discipline->title }}</span>
                    @else
                        <span>, {{ $discipline->title }}</span>
                    @endif
                @endforeach
                </h3>
            </div>
            <div class="mt-5 tablet-h:mt-0">
                <h2 class="text-[19px] font-medium tablet-v:pr-40">{{ $item->description }}</h2>
            </div>
        </div>

        <div class="mt-8">
            {!! TwillImage::make($item, 'cover')->crop('default')->render(); !!}
        </div>

        <div class="blockArea mt-5">
            {!! $item->renderBlocks() !!}
        </div>
@endsection
