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

<hr class="py-4" />

<div class="flex py-4">
@if ($prev)
        <a href="{{$prev['url']}}">< {{$prev['title']}}</a>
@endif
@if ($next)
        <a class="ml-auto" href="{{$next['url']}}">{{$next['title']}} ></a>
@endif
</div>

<a href="{{$githubLink}}" target="_blank">Edit on Github</a>

