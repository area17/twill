@if ($fields->isNotEmpty())
  @foreach($fields as $field)
    @if ($fields->forBlocks())
        @php
            $field->renderForBlocks = true;
        @endphp
    @endif
    {!! $field->render() !!}
  @endforeach
@endif
