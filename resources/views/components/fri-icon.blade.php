@props(['size' => 'md'])

@php
    $sizes = [
        'xs' => 'w-6 h-6',
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-12 h-12',
        'xl' => 'w-16 h-16',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="{{ $sizeClass }} w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0 p-1 {{ $attributes->get('class') }}">
    @if(file_exists(public_path('images/logo/fri-icon.png')))
        <img src="{{ asset('images/logo/fri-icon.png') }}" 
             alt="FRI Logo" 
             class="w-full h-full object-contain">
    @else
        <!-- Fallback SVG Icon -->
        <svg viewBox="0 0 200 200" class="w-full h-full">
            <!-- U Shape (Gray) -->
            <path d="M 50 50 L 50 120 Q 50 150 80 150 L 120 150 Q 150 150 150 120 L 150 50" 
                  fill="#4B5563" stroke="none"/>
            <!-- Red Book/M Shape on top -->
            <path d="M 60 30 L 80 50 L 100 30 L 120 50 L 140 30" 
                  fill="#DC2626" stroke="none"/>
        </svg>
    @endif
</div>

