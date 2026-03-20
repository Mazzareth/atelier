{{-- Theme-aware button component --}}
{{-- Usage: <x-themed-button>Text</x-themed-button> --}}
{{-- Or: <x-themed-button href="/url">Link Text</x-themed-button> --}}

@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
    'loading' => false,
])

@php
    $theme = app(\App\Services\ThemeManifest::class);
    $buttonText = $theme->get('language.buttons.default', 'Submit');
    $hoverText = $theme->get('language.buttons.hover', 'Click');
    $loadingText = $theme->get('language.buttons.loading', 'Loading...');
    $disabledText = $theme->get('language.buttons.disabled', 'Disabled');
    $layoutStyle = $theme->get('layout.cardGap', 'normal');
    
    $baseClasses = 'btn btn-' . $variant . ' btn-' . $size;
    
    // Add theme-specific button class
    $buttonStyle = $theme->get('components.button.style', 'standard');
    if ($buttonStyle === 'tight') {
        $baseClasses .= ' btn-tight';
    } elseif ($buttonStyle === 'clinical') {
        $baseClasses .= ' btn-clinical';
    }
    
    if ($disabled) {
        $baseClasses .= ' disabled';
    }
    
    if ($loading) {
        $baseClasses .= ' loading';
    }
@endphp

@if($href)
    <a href="{{ $href }}" class="{{ $baseClasses }}" data-hover-text="{{ $hoverText }}">
        <span class="btn-text">{{ $slot->isEmpty() ? $buttonText : $slot }}</span>
        @if($loading)
            <span class="btn-loading-text" style="display: none;">{{ $loadingText }}</span>
        @endif
        <span class="arrow">→</span>
    </a>
@else
    <button 
        type="{{ $type }}" 
        class="{{ $baseClasses }}" 
        data-hover-text="{{ $hoverText }}"
        @if($disabled) disabled @endif
    >
        <span class="btn-text">{{ $slot->isEmpty() ? $buttonText : $slot }}</span>
        @if($loading)
            <span class="btn-loading-text" style="display: none;">{{ $loadingText }}</span>
        @endif
    </button>
@endif
