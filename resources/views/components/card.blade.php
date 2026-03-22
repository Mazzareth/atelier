{{--
/**
 * x-card.blade.php
 * ----------
 * @prop padding: sm | md | lg         (default: md)
 * @prop hoverable: bool               (adds hover effects)
 * @prop href: string                  (wraps in <a>)
 * @prop clickable: bool               (same as href for form actions)
 * @prop class: string
 */
--}}
@props([
    'padding' => 'md',
    'hoverable' => false,
    'href' => null,
    'clickable' => false,
    'class' => '',
])

@php
    $baseClass = "card card-pad-{$padding}";
    if ($hoverable) $baseClass .= ' card-hoverable';
    if ($clickable) $baseClass .= ' card-clickable';
    $classes = trim($baseClass . ' ' . $class);
@endphp

@if($href)
    <a href="{{ $href }}" class="{{ $classes }}" {{ $attributes }}>
        {{ $slot }}
    </a>
@elseif($clickable)
    <button type="button" class="{{ $classes }} w-full text-left" {{ $attributes }}>
        {{ $slot }}
    </button>
@else
    <div class="{{ $classes }}" {{ $attributes }}>
        {{ $slot }}
    </div>
@endif
