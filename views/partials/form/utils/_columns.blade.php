@php
    $colClassAttr = (isset($middle) || isset($middleFields)) ? 'col--third col--third-wrap' : 'col--double col--double-wrap';
@endphp
<div class="wrapper">
    <div class="{{ $colClassAttr }}">
      @isset($leftFields)
        @foreach($leftFields as $field)
            {!! $field->render() !!}
        @endforeach
      @endisset
      {{ $left }}
    </div>
    @if(isset($middle) || isset($middleFields))
    <div class="{{ $colClassAttr }}">
      @isset($middleFields)
        @foreach($middleFields as $field)
            {!! $field->render() !!}
        @endforeach
      @endisset
      {{ $middle }}
    </div>
    @endif
    <div class="{{ $colClassAttr }}">
      @isset($rightFields)
        @foreach($rightFields as $field)
          {!! $field->render() !!}
        @endforeach
      @endisset
      {{ $right }}
    </div>
</div>
