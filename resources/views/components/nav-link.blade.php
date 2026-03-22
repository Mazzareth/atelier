{{--
/**
 * x-nav-link.blade.php
 * @prop href: string
 * @prop active: bool
 * @prop badge: int|string|null   (unread count)
 */
--}}
@props([
    'href' => '#',
    'active' => false,
    'badge' => null,
])

<a
    href="{{ $href }}"
    class="nav-link {{ $active ? 'active' : '' }}"
    {{ $attributes }}
>
    {{ $slot }}

    @if($badge)
        <span class="nav-link-badge">{{ $badge > 99 ? '99+' : $badge }}</span>
    @endif
</a>
