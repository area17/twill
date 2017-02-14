@php
    $image_left = array_first($images_left);
    $image_right = array_first($images_right);
@endphp

<div class="BlockImageGrid">
    <figure class="BlockImageGrid__figure">
        @if (is_object($image_left))
            <img src="{{ ImageService::getUrl($image_left->uuid, ['w' => 400, 'h' => 400] + $crop_params_left) }}" alt="{{ $image_left->alt_text }}" class="BlockImageGrid__img">
        @else
            <img src="{!! ImageService::getTransparentFallbackUrl(['w' => 400, 'h' => 400]) !!}" class="BlockImageGrid__img">
        @endif
    </figure>

    <figure class="BlockImageGrid__figure">
        @if (is_object($image_right))
            <img src="{{ ImageService::getUrl($image_right->uuid, ['w' => 400, 'h' => 400] + $crop_params_right) }}" alt="{{ $image_right->alt_text }}" class="BlockImageGrid__img">
        @else
            <img src="{{ ImageService::getTransparentFallbackUrl(['w' => 400, 'h' => 800]) }}" class="BlockImageGrid__img">
        @endif
    </figure>
</div>
