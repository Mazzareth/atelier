{{--
/**
 * x-badge.blade.php
 * @prop variant: accent | muted | success | error   (default: accent)
 * @prop size: sm | md                              (default: md)
 */
--}}
@props([
    'variant' => 'accent',
    'size' => 'md',
])

<span class="badge badge-{{ $variant }} badge-{{ $size }}" {{ $attributes }}>
    {{ $slot }}
</span>
