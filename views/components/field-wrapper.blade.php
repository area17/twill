<div class="flex flex-col mt-6">
    <label for="{{$name}}" class="mb-2 flex items-center gap-2">
        {{$label}}
        @if ($locale ?? null)
            <x-twill::label>{{$locale}}</x-twill::label>
        @endif
    </label>
    {{$slot}}
    @error($name) <span class="text-red-500">{{ $message }}</span> @enderror
</div>
