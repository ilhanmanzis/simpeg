@props([
    'active' => false,
    'mobile' => false,
    'page' => true,
    'last' => false,
])

@php
    $base = 'md:pl-3 sm:pl-0 py-2 md:border-0';
    $border = $last ? 'border-0' : 'border-b border-gray-300 md:border-0';
    $size = $mobile ? 'text-sm block' : 'text-md';
    $state = $active ? 'text-yellow-600 hover:text-gray-900' : 'text-gray-900 hover:text-yellow-600';
@endphp

<a
    {{ $attributes->merge([
        'class' => "{$state} {$base} {$border} {$size} md:ml-0 ml-3",
        'aria-current' => $active ? 'page' : null,
    ]) }}>
    {{ $slot }}
</a>
