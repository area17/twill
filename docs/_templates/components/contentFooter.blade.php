@php
// Find the next and previous links.
$next = null;
$prev = null;

// Loop trackers.
$matched = false;
$last = null;

foreach($tree[$currentSegment]['items'] ?? [] as $item) {
  $last = null;

  if ($item['url'] === $url) {
    $children = array_reverse($item['items'] ?? []);
    $next = array_pop($children);
    break;
  }

  foreach ($item['items'] ?? [] as $childItem) {
    if ($matched) {
      $next = $childItem;
      break;
    }

    if ($url === $childItem['url']) {
      $matched = true;
      $prev = $last;

      if ($last === null) {
        $prev = $item;
      }
    }

    if (!$matched) {
      $last = $childItem;
    }
  }

  if ($matched) {
    break;
  }
}
@endphp

<div class="mt-68">

    <a
        class="text-tip no-underline hover:underline"
        href="{{$githubLink}}"
        ref="noopener noreferrer"
        target="_blank">
        Edit this page on Github
    </a>

    <div class="flex border-t-[1px] border-primary mt-32">
        @if ($prev)
            <a class="no-underline hover:text-tip mt-15" href="{{$prev['url']}}">
                ← {{$prev['title']}}
            </a>
        @endif
        @if ($next)
            <a class="ml-auto no-underline hover:text-tip mt-15" href="{{$next['url']}}">
                {{$next['title']}} →
            </a>
        @endif
    </div>

</div>
