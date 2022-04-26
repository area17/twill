@props(['type' => 'primary'])
@if ($type === 'primary')
    <button {{$attributes->merge(['class' => 'bg-blue-600 text-white px-4 py-2'])}}>
        {{$slot}}
    </button>
@elseif ($type === 'secondary')
    <button {{$attributes->merge(['class' => 'border border-gray-400 bg-white text-gray-400 px-4 py-2'])}}>
        {{$slot}}
    </button>
@endif
