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

<footer class="mt-68">

    <p>
        <a
            class="text-link !no-underline"
            href="{{$githubLink}}"
            rel="noopener noreferrer"
            target="_blank">
            Edit this page on Github
        </a>
    </p>

    <nav class="border-t border-primary mt-32 pt-16">
        <ul class="flex flex-row !list-none !ml-0">
        @if ($prev)
            <li>
                <a class="!no-underline hover:text-primary group" href="{{$prev['url']}}">
                    <span class="relative inline-block transition-transform group-hover:-translate-x-4">←</span> {{$prev['title']}}
                </a>
            </li>
        @endif
        @if ($next)
            <li class="ml-auto">
                <a class="!no-underline hover:text-primary mt-15 group" href="{{$next['url']}}">
                    {{$next['title']}} <span class="relative inline-block transition-transform group-hover:translate-x-4">→</span>
                </a>
            </li>
        @endif
        </ul>
    </nav>

</footer>
