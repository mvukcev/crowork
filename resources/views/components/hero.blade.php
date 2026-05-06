@props([
    'title' => null,
    'subtitle' => null,
    'size' => 'md',
    'align' => 'center',
    'variant' => 'gradient',
    'theme' => 'home', // home, jobs, education, employers
])

@php
    $sizeClasses = [
        'lg' => 'min-h-[92vh] flex items-center py-24 md:py-28 lg:py-36',
        'md' => 'py-14 md:py-16 lg:py-20',
        'sm' => 'py-12 md:py-14 lg:py-16',
    ];
    
    $alignClasses = [
        'left' => 'text-left',
        'center' => 'text-center',
    ];
    
    $themeClasses = [
        'home' => 'premium-hero',
        'jobs' => 'premium-hero',
        'education' => 'premium-hero',
        'employers' => 'premium-hero',
    ];
    
    $themeShapes = [
        'home' => ['rgba(92, 131, 255, 0.32)', 'rgba(39, 190, 177, 0.24)'],
        'jobs' => ['rgba(135, 108, 248, 0.3)', 'rgba(104, 141, 255, 0.22)'],
        'education' => ['rgba(39, 190, 177, 0.3)', 'rgba(84, 135, 255, 0.2)'],
        'employers' => ['rgba(246, 153, 187, 0.24)', 'rgba(120, 153, 255, 0.22)'],
    ];
    
    $containerClasses = $alignClasses[$align] ?? 'text-center';
    $themeClass = $themeClasses[$theme] ?? $themeClasses['home'];
    $shapes = $themeShapes[$theme] ?? $themeShapes['home'];
@endphp

<section class="relative {{ $themeClass }} {{ $sizeClasses[$size] ?? $sizeClasses['md'] }}">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-36 right-[-8%] w-[30rem] h-[30rem] rounded-full blur-[88px] opacity-90" style="background: {{ $shapes[0] }};"></div>
        <div class="absolute bottom-[-9rem] left-[-5%] w-[26rem] h-[26rem] rounded-full blur-[86px] opacity-90" style="background: {{ $shapes[1] }};"></div>
        <div class="absolute top-[38%] left-[40%] w-[18rem] h-[18rem] rounded-full blur-[74px] opacity-65" style="background: rgba(255, 255, 255, 0.55);"></div>
    </div>

    <div class="container-base relative z-10">
        <div class="max-w-5xl {{ $align === 'left' ? '' : 'mx-auto' }} {{ $containerClasses }}">
            @if($title)
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-semibold text-text-primary mb-5 motion-fade-in text-balance">
                    {{ $title }}
                </h1>
            @endif
            
            @if($subtitle)
                <p class="text-lg md:text-xl text-text-secondary mb-8 md:mb-10 leading-relaxed motion-fade-in max-w-3xl {{ $align === 'left' ? '' : 'mx-auto' }}" style="animation-delay: 80ms;">
                    {{ $subtitle }}
                </p>
            @endif
            
            <!-- Actions Slot -->
            @if($slot->isNotEmpty())
                <div class="space-y-4 motion-fade-in" style="animation-delay: 120ms;">
                    {{ $slot }}
                </div>
            @else
                <!-- Default Content Slot (for search forms, etc.) -->
                @isset($content)
                    <div class="motion-fade-in" style="animation-delay: 120ms;">
                        {{ $content }}
                    </div>
                @endisset
            @endif
        </div>
    </div>
</section>
