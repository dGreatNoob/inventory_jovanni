{{-- Dynamic Jovanni Logo - switches based on theme --}}
<div 
    class="logo-container {{ $attributes->get('class') }}" 
    x-data="{ 
        isDark: $flux.appearance === 'dark' || ($flux.appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)
    }"
    x-effect="isDark = $flux.appearance === 'dark' || ($flux.appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)"
>
    {{-- Light mode logo (black) --}}
    <img 
        src="{{ asset('images/jovanni_logo_black.png') }}" 
        alt="Jovanni Logo" 
        x-show="!isDark"
        class="logo-image"
    >
    
    {{-- Dark mode logo (white) --}}
    <img 
        src="{{ asset('images/jovanni_logo_white.png') }}" 
        alt="Jovanni Logo" 
        x-show="isDark"
        class="logo-image"
    >
</div>

<style>
    /* Default logo size for regular usage */
    .logo-image {
        height: 30px;
        width: auto;
        object-fit: contain;
        object-position: center;
    }
    
    /* Dashboard logo sizing - prevent distortion */
    .logo-container.h-6.w-8 .logo-image,
    .h-6.w-8 .logo-image {
        height: 24px !important;
        width: auto !important;
        object-fit: contain;
        object-position: center;
    }
    
    /* Larger dashboard logo sizing - 2x size */
    .logo-container.h-12.w-16 .logo-image,
    .h-12.w-16 .logo-image {
        height: 48px !important;
        width: auto !important;
        object-fit: contain;
        object-position: center;
    }
    
    /* Large logo for auth pages - fill the entire container */
    .logo-container.size-45 .logo-image,
    .size-45 .logo-image {
        height: 100% !important;
        width: 100% !important;
        object-fit: cover;
        object-position: center;
    }
    
    .logo-container.size-35 .logo-image,
    .size-35 .logo-image {
        height: 100% !important;
        width: 100% !important;
        object-fit: contain;
        object-position: center;
    }
    
    /* Fallback CSS for when Alpine.js hasn't loaded yet */
    .logo-container img:first-child {
        display: block;
    }
    
    .logo-container img:last-child {
        display: none;
    }
    
    /* Show dark logo when dark mode is active (CSS fallback) */
    .dark .logo-container img:first-child {
        display: none;
    }
    
    .dark .logo-container img:last-child {
        display: block;
    }
    
    /* Also handle data-theme attribute for compatibility */
    [data-theme="dark"] .logo-container img:first-child {
        display: none;
    }
    
    [data-theme="dark"] .logo-container img:last-child {
        display: block;
    }
</style>
