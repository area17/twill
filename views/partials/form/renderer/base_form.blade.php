@unless($disableContentFieldset)
  <x-twill::formFieldset
    id="content"
    title="{{ $contentFieldsetLabel ?? twillTrans('twill::lang.form.content') }}"
  >
    @if (isset($renderFields) && $renderFields->isNotEmpty())
      @foreach($renderFields as $field)
        {!! $field->render() !!}
      @endforeach
    @endif
  </x-twill::formFieldset>
@endunless

@if(isset($renderFieldsets) && $renderFieldsets->isNotEmpty())
  @foreach($renderFieldsets as $fieldset)
    @if($fieldset->fields->isNotEmpty())
      <x-twill::formFieldset :id="$fieldset->id" :title="$fieldset->title" :open="$fieldset->open">
        @foreach($fieldset->fields as $field)
          {!! $field->render() !!}
        @endforeach
      </x-twill::formFieldset>
    @endif
  @endforeach
@endif
