@php
    $slotsOpen = (int)($module->settings['slots_open'] ?? 0);
    $isOpen = $slotsOpen > 0;
    $identity = $viewerIdentity ?? null;
    $themeName = $themeName ?? '';

    // Identity-aware commission labels (Hardcoded until moved to theme manifests)
    if ($themeName === 'dickgirl-dom') {
        if ($identity === 'male') {
            $ctaPrimary = "Tell Me What You Want";
            $statusLabel = $isOpen ? "I HAVE {$slotsOpen} OPEN SLOTS" : "NO SLOTS AVAILABLE";
            $subtitle = "Be specific. I don't do vague requests.";
        } elseif ($identity === 'female') {
            $ctaPrimary = "Request to Work Together";
            $statusLabel = $isOpen ? "{$slotsOpen} SLOTS — TAKE ONE" : "FULLY BOOKED";
            $subtitle = "What are you offering me?";
        } elseif ($identity === 'dickgirl') {
            $ctaPrimary = "Open a Commission";
            $statusLabel = $isOpen ? "{$slotsOpen} SLOTS — EQUALS ONLY" : "AT CAPACITY";
            $subtitle = "No time-wasters. I know what I'm worth.";
        } else {
            $ctaPrimary = "Request";
            $statusLabel = $isOpen ? "{$slotsOpen} SLOTS OPEN" : "NO AVAILABILITY";
            $subtitle = "Tell me what you're looking for.";
        }
    } elseif ($themeName === 'dickgirl-mommy') {
        if ($identity === 'male') {
            $ctaPrimary = "Ask Me Anything";
            $statusLabel = $isOpen ? "Welcoming {$slotsOpen} new friends~" : "Taking a little break~";
            $subtitle = "Come tell me what you've been dreaming of, baby.";
        } elseif ($identity === 'female') {
            $ctaPrimary = "Work With Me";
            $statusLabel = $isOpen ? "{$slotsOpen} openings for you~" : "Resting for now~";
            $subtitle = "What would you love to have made, sweetheart?";
        } elseif ($identity === 'dickgirl') {
            $ctaPrimary = "Let's Create";
            $statusLabel = $isOpen ? "{$slotsOpen} slots — equals welcome" : "At capacity for now";
            $subtitle = "What are you working on, dear?";
        } else {
            $ctaPrimary = "Get in Touch";
            $statusLabel = $isOpen ? "{$slotsOpen} slots open~" : "Taking a break~";
            $subtitle = "Whatever you need, darling. Let's talk.";
        }
    } else {
        $ctaPrimary = $isOpen ? "Request Commission" : "Join Waitlist";
        $statusLabel = $isOpen ? "OPEN FOR {$slotsOpen} SLOTS" : "COMMISSIONS CLOSED";
        $subtitle = !empty($module->settings['next_open_date']) ? "Next opening: {$module->settings['next_open_date']}" : null;
    }
@endphp

<x-profile-module :module="$module" :isEditMode="$isEditMode ?? false" type="comm_slots" class="group">
    <x-card padding="lg" class="border-l-4 {{ $isOpen ? 'border-l-accent' : 'border-l-border' }}">
        
        <div class="font-mono text-xs text-muted uppercase tracking-widest mb-4">
            Commission Status
        </div>

        <div class="flex items-center gap-3 mb-2">
            <div class="w-3 h-3 rounded-full shrink-0 {{ $isOpen ? 'bg-accent shadow-[0_0_10px_var(--color-accent)]' : 'bg-red-500 shadow-[0_0_8px_#ff4d4d]' }}"></div>
            <span class="font-mono text-sm font-bold uppercase tracking-widest {{ $isOpen ? 'text-accent' : 'text-main' }}">
                {{ $statusLabel }}
            </span>
        </div>

        @if($subtitle)
            <p class="text-sm text-muted mb-6">
                {{ $subtitle }}
            </p>
        @else
            <div class="mb-6"></div>
        @endif

        <div class="flex flex-col gap-3">
            <x-button type="button" variant="ghost" class="w-full justify-center text-accent border-accent/30 hover:bg-accent/10 open-pricing-sheet">
                View Pricing & Quote
            </x-button>

            @auth
                @if(auth()->id() !== $artist->id)
                    <x-button href="{{ route('commission.create', $artist->username) }}" variant="primary" class="w-full justify-center">
                        {{ $ctaPrimary }}
                    </x-button>
                @endif
            @else
                <x-button href="{{ route('login') }}" variant="primary" class="w-full justify-center">
                    Log in to Request
                </x-button>
            @endauth
        </div>
    </x-card>
</x-profile-module>
