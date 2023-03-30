@props([
    'branded' => false,
    'url' => '#',
    'label' => '',
])

<a @class([
    'f-h4 no-underline xs:hidden lg:inline',
    'hover:text-link' => !$branded,
    'text-link hover:text-primary' => $branded
]) href="{{ $url }}">{{ $label }}</a>
