<nav class="nav">
    {{-- Logo --}}
    <a 
        href="{{ auth()->check() 
            ? (auth()->user()->active_profile == 'artist' 
                ? route('artist.dashboard') 
                : (auth()->user()->active_profile == 'admin' 
                    ? route('admin.dashboard') 
                    : route('dashboard')))
            : url('/') }}" 
        class="nav-logo"
        aria-label="Atelier Home"
    >
        <div class="nav-logo-mark">a</div>
        <span>atelier</span>
    </a>

    @auth
        @php
            $u = auth()->user();
            $modesCount = $u->isAdmin() ? 3 : ($u->isArtist() ? 2 : 1);
            $homeRoute = $u->active_profile == 'artist'
                ? route('artist.dashboard')
                : ($u->active_profile == 'admin' ? route('admin.dashboard') : route('dashboard'));
        @endphp

        {{-- Navigation links --}}
        <div class="nav-links">
            @if($modesCount === 1)
                {{-- Commissioner-only: simple links --}}
                <x-nav-link href="{{ route('browse') }}" :active="request()->routeIs('browse')">
                    Browse
                </x-nav-link>
                <x-nav-link href="{{ route('conversations.index') }}" :active="request()->routeIs('conversations.*')">
                    Chats
                </x-nav-link>
                <x-nav-link href="{{ route('commission.index') }}" :active="request()->routeIs('commission.*')">
                    Requests
                </x-nav-link>
            @else
                {{-- Artist/Admin: dropdown for browse --}}
                <details class="nav-dropdown">
                    <summary class="nav-dropdown-trigger">
                        Browse ▾
                    </summary>
                    <div class="nav-dropdown-panel">
                        <div class="nav-dropdown-group">
                            <div class="nav-dropdown-label">Play</div>
                            <x-nav-link href="{{ route('browse') }}" :active="request()->routeIs('browse')" class="nav-dropdown-link">
                                Browse artists
                            </x-nav-link>
                            <x-nav-link href="{{ route('conversations.index') }}" :active="request()->routeIs('conversations.*')" class="nav-dropdown-link">
                                Chats
                            </x-nav-link>
                            <x-nav-link href="{{ route('commission.index') }}" :active="request()->routeIs('commission.*')" class="nav-dropdown-link">
                                My commissions
                            </x-nav-link>
                        </div>
                        
                        @if($u->isArtist() || $u->isAdmin())
                            <div class="nav-dropdown-group">
                                <div class="nav-dropdown-label">Work</div>
                                <x-nav-link href="{{ route('artist.dashboard') }}" :active="request()->routeIs('artist.dashboard')" class="nav-dropdown-link">
                                    Artist dashboard
                                </x-nav-link>
                                <x-nav-link href="{{ route('artist.profile', $u->username) }}" :active="request()->routeIs('artist.profile')" class="nav-dropdown-link">
                                    My profile
                                </x-nav-link>
                                <x-nav-link href="{{ route('artist.workspace.show') }}" :active="request()->routeIs('artist.workspace.*')" class="nav-dropdown-link">
                                    Workspace
                                </x-nav-link>
                                <x-nav-link href="{{ route('artist.requests.index') }}" :active="request()->routeIs('artist.requests.*')" class="nav-dropdown-link">
                                    Requests
                                </x-nav-link>
                            </div>
                        @endif
                    </div>
                </details>
            @endif
        </div>

        {{-- Center: mode switcher (if multiple modes) --}}
        @if($modesCount > 1)
            <div class="nav-center">
                <x-mode-switcher :user="$u" />
            </div>
        @endif

        {{-- Right side: chat toggle, theme, profile --}}
        <div class="nav-end">
            @if($modesCount > 1)
                <a href="{{ route('conversations.index') }}" id="requests-drawer-toggle" class="nav-chat-toggle">
                    Chats
                    <span id="requests-unread-badge" class="nav-unread-badge"></span>
                </a>
            @endif

            <details class="nav-profile-menu">
                <summary class="nav-profile-trigger">
                    <span class="nav-profile-badge">/{{ $u->username ?? strtolower(str_replace(' ', '', $u->name)) }}</span>
                    <span class="nav-profile-caret">▾</span>
                </summary>
                <div class="nav-profile-dropdown">
                    <a href="{{ $homeRoute }}" class="nav-profile-item">Home</a>
                    <a href="{{ route('plans') }}" class="nav-profile-item">Plans</a>
                    
                    <div class="theme-select-group">
                        <label class="nav-profile-label">Theme</label>
                        <div class="theme-select-row">
                            <x-theme-select />
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="nav-profile-item-button">Log out</button>
                    </form>
                </div>
            </details>
        </div>
    @else
        {{-- Guest navigation --}}
        <div class="nav-links nav-links--guest">
            <div class="theme-select-group nav-theme-group--guest">
                <div class="theme-select-row">
                    <x-theme-select compact="true" />
                </div>
            </div>
            
            <a href="{{ route('pricing') }}" class="nav-link">
                Pricing <span class="free-badge">free!</span>
            </a>
        </div>
        
        <a href="{{ route('login') }}" class="sign-in">Sign in</a>
    @endauth
</nav>
