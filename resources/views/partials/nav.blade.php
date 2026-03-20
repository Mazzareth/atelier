<nav>
    <a href="{{ auth()->check() ? (auth()->user()->active_profile == 'artist' ? route('artist.dashboard') : (auth()->user()->active_profile == 'admin' ? route('admin.dashboard') : route('dashboard'))) : url('/') }}" class="logo logo-link serif">
        <div class="logo-circle">a</div>
        atelier
    </a>
    
    @auth
        @php
            $u = auth()->user();
            $modesCount = $u->isAdmin() ? 3 : ($u->isArtist() ? 2 : 1);
            $active = $u->active_profile;
            if ($modesCount == 3) {
                if ($active == 'commissioner') $tx = '0%';
                if ($active == 'artist') $tx = '100%';
                if ($active == 'admin') $tx = '200%';
            } else {
                if ($active == 'commissioner') $tx = '0%';
                if ($active == 'artist') $tx = '100%';
            }

            $homeRoute = $active == 'artist'
                ? route('artist.dashboard')
                : ($active == 'admin' ? route('admin.dashboard') : route('dashboard'));
        @endphp

        <div class="app-nav-shell {{ $modesCount === 1 ? 'app-nav-shell--commissioner' : '' }}">
            <div class="nav-links mono app-nav-left {{ $modesCount === 1 ? 'app-nav-left--commissioner' : '' }}">
                @if($modesCount === 1)
                    <a href="{{ route('browse') }}" class="nav-quick-link {{ request()->routeIs('browse') ? 'is-active' : '' }}">Browse</a>
                    <a href="{{ route('conversations.index') }}" class="nav-quick-link {{ request()->routeIs('conversations.*') ? 'is-active' : '' }}">Chats</a>
                    <a href="{{ route('commission.index') }}" class="nav-quick-link {{ request()->routeIs('commission.*') ? 'is-active' : '' }}">Requests</a>
                @else
                    <details class="nav-menu-dropdown">
                        <summary class="nav-menu-trigger" style="color: {{ request()->routeIs('browse') || request()->routeIs('artist.workspace.*') || request()->routeIs('artist.dashboard') || request()->routeIs('artist.requests.*') || request()->routeIs('commission.*') ? 'var(--accent-color)' : 'inherit' }}">Browse ▾</summary>
                        <div class="nav-menu-panel">
                            <div class="nav-menu-group">
                                <div class="nav-menu-label mono">Play</div>
                                <a href="{{ route('browse') }}" class="nav-menu-link">Browse artists</a>
                                <a href="{{ route('conversations.index') }}" class="nav-menu-link">Chats</a>
                                <a href="{{ route('commission.index') }}" class="nav-menu-link">My commissions</a>
                            </div>
                            @if($u->isArtist() || $u->isAdmin())
                                <div class="nav-menu-group">
                                    <div class="nav-menu-label mono">Work</div>
                                    <a href="{{ route('artist.dashboard') }}" class="nav-menu-link">Artist dashboard</a>
                                    <a href="{{ route('artist.profile', $u->username) }}" class="nav-menu-link">My profile</a>
                                    <a href="{{ route('artist.workspace.show') }}" class="nav-menu-link">Workspace</a>
                                    <a href="{{ route('artist.requests.index') }}" class="nav-menu-link">Requests</a>
                                </div>
                            @endif
                        </div>
                    </details>
                @endif
            </div>

            @if($modesCount > 1)
            <div class="app-nav-center">
                <div class="mode-switcher mono" style="--modes: {{ $modesCount }};">
                    <div class="mode-slider" style="transform: translateX({{ $tx }});"></div>
                    
                    <form class="mode-form" method="POST" action="{{ route('profile.switch', 'commissioner') }}">
                        @csrf
                        <button type="submit" class="mode-btn {{ $active == 'commissioner' ? 'active' : '' }}">
                            <span class="mode-btn-icon">✦</span>
                            <span class="mode-btn-text-wrap">
                                <span class="mode-btn-label">Play</span>
                            </span>
                        </button>
                    </form>
                    
                    @if($u->isArtist() || $u->isAdmin())
                    <form class="mode-form" method="POST" action="{{ route('profile.switch', 'artist') }}">
                        @csrf
                        <button type="submit" class="mode-btn {{ $active == 'artist' ? 'active' : '' }}">
                            <span class="mode-btn-icon">✎</span>
                            <span class="mode-btn-text-wrap">
                                <span class="mode-btn-label">Work</span>
                            </span>
                        </button>
                    </form>
                    @endif
                    
                    @if($u->isAdmin())
                    <form class="mode-form" method="POST" action="{{ route('profile.switch', 'admin') }}">
                        @csrf
                        <button type="submit" class="mode-btn {{ $active == 'admin' ? 'active' : '' }}" data-mode-target="admin">
                            <span class="mode-btn-icon">👑</span>
                            <span class="mode-btn-text-wrap">
                                <span class="mode-btn-label">Admin</span>
                            </span>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif

            <div class="app-nav-right">
                @if($modesCount > 1)
                    <a href="{{ route('conversations.index') }}" id="requests-drawer-toggle" class="sign-in nav-chat-link nav-chat-link--with-badge">
                        Chats
                        <span id="requests-unread-badge" class="nav-unread-badge"></span>
                    </a>
                @endif

                <details class="nav-profile-menu">
                    <summary class="nav-profile-trigger mono">
                        <span class="nav-profile-badge">{{ '/' . ($u->username ?? strtolower(str_replace(' ', '', $u->name))) }}</span>
                        <span class="nav-profile-caret">▾</span>
                    </summary>
                    <div class="nav-profile-dropdown">
                        <a href="{{ $homeRoute }}" class="nav-profile-item mono">Home</a>
                        <a href="{{ route('plans') }}" class="nav-profile-item mono">Plans</a>
                        <div class="theme-select-group">
                            <label class="nav-profile-label mono" for="theme-selector">Theme</label>
                            <div class="theme-select-row">
                                <select id="theme-selector" class="theme-select mono nav-theme-select" data-theme-selector>
                                    <optgroup label="Core">
                                        <option value="default">Atelier Green (Default)</option>
                                    </optgroup>
                                    <optgroup label="Pride">
                                        <option value="gay">Gay Pride</option>
                                        <option value="trans">Trans Pride</option>
                                        <option value="lesbian">Lesbian Pride</option>
                                        <option value="bi">Bisexual Pride</option>
                                        <option value="nonbinary">Non-Binary Pride</option>
                                        <option value="pan">Pansexual Pride</option>
                                        <option value="asexual">Asexual Pride</option>
                                        <option value="genderqueer">Genderqueer Pride</option>
                                        <option value="intersex">Intersex Pride</option>
                                        <option value="genderfluid">Genderfluid Glow</option>
                                    </optgroup>
                                    <optgroup label="Soft & Storybook">
                                        <option value="yami_kawaii">Yami Kawaii</option>
                                        <option value="pastel_goth">Pastel Goth</option>
                                        <option value="deep_sea">Deep Sea</option>
                                    </optgroup>
                                    <optgroup label="Species">
                                        <option value="goat">Goat Hearth</option>
                                        <option value="moth">Moth Moon</option>
                                        <option value="bunny">Bunny Plush</option>
                                        <option value="sea_bunny">Sea Bunny Drift</option>
                                    </optgroup>
                                    <optgroup label="Vibes & Dynamics">
                                        <option value="dickgirl">Dickgirl Dommy Mommy</option>
                                        <option value="femboy">Soft Femboy</option>
                                        <option value="dominant">Strict Dominant</option>
                                        <option value="submissive">Submissive Pet</option>
                                        <option value="musk">Heavy Musk</option>
                                        <option value="pup">Pup Play</option>
                                    </optgroup>
                                    <optgroup label="Fetish Material">
                                        <option value="rubber">Rubber Latex</option>
                                        <option value="rope">Rope Bondage</option>
                                        <option value="inflation">Inflation Bubble</option>
                                        <option value="vore">Vore Jungle</option>
                                        <option value="werewolf">Werewolf Moon</option>
                                        <option value="hypno">Hypno Spiral</option>
                                        <option value="daddy">Daddy Lounge</option>
                                        <option value="hexcorp">Hexcorp Clinic</option>
                                    </optgroup>
                                </select>
                                <label class="mature-toggle mono">
                                    <input type="checkbox" data-theme-mature-toggle>
                                    <span>Show mature themes</span>
                                </label>
                            </div>
                            <div class="extreme-themes-optgroup" aria-hidden="true">
                                <template>
                                    <optgroup label="Extreme Kinks (opt-in required)">
                                        <option value="guro">Guro Clinic</option>
                                        <option value="living_toilet">Living Toilet</option>
                                        <option value="parasites">Parasites Bloom</option>
                                    </optgroup>
                                </template>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="logout-form">
                            @csrf
                            <button type="submit" class="nav-profile-item nav-profile-item-button mono">Log out</button>
                        </form>
                    </div>
                </details>
            </div>
        </div>
    @else
        <div class="nav-links mono nav-links--guest">
            <div class="theme-select-group nav-theme-group--guest">
                <div class="theme-select-row">
                    <select id="theme-selector" class="theme-select theme-select--compact mono" data-theme-selector>
                        <optgroup label="Core">
                            <option value="default">Atelier Green (Default)</option>
                        </optgroup>
                        <optgroup label="Pride">
                            <option value="gay">Gay Pride</option>
                            <option value="trans">Trans Pride</option>
                            <option value="lesbian">Lesbian Pride</option>
                            <option value="bi">Bisexual Pride</option>
                            <option value="nonbinary">Non-Binary Pride</option>
                            <option value="pan">Pansexual Pride</option>
                            <option value="asexual">Asexual Pride</option>
                            <option value="genderqueer">Genderqueer Pride</option>
                            <option value="intersex">Intersex Pride</option>
                            <option value="genderfluid">Genderfluid Glow</option>
                        </optgroup>
                        <optgroup label="Soft & Storybook">
                            <option value="yami_kawaii">Yami Kawaii</option>
                            <option value="pastel_goth">Pastel Goth</option>
                            <option value="deep_sea">Deep Sea</option>
                        </optgroup>
                        <optgroup label="Species">
                            <option value="goat">Goat Hearth</option>
                            <option value="moth">Moth Moon</option>
                            <option value="bunny">Bunny Plush</option>
                            <option value="sea_bunny">Sea Bunny Drift</option>
                        </optgroup>
                        <optgroup label="Vibes & Dynamics">
                            <option value="dickgirl">Dickgirl Dommy Mommy</option>
                            <option value="femboy">Soft Femboy</option>
                            <option value="dominant">Strict Dominant</option>
                            <option value="submissive">Submissive Pet</option>
                            <option value="musk">Heavy Musk</option>
                            <option value="pup">Pup Play</option>
                        </optgroup>
                        <optgroup label="Fetish Material">
                            <option value="rubber">Rubber Latex</option>
                            <option value="rope">Rope Bondage</option>
                            <option value="inflation">Inflation Bubble</option>
                            <option value="vore">Vore Jungle</option>
                            <option value="werewolf">Werewolf Moon</option>
                            <option value="hypno">Hypno Spiral</option>
                            <option value="daddy">Daddy Lounge</option>
                            <option value="hexcorp">Hexcorp Clinic</option>
                        </optgroup>
                    </select>
                    <label class="mature-toggle mono">
                        <input type="checkbox" data-theme-mature-toggle>
                        <span>Show mature themes</span>
                    </label>
                </div>
                <div class="extreme-themes-optgroup" aria-hidden="true">
                    <template>
                        <optgroup label="Extreme Kinks (opt-in required)">
                            <option value="guro">Guro Clinic</option>
                            <option value="living_toilet">Living Toilet</option>
                            <option value="parasites">Parasites Bloom</option>
                        </optgroup>
                    </template>
                </div>
            </div>
            <a href="{{ route('pricing') }}">Pricing <span class="free-badge mono">free!</span></a>
        </div>
        <a href="{{ route('login') }}"><button class="sign-in">Sign in</button></a>
    @endauth
</nav>
