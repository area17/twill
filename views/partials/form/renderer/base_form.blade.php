@unless($disableContentFieldset)
  @unless($isCreate)
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
  @else
    @if (isset($renderFields) && $renderFields->isNotEmpty())
      @foreach($renderFields as $field)
        {!! $field->render() !!}
      @endforeach
    @endif
  @endunless
@endunless

@if(isset($renderFieldsets) && $renderFieldsets->isNotEmpty())
  @foreach($renderFieldsets as $fieldset)
    @if ($isCreate)
      <div class="create-fieldset-margin">
        @endif
        @if($fieldset->fields->isNotEmpty())
          <x-twill::formFieldset :id="$fieldset->id" :title="$fieldset->title" :open="$fieldset->open">
            @foreach($fieldset->fields as $field)
              {!! $field->render() !!}
            @endforeach
          </x-twill::formFieldset>
        @endif
        @if ($isCreate)
      </div>
    @endif
  @endforeach
@endif
