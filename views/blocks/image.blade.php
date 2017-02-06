@php
    $image = array_first($images);
@endphp

<div class="BlockImage">
    @if (is_object($image))
        <picture class="BlockImage__figure">
            <source
                media="(max-width: 420px)"
                sizes="100vw"
                srcset="{{ ImageService::getUrl($image->uuid, ['w' => 320, 'h' => 200] + $crop_params) }} 320w,
                        {{ ImageService::getUrl($image->uuid, ['w' => 640, 'h' => 400] + $crop_params) }} 640w,
                        {{ ImageService::getUrl($image->uuid, ['w' => 960, 'h' => 600] + $crop_params) }} 960w">
            <source
                media="(min-width: 421px) and (max-width: 1023px)"
                sizes="100vw"
                srcset="{{ ImageService::getUrl($image->uuid, ['w' => 780, 'h' => 395] + $crop_params) }} 780w,
                        {{ ImageService::getUrl($image->uuid, ['w' => floor(780*1.5), 'h' => floor(395*1.5)] + $crop_params) }} {{floor(780*1.5)}}w,
                        {{ ImageService::getUrl($image->uuid, ['w' => floor(780*2), 'h' => floor(395*2)] + $crop_params) }} {{floor(780*2)}}w,
                        {{ ImageService::getUrl($image->uuid, ['w' => floor(780*2.5), 'h' => floor(395*2.5)] + $crop_params) }} {{floor(780*2.5)}}w">
            <img
                class="BlockImage__img"
                alt="{{ $image->alt_text }}"
                sizes="100vw"
                srcset="{{ ImageService::getUrl($image->uuid, ['w' => 1340, 'h' => 560] + $crop_params) }} 1340w,
                        {{ ImageService::getUrl($image->uuid, ['w' => floor(1340*1.5), 'h' => floor(560*1.5)] + $crop_params) }} {{floor(1340*1.5)}}w,
                        {{ ImageService::getUrl($image->uuid, ['w' => floor(1340*2), 'h' => floor(560*2)] + $crop_params) }} {{floor(1340*2)}}w,
                        {{ ImageService::getUrl($image->uuid, ['w' => floor(1340*2.5), 'h' => floor(560*2.5)] + $crop_params) }} {{floor(1340*2.5)}}w">
        </picture>
    @else
        <img class="BlockImage__img" src="{!! ImageService::getFallbackUrl(['w' => 960, 'h' => 600]) !!}" />
    @endif
</div>
