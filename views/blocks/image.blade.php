@php
    $image = array_first($images);
@endphp

<div class="BlockImage">
    @if (is_object($image))
        <img class="BlockImage__img" src="{!! ImageService::getUrl($image->uuid, ['w' => 320, 'h' => 200] + $crop_params) !!}" alt="{{ $image->alt_text }}"  />
    @else
        <img class="BlockImage__img" src="{!! ImageService::getTransparentFallbackUrl(['w' => 320, 'h' => 200]) !!}" />
    @endif
</div>
