{{-- Theme-aware card component --}}
{{-- Usage: <x-themed-card>Content</x-themed-card> --}}

@props([
    'collapsible' => false,
    'collapsed' => false,
])

@php
    $theme = app(\App\Services\ThemeManifest::class);
    $cardStyle = $theme->get('components.card.style', 'standard');
    $cardGap = $theme->get('layout.cardGap', 'normal');
    
    $cardClasses = 'card themed-card';
    
    // Add theme-specific card class
    if ($cardStyle === 'tight') {
        $cardClasses .= ' card-tight';
    } elseif ($cardStyle === 'clinical') {
        $cardClasses .= ' card-clinical';
    }
    
    // Card gap style
    if ($cardGap === 'none') {
        $cardClasses .= ' card-no-gap';
    } elseif ($cardGap === 'tight') {
        $cardClasses .= ' card-tight-gap';
    }
    
    $collapseText = $theme->get('language.cards.collapse', 'Show Less');
    $expandText = $theme->get('language.cards.expand', 'Show More');
@endphp

<div class="{{ $cardClasses }}" {{ $attributes->except('class') }}>
    <div class="card-content">
        {{ $slot }}
    </div>
    
    @if($collapsible)
        <button 
            class="card-toggle" 
            data-collapsed="{{ $collapsed ? 'true' : 'false' }}"
            data-expand-text="{{ $expandText }}"
            data-collapse-text="{{ $collapseText }}"
        >
            <span class="toggle-text">{{ $collapsed ? $expandText : $collapseText }}</span>
            <span class="toggle-icon">▼</span>
        </button>
    @endif
</div>

@once
@push('styles')
<style>
    .themed-card {
        background: var(--bg-panel);
        border: var(--border-width, 1px) solid var(--border-color);
        border-radius: var(--radius-card);
        padding: 1.5rem;
        transition: all calc(0.3s * var(--anim-speed, 1)) var(--motion-easing, ease);
    }
    
    /* Tight style (Rubber theme) */
    .card-tight {
        border-radius: 0;
        padding: 1rem;
    }
    
    /* Clinical style (Guro theme) */
    .card-clinical {
        border-radius: 0;
        border-width: 2px;
        padding: 1rem 1.25rem;
    }
    
    /* Card gap variations */
    .card-no-gap {
        margin: 0;
        padding: 0;
    }
    
    .card-tight-gap {
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
    }
    
    .card:hover {
        transform: scale(var(--hover-compression, 1.02));
        border-color: color-mix(in srgb, var(--accent-color) 40%, var(--border-color));
    }
    
    .card-toggle {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-family: var(--font-mono);
        font-size: 0.75rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        cursor: pointer;
        padding: 0.75rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: color calc(0.15s * var(--anim-speed, 1));
    }
    
    .card-toggle:hover {
        color: var(--accent-color);
    }
    
    .card-toggle .toggle-icon {
        font-size: 0.6rem;
        transition: transform calc(0.2s * var(--anim-speed, 1));
    }
    
    .card-toggle[data-collapsed="true"] .toggle-icon {
        transform: rotate(-90deg);
    }
</style>
@endpush
@endonce
