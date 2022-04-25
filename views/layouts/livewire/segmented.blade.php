<div class="flex flex-row gap-4">
    <div class="grow">
        {{-- The default fieldset is always open --}}
        <x-twill::fieldset :open="true" :title="__('Content')">
            @yield('contentFields')
        </x-twill::fieldset>
    </div>
    <div class="w-1/4">
        {{-- publisher --}}
        <x-twill::fieldset :title="__('Publish')" open>
            <div class="mt-6">
                <label for="lang">Language</label>
                <select name="lang" wire:model.debounce="currentLang" class="mb-6">
                    @foreach($langCodes as $langCode)
                        <option value="{{$langCode}}">{{$langCode}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button class="bg-green-600 px-4 py-2 w-full text-white" wire:click="save">Save</button>
            </div>
        </x-twill::fieldset>
        @yield('sidebar')
    </div>
</div>
