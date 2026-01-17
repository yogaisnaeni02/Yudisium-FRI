@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'icon' => null,
])

@php
    $variants = [
        'primary' => 'bg-green-600 hover:bg-green-700 text-white',
        'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
        'success' => 'bg-green-600 hover:bg-green-700 text-white',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
        'warning' => 'bg-yellow-600 hover:bg-yellow-700 text-white',
        'info' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'outline' => 'bg-white border-2 border-green-600 text-green-600 hover:bg-green-50',
        'ghost' => 'bg-transparent hover:bg-gray-100 text-gray-700',
    ];
    
    $sizes = [
        'sm' => 'py-1.5 px-3 text-sm',
        'md' => 'py-2.5 px-6 text-base',
        'lg' => 'py-3 px-8 text-lg',
    ];
    
    $baseClasses = 'font-semibold rounded-lg transition shadow-sm hover:shadow-md flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed';
    $classes = $baseClasses . ' ' . $variants[$variant] . ' ' . $sizes[$size];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            {!! $icon !!}
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            {!! $icon !!}
        @endif
        {{ $slot }}
    </button>
@endif
