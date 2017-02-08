<div class="BlockDiaporama">
    <h2 class="BlockDiaporama__title">{{ $title }}</h2>
    @foreach($images as $image)
        @if(is_object($image))
            <figure class="BlockDiaporama__figure">
                <img src="{{ ImageService::getUrl($image->uuid, ['w' => 400, 'h' => 400]) }}" alt="{{ $image->alt_text }}" class="BlockImageGrid__img">
            </figure>
        @endif
    @endforeach
</div>
