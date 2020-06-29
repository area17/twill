@php
    $blocks = app(\A17\Twill\Services\Blocks\BlockCollection::class)
        ->collect()
        ->reject(function ($block) {
            return $block->compiled ?? false;
        });

    $names = $blocks->pluck('name')->values()->toJson();
@endphp

<script>
    window['{{ config('twill.js_namespace') }}'].TWILL_BLOCKS_COMPONENTS = {!! $names !!}
</script>

@foreach($blocks as $block)
    <script type="text/x-template" id="{{ $block->component }}">
        <div class="block__body">
            {!! $block->render() !!}
        </div>
    </script>
@endforeach
