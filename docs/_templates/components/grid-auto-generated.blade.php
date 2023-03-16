@if (!empty($tree[$currentSegment]['items'] ?? []))
<nav class="grid grid--{{ $type || '' }} gap-gutter grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mt-48">
    @foreach ($tree[$currentSegment]['items'] ?? [] as $item)
    <div class="card card--blog">
        <p><a href="{{ $item['url'] }}">{{ $item['title'] }}</a></p>
    </div>
    @endforeach
</nav>
@endif
