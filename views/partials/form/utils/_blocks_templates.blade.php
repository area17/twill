@php
$alreadyRenderedBlocks = collect();
do {
    $blocks = \A17\Twill\Facades\TwillBlocks::getListOfUsedBlocks()
        ->reject(function ($block) {
            return $block->compiled ?? false;
        })
        ->merge(\A17\Twill\Facades\TwillBlocks::getBlockCollection()->getRepeaters())
        ->keyBy('component');
    $blocksToRender = $blocks->diffKeys($alreadyRenderedBlocks);
@endphp

@foreach ($blocksToRender as $block)
    <script type="text/x-template" id="{{ $block->component }}">
        <div class="block__body">
            {!! $block->renderForm() !!}
        </div>
    </script>
@endforeach

@php
    $alreadyRenderedBlocks = $alreadyRenderedBlocks->merge($blocksToRender);
} while (!$blocksToRender->isEmpty());
@endphp

{{-- The order here is important as the renderform above may regiser repeaters. --}}
@php
    $names = $blocks
        ->pluck('component')
        ->values();
@endphp

<script>
    window['{{ config('twill.js_namespace') }}'].TWILL_BLOCKS_COMPONENTS = {!! $names->toJson() !!}
    window['{{ config('twill.js_namespace') }}'].STORE.form.availableRepeaters = {!! (string)\A17\Twill\Facades\TwillBlocks::getAvailableRepeaters() ?: '{}' !!}

</script>
