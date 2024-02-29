@extends('site.layout.main')

@push('scripts')
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.min.js"></script>

    <script>
        $(document).ready(function(){
            $('.carousel').slick({
                autoplay: true
            });
        });
    </script>
@endpush

@push('extra_css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick-theme.css"/>
@endpush

@section('title')
    {{ app(A17\Twill\Repositories\SettingRepository::class)->byKey('contact_title') }}
@endsection

@section('main')
    <h1 class="text-[36px] tablet-h:text-[40px] laptop:text-[55px] font-bold pt-6">Contact</h1>

    @php
        $offices = \App\Models\Office::all();
    @endphp


    <div class="offices pt-6 tablet-v:grid tablet-v:grid-cols-2 tablet-v:gap-8">
    @foreach($offices as $office)
            @php
                if ($office->id === $item->id) {
                    $office = $item;
                }
                $galleryImages = $office->imageObjects('cover', 'default')->map(function ($media) use ($office) {
                    return TwillImage::make($office, 'cover', $media)->crop('default');
                })->toArray();

                $time = \Carbon\Carbon::now()->setTimezone($office->timezone);
            @endphp

        <div class="office mb-6">
            <div class="carousel">
                @if($galleryImages)
                    @foreach($galleryImages as $image)
                        {!! TwillImage::render($image) !!}
                    @endforeach
                @endif
            </div>
            <h3 class="font-semibold mt-4 mb-3 text-lg tablet-v:text-xl tablet-h:text-2xl laptop:text-3xl">
                {{ $office->title }} <br>
                <span>
                    <span>{{ $time->hour }}</span><span class="time__separator">:</span><span>{{ str_pad($time->minute, 2, '0', STR_PAD_LEFT) }}</span>
                </span>

            </h3>
            <div class="laptop:grid laptop:grid-cols-2 laptop:gap-8">
                <div>
                    <p class="mb-4">
                        <a href="mailto:{{ $office->email }}">{{ $office->email }}</a>
                        <br>
                        {{ $office->phone }}
                    </p>
                    <p class="mb-4">
                        {{ $office->street }} <br>
                        {{ $office->city }} {{ $office->zipcode }} <br>
                        {{ $office->country }} <br>
                        <a class="underline" href="{{ $office->directions }}">Get directions</a>
                    </p>
                </div>
                <div>
                    {{ $office->description }}
                </div>
            </div>

        </div>
    @endforeach
    </div>
@endsection
