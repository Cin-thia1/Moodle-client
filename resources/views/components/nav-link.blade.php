@props(['active'])

@php
$baseClasses = 'inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500';

$activeClasses = 'bg-indigo-100 text-indigo-700 font-semibold';

$inactiveClasses = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900';

$classes = $baseClasses . ' ' . ($active ?? false ? $activeClasses : $inactiveClasses);
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>