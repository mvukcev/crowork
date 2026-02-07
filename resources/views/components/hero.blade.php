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
        'lg' => 'py-24 md:py-32 lg:py-40',
        'md' => 'py-20 md:py-24 lg:py-28',
        'sm' => 'py-16 md:py-18 lg:py-20',
    ];
    
    $alignClasses = [
        'left' => 'text-left',
        'center' => 'text-center',
    ];
    
    // Page-specific gradients
    $themeGradients = [
        'home' => 'background: linear-gradient(145deg, #EBF3FF 0%, #F5F8FF 25%, #FFFFFF 60%, #FAFAFA 100%);',
        'jobs' => 'background: linear-gradient(145deg, #F3EBFF 0%, #F9F5FF 25%, #FFFFFF 60%, #FAFAFA 100%);',
        'education' => 'background: linear-gradient(145deg, #ECFDF5 0%, #F0FDF9 25%, #FFFFFF 60%, #FAFAFA 100%);',
        'employers' => 'background: linear-gradient(145deg, #FEF2F2 0%, #FEF7F7 25%, #FFFFFF 60%, #FAFAFA 100%);',
    ];
    
    // Theme-specific shape colors
    $themeShapes = [
        'home' => ['#346AF0', '#00B294'],
        'jobs' => ['#8B5CF6', '#7C3AED'],
        'education' => ['#10B981', '#059669'],
        'employers' => ['#EF4444', '#DC2626'],
    ];
    
    $containerClasses = $alignClasses[$align] ?? 'text-center';
    $gradientStyle = $themeGradients[$theme] ?? $themeGradients['home'];
    $shapes = $themeShapes[$theme] ?? $themeShapes['home'];
@endphp

<!-- Fluent 2 Hero Section - Calm Acrylic Style with Page-Specific Theme -->
<div class="relative overflow-hidden {{ $sizeClasses[$size] ?? $sizeClasses['md'] }}" 
     style="{{ $gradientStyle }}">
    
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
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-semibold text-text-primary mb-6 motion-fade-in" style="letter-spacing: -0.02em;">
                    {{ $title }}
                </h1>
            @endif
            
            @if($subtitle)
                <p class="text-xl md:text-2xl text-text-secondary mb-10 leading-relaxed motion-fade-in max-w-3xl {{ $align === 'left' ? '' : 'mx-auto' }}" style="animation-delay: 80ms;">
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
