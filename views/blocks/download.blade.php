<div class="BlockDownload">
    <div class="BlockDownload__inner grid-container">
        <div class="BlockDownload__content">
            <h4 class="BlockDownload__title">{{ $title }}</h4>
            <p class="BlockDownload__text">{{ $text }}</p>
            <a href="{{ $resource_url or '#' }}" class="BlockDownload__button" download >
                <span>{{ $label }}</span>
                <span class="BlockDownload__icon">{!! icon('download') !!}</span>
            </a>
        </div>
        <div class="BlockDownload__figure">
            @if (is_object($image))
            <img src="{{ ImageService::getUrl($image['uuid'], ['w' => 386, 'h' => 386] + $crop_params)}}" alt="{{ $image['alt_text'] }}" class="BlockDownload__img" />
            @else
            <img src="{!! ImageService::getFallbackUrl(['w' => 386, 'h' => 386]) !!}" class="BlockDownload__img" />
            @endif
        </div>
    </div>
</div>
