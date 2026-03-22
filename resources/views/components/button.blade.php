{{--
/**
 * x-button.blade.php
 * ----------
 * @prop variant: primary | ghost | secondary | danger  (default: primary)
 * @prop size: sm | md | lg                            (default: md)
 * @prop href: string                                   (if set, renders <a>)
 * @prop type: submit | button | reset                 (default: button)
 * @prop disabled: bool
 * @prop full: bool                                     (full width)
 * @prop class: string                                  (extra classes)
 * @prop onclick: string                                (JS handler)
 */
--}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
    'full' => false,
    'class' => '',
    'onclick' => null,
])

@php
    $classes = trim("btn btn-{$variant} " . ($size !== 'md' ? "btn-{$size}" : "") . " " . ($full ? 'btn-full' : '') . " " . $class);
@endphp

@if($href)
    <a
        href="{{ $href }}"
        class="{{ $classes }}"
        @if($onclick) onclick="{{ $onclick }}" @endif
        {{ $attributes->class(['opacity-50 cursor-not-allowed pointer-events-none' => $disabled]) }}
    >
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        class="{{ $classes }}"
        @if($disabled) disabled @endif
        @if($onclick) onclick="{{ $onclick }}" @endif
        {{ $attributes }}
    >
        {{ $slot }}
    </button>
@endif
