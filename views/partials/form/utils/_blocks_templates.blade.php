<script>
    window.TWILL_BLOCKS_COMPONENTS = []
</script>

@php
    $newBlocks = app(\A17\Twill\Services\Blocks\BlockCollection::class)->all();
@endphp

@foreach($newBlocks as $block)
    <script>
        window.TWILL_BLOCKS_COMPONENTS.push('{{ $block->name }}')
    </script>
    <script type="text/x-template" id="{{ $block->component }}">
        <div class="block__body">
            {!! $block->render() !!}
        </div>
    </script>
@endforeach
