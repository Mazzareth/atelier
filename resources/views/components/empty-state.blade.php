{{--
/**
 * x-empty-state.blade.php
 * @prop title: string
 * @prop description: string|null
 * @prop actionRoute: string|null
 * @prop actionLabel: string|null
 * @prop icon: string (SVG path or emoji fallback)
 */
--}}
@props([
    'title',
    'description' => null,
    'actionRoute' => null,
    'actionLabel' => null,
    'icon' => null,
])

<div class="empty-state">
    @if($icon)
        <div class="empty-state-icon">{{ $icon }}</div>
    @endif

    <h3 class="empty-state-title">{{ $title }}</h3>

    @if($description)
        <p class="empty-state-description">{{ $description }}</p>
    @endif

    @if($actionRoute && $actionLabel)
        <x-button href="{{ $actionRoute }}" variant="ghost" size="sm">
            {{ $actionLabel }}
        </x-button>
    @endif
</div>
