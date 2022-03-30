@php /** @var $block \A17\Twill\Models\Block */ @endphp
<div class="row">
@foreach($block->images('blog_image', 'desktop') as $image)
    <div class="column">
        <img src="{{$image}}" />
    </div>
@endforeach
</div>

<style>
    .row {
        display: flex;
        flex-wrap: wrap;
        padding: 0 4px;
    }

    .column {
        flex: 25%;
        max-width: 25%;
    }

    .column img {
        margin: 8px;
        vertical-align: middle;
        width: 100%;
    }

    @media screen and (max-width: 800px) {
        .column {
            flex: 50%;
            max-width: 50%;
        }
    }

    @media screen and (max-width: 600px) {
        .column {
            flex: 100%;
            max-width: 100%;
        }
    }
</style>
