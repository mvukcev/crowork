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
        'lg' => 'py-20 md:py-28 lg:py-32',
        'md' => 'py-14 md:py-16 lg:py-20',
        'sm' => 'py-10 md:py-12 lg:py-14',
    ];
    
    $alignClasses = [
        'left' => 'text-left',
        'center' => 'text-center',
    ];
    
    // Page-specific Fluent 2 hero backgrounds
    $themeClasses = [
        'home' => 'fluent-hero-home',
        'jobs' => 'fluent-hero-jobs',
        'education' => 'fluent-hero-education',
        'employers' => 'fluent-hero-employers',
    ];
    
    // Theme-specific shape colors
    $themeShapes = [
        'home' => ['#346AF0', '#00B294'],
        'jobs' => ['#8B5CF6', '#7C3AED'],
        'education' => ['#10B981', '#059669'],
        'employers' => ['#EF4444', '#DC2626'],
    ];
    
    $containerClasses = $alignClasses[$align] ?? 'text-center';
    $themeClass = $themeClasses[$theme] ?? $themeClasses['home'];
    $shapes = $themeShapes[$theme] ?? $themeShapes['home'];
@endphp

<!-- Fluent 2 Hero Section - Calm Acrylic Style with Page-Specific Theme -->
<div class="relative overflow-hidden {{ $themeClass }} {{ $sizeClasses[$size] ?? $sizeClasses['md'] }}">
    
    <!-- Soft Abstract Shapes (Fluent style - oversized, blurred, partially off-screen) -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <!-- Large shape top-right -->
        <div class="absolute -top-48 -right-48 w-96 h-96 rounded-full hero-shape" 
             style="background: linear-gradient(135deg, {{ $shapes[0] }} 0%, {{ $shapes[1] }} 100%); animation: heroFloat1 25s ease-in-out infinite;">
        </div>
        
        <!-- Large shape bottom-left -->
        <div class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full hero-shape" 
             style="background: linear-gradient(135deg, {{ $shapes[1] }} 0%, {{ $shapes[0] }} 100%); animation: heroFloat2 30s ease-in-out infinite 5s;">
        </div>
        
        <!-- Subtle gradient overlay for depth -->
        <div class="absolute inset-0" 
             style="background: radial-gradient(circle at 30% 50%, rgba({{ $theme === 'home' ? '52, 106, 240' : ($theme === 'jobs' ? '139, 92, 246' : ($theme === 'education' ? '16, 185, 129' : '239, 68, 68')) }}, 0.03) 0%, transparent 50%); animation: heroFloat3 20s ease-in-out infinite 10s;">
        </div>
    </div>
    
    <!-- Content -->
    <div class="container-base relative z-10">
        <div class="max-w-4xl {{ $align === 'left' ? '' : 'mx-auto' }}">
            @if($title)
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-semibold text-text-primary mb-5 motion-fade-in text-balance" style="letter-spacing: -0.035em;">
                    {{ $title }}
                </h1>
            @endif
            
            @if($subtitle)
                <p class="text-lg md:text-xl text-text-secondary mb-8 leading-relaxed motion-fade-in max-w-3xl {{ $align === 'left' ? '' : 'mx-auto' }}" style="animation-delay: 80ms;">
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
</div>

<!-- Hero Animations (CSS) - Calm, subtle movements -->
<style>
    @keyframes heroFloat1 {
        0%, 100% {
            transform: translate(0, 0) scale(1);
        }
        33% {
            transform: translate(-20px, 30px) scale(1.05);
        }
        66% {
            transform: translate(30px, -20px) scale(0.95);
        }
    }
    
    @keyframes heroFloat2 {
        0%, 100% {
            transform: translate(0, 0) scale(1);
        }
        33% {
            transform: translate(25px, -25px) scale(1.08);
        }
        66% {
            transform: translate(-30px, 20px) scale(0.92);
        }
    }
    
    @keyframes heroFloat3 {
        0%, 100% {
            opacity: 0.3;
        }
        50% {
            opacity: 0.6;
        }
    }
    
    /* Reduced motion: disable animations */
    @media (prefers-reduced-motion: reduce) {
        .hero-shape,
        [style*="animation"] {
            animation: none !important;
        }
    }
</style>
