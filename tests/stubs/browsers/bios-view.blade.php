<div>This is a bio</div>

<div>
    @if ($writer = $item->writer->first())
        Writer: {{ $writer->title }}
    @else
        No writer
    @endif
</div>
