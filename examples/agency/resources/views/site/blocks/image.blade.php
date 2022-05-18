<div class="py-5">
    @foreach($images as $image)
        <div>
            {!! TwillImage::render($image['image']) !!}
            <p class="pt-4 text-slate-500">{{$image['caption']}}</p>
        </div>

    @endforeach
</div>
