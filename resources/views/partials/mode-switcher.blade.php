<div class="mode-switcher mono">
    @php
        $modes = [];
        $modes[] = ['label' => 'Play', 'value' => 'commissioner', 'icon' => '🎮'];
        
        if (auth()->user()->isArtist()) {
            $modes[] = ['label' => 'Work', 'value' => 'artist', 'icon' => '🎨'];
        }
        
        if (auth()->user()->isAdmin()) {
            $modes[] = ['label' => 'Admin', 'value' => 'admin', 'icon' => '👑'];
        }
        
        $modeCount = count($modes);
        
        $activeMode = auth()->user()->active_profile ?? 'commissioner';
        $activeIndex = array_search($activeMode, array_column($modes, 'value'));
        if ($activeIndex === false) $activeIndex = 0;
        
        // Calculate slider width and position
        $sliderWidth = "calc(100% / {$modeCount})";
        $sliderTransform = "translateX(calc(100% * {$activeIndex}))";
    @endphp

    <div class="mode-slider" style="width: {{ $sliderWidth }}; transform: {{ $sliderTransform }};"></div>
    
    @foreach($modes as $mode)
        <form method="POST" action="{{ route('profile.switch', $mode['value']) }}" class="mode-form" style="flex: 1; display: flex;">
            @csrf
            <button type="submit" class="mode-btn {{ $activeMode === $mode['value'] ? 'active' : '' }}" title="{{ $mode['label'] }}">
                <span class="mode-icon" style="margin-right: 0.2rem;">{{ $mode['icon'] }}</span>
                {{ $mode['label'] }}
            </button>
        </form>
    @endforeach
</div>
