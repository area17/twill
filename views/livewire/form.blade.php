<div>
    <select wire:model.debounce="currentLang">
        @foreach($langCodes as $langCode)
            <option value="{{$langCode}}">{{$langCode}}</option>
        @endforeach
    </select>
    <form wire:submit.prevent="save">
        {!! $formView !!}
        <button>Save</button>
    </form>
</div>
