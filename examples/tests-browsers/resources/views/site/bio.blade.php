<div>This is a bio</div>

<div>
    @if ($writer = $item->writer)
        Writer: {{ $writer->title }}
    @else
        No writer
    @endif
</div>
