@php
    $colClassAttr = (isset($middle)) ? 'col--third col--third-wrap' : 'col--double col--double-wrap';
@endphp
<div class="wrapper">
    <div class="{{ $colClassAttr }}">
      {{ $left }}
    </div>
    @isset($middle)
    <div class="{{ $colClassAttr }}">
      {{ $middle }}
    </div>
    @endisset
    <div class="{{ $colClassAttr }}">
      {{ $right }}
    </div>
</div>
