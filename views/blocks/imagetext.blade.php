<article class="BlockImageText">
    <div class="BlockImageText__inner">
        @unless($image_first)
            <div class="BlockImageText__content">
                <h3 class="BlockImageText__title">{{ $title }}</h3>
                <p class="BlockImageText__text">{{ $text }}</p>
            </div>
        @endunless

        <div class="BlockImageText__img">
            @if (is_object($image))
                <img src="{{ ImageService::getUrl($image['uuid'], ['w' => 800, 'h' => 800] + $crop_params )}}" alt="{{ $image['alt_text'] }}" class="BlockDownload__img" />
            @else
                <img src="{!! ImageService::getFallbackUrl(['w' => 800, 'h' => 800]) !!}" class="BlockDownload__img" />
            @endif
        </div>

        @if($image_first)
            <div class="BlockImageText__content">
                <h3 class="BlockImageText__title">{{ $title }}</h3>
                <p class="BlockImageText__text">{{ $text }}</p>
            </div>
        @endif
    </div>
</article>
