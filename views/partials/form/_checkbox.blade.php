@if(isset($livewire) && $livewire)
    @if ($translated)
        @foreach($twillFormLocales as $locale)
            @if ($twillFormCurrentLocale === $locale)
                <label for="{{$name}}">{{$label}} ({{$locale}})</label>
                <input name="{{$name}}" wire:model="form.{{$name}}.{{$locale}}" type="checkbox"/>
            @endif
        @endforeach
    @else
        <label for="{{$name}}">{{$label}} ({{$locale}})</label>
        <input name="{{$name}}" wire:model="form.{{$name}}" type="checkbox"/>
    @endif
    @error($name) <span class="error">{{ $message }}</span> @enderror
    <br />
@else
    <a17-singlecheckbox
        @include('twill::partials.form.utils._field_name')
        label="{{ $label ?? '' }}"
        :initial-value="{{ $default ? 'true' : 'false' }}"
        @if ($note) note='{{ $note }}' @endif
        @if ($disabled) disabled @endif
        @if ($border) :border="true" @endif
        @if ($requireConfirmation) :require-confirmation="true" @endif
        @if ($confirmMessageText) confirm-message-text="{{ $confirmMessageText }}" @endif
        @if ($confirmTitleText) confirm-title-text="{{ $confirmTitleText }}" @endif
        :has-default-store="true"
        in-store="currentValue"
    ></a17-singlecheckbox>

    @unless($renderForBlocks || $renderForModal || (!isset($item->$name) && null === $formFieldsValue = getFormFieldsValue($form_fields, $name)))
        @push('vuexStore')
            window['{{ config('twill.js_namespace') }}'].STORE.form.fields.push({
            name: '{{ $name }}',
            value:
            @if((isset($item) && $item->$name) || ($formFieldsValue ?? false))
                true
            @else
                false
            @endif
            })
        @endpush
    @endunless

@endif
