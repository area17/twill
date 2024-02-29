@extends('site.layout.main')

@section('title')
    {{ app(A17\Twill\Repositories\SettingRepository::class)->byKey('about_title') }}
@endsection

@section('main')
    <div class="block tablet-h:grid tablet-h:grid-cols-2 tablet-h:gap-8">
        <div>
            <h2 class="text-[36px] tablet-h:text-[40px] laptop:text-[55px] font-medium">{{ $item->tagline }}</h2>
        </div>
        <div class="text mt-10 tablet-h:mt-0">
            {!! $item->text !!}
        </div>
    </div>

    <div class="block tablet-v:grid tablet-v:grid-cols-2 laptop:grid-cols-3 tablet-v:gap-8 mt-12">
        @php
            $people = \App\Models\Person::with(['office', 'slugs', 'medias'])->get();
        @endphp
        @foreach($people as $person)
            <article>
                <a href="{{ route('twill.about.people.show', ['person' => $person->slug]) }}" class="">
                    {!! TwillImage::make($person, 'main')->crop('default')->render(); !!}
                    <h4 class="mt-3"><span class="">{{ $person->full_name }}<span></span></span></h4>
                    <p class="text-slate-500">{{ $person->office_name }}</p>
                </a>
            </article>
        @endforeach
    </div>
@endsection
