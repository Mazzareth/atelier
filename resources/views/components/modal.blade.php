{{--
/**
 * x-modal.blade.php
 * Minimal modal shell used by the profile editor.
 *
 * @prop id: string|null
 * @prop size: sm | md | lg | xl   (default: md)
 * @prop closeable: bool
 * @prop class: string
 */
--}}
@props([
    'id' => null,
    'size' => 'md',
    'closeable' => false,
    'class' => '',
])

@php
    $sizeClass = match ($size) {
        'sm' => 'max-w-2xl',
        'lg' => 'max-w-5xl',
        'xl' => 'max-w-7xl',
        default => 'max-w-3xl',
    };

    $classes = trim('modal-shell fixed inset-0 z-50 flex items-center justify-center p-4 ' . $class);
@endphp

<div
    @if($id) id="{{ $id }}" @endif
    class="{{ $classes }}"
    role="dialog"
    aria-modal="true"
    @if($closeable) data-closeable="true" @endif
    {{ $attributes }}
>
    <div class="modal-panel {{ $sizeClass }} w-full">
        {{ $slot }}
    </div>
</div>
