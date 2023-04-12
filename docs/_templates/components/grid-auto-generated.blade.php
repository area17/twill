@if (!empty($tree[$currentSegment]['items'] ?? []))
<nav class="grid gap-gutter grid-cols-1 md:grid-cols-2 lg:grid-cols-3 mt-48">
    @foreach ($tree[$currentSegment]['items'] ?? [] as $item)
    <div class="card {{ (isset($item["metadata"]["tag"]) || isset($item["metadata"]["date"])) ? ' card--w-metadata' : '' }}">
        <p>
            <a class="card__link" href="{{ $item['url'] }}">{{ $item['title'] }}</a>
            @isset($item["metadata"]["summary"])<span class="card__summary">{{ $item["metadata"]["summary"] }}</span>@endisset
            @isset($item["metadata"]["tag"])<em class="card__tag">{{ $item["metadata"]["tag"] }}</em>@endisset
            @isset($item["metadata"]["date"])<em class="card__date">{{ $item["metadata"]["date"] }}</em>@endisset
        </p>
    </div>
    @endforeach
</nav>
@endif
