{{--
/**
 * x-avatar.blade.php
 * @prop src: string|null
 * @prop alt: string
 * @prop size: sm | md | lg | xl | 2xl   (default: md)
 * @prop fallback: string               (single letter if no src)
 */
--}}
@props([
    'src' => null,
    'alt' => 'Avatar',
    'size' => 'md',
    'fallback' => '',
])

<div class="avatar avatar-{{ $size }}" {{ $attributes }}>
    @if($src)
        <img src="{{ $src }}" alt="{{ $alt }}">
    @else
        {{ strtoupper($fallback ?: substr($alt, 0, 1)) }}
    @endif
</div>
