@if ($fields->isNotEmpty())
  @foreach($fields as $field)
    {!! $field->render() !!}
  @endforeach
@endif
