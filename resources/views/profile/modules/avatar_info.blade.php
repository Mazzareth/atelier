@php
    $identity = $viewerIdentity ?? null;
    $identityLabel = $theme->getIdentityLabel($identity) ?? 'Guest';
    $greeting = $theme->identityAware('greeting', $identity, null);
    $isFollowing = auth()->check() ? \Illuminate\Support\Facades\DB::table('followers')->where('user_id', $artist->id)->where('follower_id', auth()->id())->exists() : false;
@endphp

<x-profile-module :module="$module" :isEditMode="$isEditMode ?? false" type="avatar_info" class="group relative">
    <div class="profile-avatar-module flex flex-col md:flex-row gap-8 items-center md:items-end py-8 relative">
        
        {{-- Greeting (Theme Identity injected if it exists) --}}
        @if($greeting)
            <div class="absolute top-0 left-0 w-full text-center md:text-left mb-4">
                <span class="font-mono text-xs uppercase tracking-widest text-accent opacity-80">{{ $greeting }}</span>
            </div>
        @endif

        {{-- Avatar & Follow Action --}}
        <div class="relative shrink-0 flex flex-col items-center gap-4 mt-6 md:mt-0">
            <x-avatar 
                :src="$module->settings['avatar'] ?? null" 
                :alt="$artist->username ?? 'Artist'" 
                size="xl" 
                class="border-4 border-surface shadow-lg ring-2 ring-border/50 profile-avatar-img"
            />
            
            @if($module->settings['show_follow'] ?? true)
                <div class="-mt-8 relative z-10 profile-follow-wrap">
                    <x-follow-button :artist="$artist" :following="$isFollowing" />
                </div>
            @endif
        </div>
        
        {{-- Artist Info --}}
        <div class="flex-1 text-center md:text-left min-w-0 pb-2">
            <div class="flex items-center justify-center md:justify-start gap-3 mb-4 flex-wrap">
                <x-badge variant="muted" size="sm" class="profile-theme-chip bg-accent/10 text-accent border-accent/30">
                    Artist Page
                </x-badge>
            </div>
            
            <h1 class="text-4xl md:text-5xl font-bold font-serif leading-none mb-3 text-main profile-artist-name break-words">
                {{ $artist->name ?? 'Artist Name' }}
            </h1>
            
            <div class="flex items-center justify-center md:justify-start gap-4 flex-wrap mb-6">
                <div class="font-mono text-accent text-lg">
                    {{ '/' . ($artist->username ?? 'username') }}
                </div>
                <div class="h-4 w-px bg-border hidden sm:block"></div>
                <div class="font-mono text-sm text-muted uppercase tracking-wider">
                    <span class="follower-count-value text-main font-bold">{{ number_format($artist->follower_count ?? 0) }}</span> 
                    <span class="opacity-80">followers</span>
                </div>
            </div>
            
            <div class="flex items-center justify-center md:justify-start gap-3 flex-wrap">
                <x-button type="button" variant="secondary" size="sm" class="profile-copy-link-btn gap-2">
                    <span class="text-xs">⎘</span> Copy Profile Link
                </x-button>
                <x-button href="#sidebar-zone" variant="ghost" size="sm" class="gap-2">
                    View Links
                </x-button>
            </div>
        </div>
    </div>
</x-profile-module>

@push('scripts')
@once
<script>
document.addEventListener('click', async function(e) {
    if (e.target.closest('.profile-copy-link-btn')) {
        try {
            await navigator.clipboard.writeText(window.location.href);
            const btn = e.target.closest('.profile-copy-link-btn');
            const original = btn.innerHTML;
            btn.innerHTML = '<span class="text-xs">✓</span> Copied!';
            setTimeout(() => btn.innerHTML = original, 1500);
        } catch (err) { console.error(err); }
    }
});
</script>
@endonce
@endpush
