@props([
    'branded' => false,
    'url' => '#',
    'label' => '',
    'mobileNav' => false
])

<a @class([
    'f-h4 no-underline',
    'xs:hidden lg:inline' => !$mobileNav,
    'block' => $mobileNav,
    'hover:text-link' => !$branded,
    'text-link hover:text-primary' => $branded
]) href="{{ $url }}">{{ $label }}</a>
