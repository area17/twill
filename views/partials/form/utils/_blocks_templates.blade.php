@php
    $blocks = \A17\Twill\Facades\TwillBlocks::getBlockCollection()
        ->collect()
        ->reject(function ($block) {
            return $block->compiled ?? false;
        });
@endphp

@foreach ($blocks as $block)
    <script type="text/x-template" id="{{ $block->component }}">
        <div class="block__body">
            {!! $block->renderForm() !!}
        </div>
    </script>
@endforeach

{{-- The order here is important as the renderform above may regiser repeaters. --}}
@php
    $names = $blocks
        ->pluck('component')
        ->values();
@endphp

<script>
    window['{{ config('twill.js_namespace') }}'].TWILL_BLOCKS_COMPONENTS = {!! $names->toJson() !!}
    window['{{ config('twill.js_namespace') }}'].STORE.form.availableRepeaters = {!! \A17\Twill\Facades\TwillBlocks::getAvailableRepeaters() ?? '{}' !!}

</script>

