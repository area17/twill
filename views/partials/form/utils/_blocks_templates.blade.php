<script>
    window['{{ config('twill.js_namespace') }}'].TWILL_BLOCKS_COMPONENTS = []
</script>

@php
    $allBlocks = (config('twill.block_editor.blocks') ?? []) + (config('twill.block_editor.repeaters') ?? []);

    $blocksForInlineTemplates = collect($allBlocks)->reject(function ($block) {
        return $block['compiled'] ?? false;
    })->filter(function ($block, $blockName) {
        return View::exists('admin.blocks.'.$blockName);
    });
@endphp

@foreach($blocksForInlineTemplates as $blockName => $block)
    <script>
        window['{{ config('twill.js_namespace') }}'].TWILL_BLOCKS_COMPONENTS.push('{{ $blockName }}')
    </script>
    <script type="text/x-template" id="{{ $block['component'] }}">
        <div class="block__body">
            {!! View::make('admin.blocks.' . $blockName, [
                'renderForBlocks' => true
            ])->render() !!}
        </div>
    </script>
@endforeach
