{{--
/**
 * x-mode-switcher.blade.php
 * Play / Work / Admin mode switcher.
 * @prop user: \App\Models\User
 * @prop active: commissioner | artist | admin
 * @prop mods: array  ['commissioner', 'artist', 'admin'] (default based on user)
 */
--}}
@props([
    'user',
    'active' => null,
    'mods' => null,
])

@php
    $active = $active ?? $user->active_profile;
    $mods = $mods ?? [
        'commissioner' => ['label' => 'Play', 'icon' => '✦', 'route' => 'profile.switch'],
        'artist' => ['label' => 'Work', 'icon' => '✎', 'route' => 'profile.switch'],
        'admin' => ['label' => 'Admin', 'icon' => '👑', 'route' => 'profile.switch'],
    ];
    
    // Filter based on user permissions
    if (!$user->isAdmin()) unset($mods['admin']);
    if (!$user->isArtist()) unset($mods['artist']);
    
    $count = count($mods);
    $percentPer = 100 / max($count, 1);
    $activeIndex = array_search($active, array_keys($mods));
    $translateX = ($activeIndex !== false) ? ($percentPer * $activeIndex) . '%' : '0%';
@endphp

<div class="mode-switcher" style="--modes: {{ $count }};">
    @if($count > 1)
        <div class="mode-slider" style="transform: translateX({{ $translateX }});"></div>
    @endif
    
    @foreach($mods as $key => $mode)
        <form method="POST" action="{{ route($mode['route'], $key) }}" class="mode-form">
            @csrf
            <button 
                type="submit" 
                class="mode-btn {{ $active === $key ? 'active' : '' }}"
                data-mode-target="{{ $key }}"
            >
                <span class="mode-btn-icon">{{ $mode['icon'] }}</span>
                <span class="mode-btn-text-wrap">
                    <span class="mode-btn-label">{{ $mode['label'] }}</span>
                </span>
            </button>
        </form>
    @endforeach
</div>
