@php /** @var $block \A17\Twill\Models\Block */ @endphp
<div class="grid grid-cols-3 gap-4">
    @foreach($block->images('blog_image', 'desktop') as $image)
        <img class="h-auto w-auto" src="{{$image}}"/>
    @endforeach
</div>
