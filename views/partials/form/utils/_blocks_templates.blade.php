@php
    $newBlocks = app(\A17\Twill\Services\Blocks\BlockCollection::class)->all();

    $names = collect($newBlocks)->map(function ($block) {
        return $block->name;
    })->values()->toJson();
@endphp

<script>
    window['{{ config('twill.js_namespace') }}'].TWILL_BLOCKS_COMPONENTS = {!! $names !!}
</script>

@foreach($newBlocks as $block)
    <script type="text/x-template" id="{{ $block->component }}">
        <div class="block__body">
            {!! $block->render() !!}
        </div>
    </script>
@endforeach
