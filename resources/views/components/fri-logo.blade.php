@props(['size' => 'md', 'showText' => true])

@php
    $sizes = [
        'xs' => ['w' => 'w-8', 'h' => 'h-8', 'text' => ['title' => 'text-lg', 'subtitle' => 'text-xs']],
        'sm' => ['w' => 'w-10', 'h' => 'h-10', 'text' => ['title' => 'text-lg', 'subtitle' => 'text-xs']],
        'md' => ['w' => 'w-12', 'h' => 'h-12', 'text' => ['title' => 'text-xl', 'subtitle' => 'text-sm']],
        'lg' => ['w' => 'w-16', 'h' => 'h-16', 'text' => ['title' => 'text-2xl', 'subtitle' => 'text-sm']],
        'xl' => ['w' => 'w-20', 'h' => 'h-20', 'text' => ['title' => 'text-2xl', 'subtitle' => 'text-sm']],
    ];
    $sizeClasses = $sizes[$size] ?? $sizes['md'];
    $textClasses = $sizeClasses['text'] ?? $sizes['md']['text'];
@endphp

<div class="flex items-center gap-3 {{ $attributes->get('class') }}">
    <div class="{{ $sizeClasses['w'] }} {{ $sizeClasses['h'] }} bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0 p-1">
        @if(file_exists(public_path('images/logo/fri-logo.png')))
            <img src="{{ asset('images/logo/fri-logo.png') }}" 
                 alt="Fakultas Rekayasa Industri Telkom University" 
                 class="w-full h-full object-contain">
        @else
            <!-- Fallback SVG Logo -->
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
    
    @if($showText)
        <div>
            <div class="{{ $textClasses['title'] }} font-bold text-gray-900">SIYU</div>
            <div class="{{ $textClasses['subtitle'] }} text-gray-600">Sistem Informasi Yudisium</div>
        </div>
    @endif
</div>

