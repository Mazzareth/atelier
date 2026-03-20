@extends('layouts.app')

@section('content')
<div class="browse-page-shell">
    <section class="browse-hero-card">
        <div>
            <span class="pill"><span class="dot"></span> Browse Ateliers</span>
            <h1 class="serif browse-title">Discover <span class="highlight">Artists</span></h1>
            <p class="browse-subtitle">Find creators by style, availability, and the kind of work they actually make.</p>
        </div>

        <form action="{{ route('browse') }}" method="GET" class="browse-search-form">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search artist name, username, or vibe..."
                class="mono browse-search-input"
            >
            <select name="availability" class="mono browse-filter-select">
                <option value="">Any availability</option>
                <option value="open" @selected(request('availability') === 'open')>Open for commissions</option>
                <option value="closed" @selected(request('availability') === 'closed')>Closed / no slots</option>
            </select>
            <select name="tag" class="mono browse-filter-select">
                <option value="">Any tag</option>
                @foreach(['furry', 'nsfw', 'sfw', 'character', 'design', 'anime', 'queer'] as $tag)
                    <option value="{{ $tag }}" @selected(request('tag') === $tag)>{{ ucfirst($tag) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </section>

    <section class="browse-results-bar">
        <div class="mono browse-results-count">Showing {{ $artists->count() }} of {{ $totalArtists }} artists</div>
        @if(request()->query())
            <a href="{{ route('browse') }}" class="mono browse-clear-link">Clear filters</a>
        @endif
    </section>

    @if($artists->isEmpty())
        <div class="browse-empty-state">
            <p class="mono">No artists found matching your criteria.</p>
            <a href="{{ route('browse') }}" class="browse-clear-link">Clear search</a>
        </div>
    @else
        <div class="browse-card-list">
            @foreach($artists as $artist)
                @php
                    $avatarModule = $artist->profileModules->firstWhere('type', 'avatar_info');
                    $bioModule = $artist->profileModules->firstWhere('type', 'bio');
                    $galleryModule = $artist->profileModules->firstWhere('type', 'gallery_feed');
                    $slotsModule = $artist->profileModules->firstWhere('type', 'comm_slots');
                    $avatar = $avatarModule->settings['avatar'] ?? null;
                    $bio = trim(preg_replace('/\s+/', ' ', preg_replace('/[#*_`>\-\[\]\(\)!]+/', ' ', strip_tags($bioModule->settings['text'] ?? 'Artist on Atelier.'))));
                    $layout = $galleryModule->settings['layout'] ?? 'grid';
                    $slotsOpen = (int) ($slotsModule->settings['slots_open'] ?? 0);
                    $isOpen = $slotsOpen > 0;
                    $isFollowing = auth()->check()
                        ? \Illuminate\Support\Facades\DB::table('followers')->where('user_id', $artist->id)->where('follower_id', auth()->id())->exists()
                        : false;
                    $tags = collect(['furry', 'nsfw', 'sfw', 'character design', 'queer'])
                        ->filter(fn ($tag) => str_contains(strtolower($bio), strtolower($tag)) || str_contains(strtolower($artist->name), strtolower($tag)));
                    if ($tags->isEmpty()) {
                        $tags = collect([$layout === 'grid' ? 'portfolio grid' : $layout . ' portfolio']);
                    }
                @endphp

                <article class="browse-artist-card" data-artist-card>
                    <a href="{{ route('artist.profile', $artist->username) }}" class="browse-artist-mainlink">
                        <div class="browse-artist-top">
                            <div class="browse-artist-avatar">
                                @if($avatar)
                                    <img src="{{ $avatar }}" alt="{{ $artist->name }} avatar">
                                @else
                                    <div class="browse-artist-avatar-fallback serif">{{ strtoupper(substr($artist->username ?? 'A', 0, 1)) }}</div>
                                @endif
                            </div>

                            <div class="browse-artist-heading">
                                <div class="browse-artist-name-row">
                                    <h3 class="serif browse-artist-name">{{ $artist->name }}</h3>
                                    <span class="browse-availability-badge {{ $isOpen ? 'is-open' : 'is-closed' }} mono">{{ $isOpen ? 'Open' : 'Closed' }}</span>
                                </div>
                                <div class="mono browse-artist-handle">/{{ $artist->username }}</div>
                                <p class="browse-artist-bio">{{ \Illuminate\Support\Str::limit($bio, 130) }}</p>
                                <div class="browse-tag-row">
                                    @foreach($tags->take(3) as $tag)
                                        <span class="browse-tag mono">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="browse-portfolio-strip">
                            <div class="browse-thumb browse-thumb--large"></div>
                            <div class="browse-thumb"></div>
                            <div class="browse-thumb"></div>
                        </div>
                    </a>

                    <div class="browse-artist-footer">
                        <div class="browse-artist-metrics">
                            <div>
                                <div class="mono browse-metric-label">Followers</div>
                                <div class="browse-metric-value" data-follower-count>{{ number_format($artist->follower_count) }}</div>
                            </div>
                            <div>
                                <div class="mono browse-metric-label">Portfolio</div>
                                <div class="browse-metric-value">{{ strtoupper($layout) }}</div>
                            </div>
                            <div>
                                <div class="mono browse-metric-label">Availability</div>
                                <div class="browse-metric-value">{{ $isOpen ? ($slotsOpen . ' slots') : 'Waitlist' }}</div>
                            </div>
                        </div>
                        @auth
                            <div class="browse-artist-actions">
                                <button
                                    type="button"
                                    class="btn {{ $isFollowing ? 'btn-secondary' : 'btn-ghost' }} browse-card-button browse-follow-button"
                                    data-follow-btn
                                    data-username="{{ $artist->username }}"
                                >{{ $isFollowing ? 'Unfollow' : 'Follow' }}</button>
                                <a href="{{ route('conversations.start', $artist->username) }}" class="btn btn-ghost browse-card-button">Chat</a>
                                @if($isOpen)
                                    <a href="{{ route('commission.create', $artist->username) }}" class="btn btn-primary browse-card-button">Request</a>
                                @endif
                            </div>
                        @endauth
                    </div>
                </article>
            @endforeach
        </div>

        <div class="browse-pagination-wrap">
            {{ $artists->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<style>
    .browse-page-shell {
        max-width: 1180px;
        margin: 0 auto;
        padding: 1.5rem 1.25rem 3rem;
        min-height: 80vh;
    }

    .browse-hero-card,
    .browse-artist-card,
    .browse-empty-state {
        background: linear-gradient(180deg, color-mix(in srgb, var(--bg-panel) 96%, transparent), color-mix(in srgb, var(--bg-color) 14%, var(--bg-panel)));
        border: 1px solid color-mix(in srgb, var(--accent-color) 14%, var(--border-color));
        border-radius: 24px;
        box-shadow: 0 18px 44px rgba(0,0,0,0.14);
    }

    .browse-hero-card {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(300px, 0.9fr);
        gap: 1.25rem;
        align-items: center;
        padding: 1.55rem;
        margin-bottom: 1rem;
        position: relative;
        overflow: hidden;
    }

    .browse-hero-card::after {
        content: '';
        position: absolute;
        inset: auto -80px -120px auto;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: radial-gradient(circle, color-mix(in srgb, var(--accent-color) 18%, transparent), transparent 68%);
        pointer-events: none;
    }

    .browse-title {
        font-size: clamp(2.4rem, 4vw, 3.7rem);
        margin: 0.8rem 0 0.55rem;
    }

    .browse-subtitle,
    .browse-artist-bio {
        color: color-mix(in srgb, var(--text-muted) 78%, white 22%);
        line-height: 1.6;
    }

    .browse-search-form {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 1fr;
        gap: 0.65rem;
        align-self: stretch;
    }

    .browse-search-input,
    .browse-filter-select {
        background: var(--bg-panel);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        padding: 0.85rem 1rem;
        border-radius: 14px;
        outline: none;
    }

    .browse-search-input {
        min-width: 0;
        grid-column: 1 / -1;
    }

    .browse-search-form .btn {
        width: 100%;
        justify-content: center;
        border-radius: 14px;
    }

    .browse-results-bar {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        padding: 0 0.2rem 1rem;
    }

    .browse-results-count,
    .browse-clear-link,
    .browse-metric-label,
    .browse-tag,
    .browse-artist-handle,
    .browse-availability-badge {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .browse-clear-link {
        color: var(--accent-color);
        text-decoration: none;
    }

    .browse-card-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .browse-artist-card {
        padding: 1.15rem;
    }

    .browse-artist-mainlink {
        color: inherit;
        text-decoration: none;
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(220px, 0.7fr);
        gap: 0.95rem;
        align-items: stretch;
    }

    .browse-artist-top {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .browse-artist-avatar {
        width: 4.75rem;
        height: 4.75rem;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        border: 1px solid color-mix(in srgb, var(--accent-color) 20%, var(--border-color));
        background: color-mix(in srgb, var(--bg-color) 50%, var(--bg-panel));
    }

    .browse-artist-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .browse-artist-avatar-fallback {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        color: var(--accent-color);
        background: var(--accent-dim);
        font-size: 1.4rem;
    }

    .browse-artist-heading {
        min-width: 0;
        flex: 1;
    }

    .browse-artist-name-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        align-items: center;
    }

    .browse-artist-name {
        font-size: 1.5rem;
        margin: 0;
    }

    .browse-artist-handle {
        color: var(--accent-color);
        margin-top: 0.2rem;
    }

    .browse-availability-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.36rem 0.58rem;
        border-radius: 999px;
        border: 1px solid transparent;
    }

    .browse-availability-badge.is-open {
        color: #8ef0b0;
        background: rgba(52, 166, 83, 0.16);
        border-color: rgba(52, 166, 83, 0.35);
    }

    .browse-availability-badge.is-closed {
        color: #f3b4b4;
        background: rgba(189, 78, 78, 0.16);
        border-color: rgba(189, 78, 78, 0.35);
    }

    .browse-artist-bio {
        margin-top: 0.55rem;
        font-size: 0.95rem;
        max-width: 58ch;
    }

    .browse-tag-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        margin-top: 0.7rem;
    }

    .browse-tag {
        padding: 0.34rem 0.55rem;
        border-radius: 999px;
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        background: color-mix(in srgb, var(--bg-color) 35%, var(--bg-panel));
    }

    .browse-portfolio-strip {
        display: grid;
        grid-template-columns: 1fr;
        grid-template-rows: 1.35fr 1fr 1fr;
        gap: 0.55rem;
        min-height: 170px;
        padding: 0.7rem;
        border-radius: 20px;
        border: 1px solid color-mix(in srgb, var(--accent-color) 12%, var(--border-color));
        background: linear-gradient(180deg, color-mix(in srgb, var(--accent-color) 6%, var(--bg-panel)), color-mix(in srgb, var(--bg-color) 14%, var(--bg-panel)));
    }

    .browse-thumb {
        border-radius: 14px;
        min-height: 0;
        border: 1px solid color-mix(in srgb, var(--accent-color) 10%, var(--border-color));
        background:
            linear-gradient(135deg, color-mix(in srgb, var(--accent-color) 18%, transparent), transparent 50%),
            linear-gradient(180deg, color-mix(in srgb, var(--bg-color) 24%, var(--bg-panel)), color-mix(in srgb, var(--bg-panel) 90%, transparent));
    }

    .browse-thumb--large {
        min-height: 88px;
    }

    .browse-artist-footer {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        margin-top: 0.95rem;
        padding-top: 0.95rem;
        border-top: 1px solid color-mix(in srgb, var(--border-color) 84%, transparent);
        flex-wrap: wrap;
    }

    .browse-artist-metrics {
        display: flex;
        flex-wrap: wrap;
        gap: 1.2rem;
    }

    .browse-metric-label {
        color: var(--text-muted);
        margin-bottom: 0.2rem;
    }

    .browse-metric-value {
        color: var(--text-main);
        font-size: 0.95rem;
    }

    .browse-artist-actions {
        display: flex;
        gap: 0.55rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .browse-card-button {
        min-width: 88px;
        padding: 0.65rem 0.95rem;
        border-radius: 12px;
        font-size: 0.82rem;
        justify-content: center;
    }

    .browse-empty-state {
        text-align: center;
        padding: 6rem 1rem;
    }

    .browse-pagination-wrap {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
    }

    @media (max-width: 980px) {
        .browse-hero-card,
        .browse-artist-mainlink {
            grid-template-columns: 1fr;
            align-items: stretch;
        }

        .browse-search-form { grid-template-columns: 1fr; }
        .browse-portfolio-strip { grid-template-columns: 1.25fr 1fr 1fr; grid-template-rows: 1fr; min-height: 110px; }
    }

    @media (max-width: 640px) {
        .browse-page-shell {
            padding-inline: 0.9rem;
        }

        .browse-hero-card,
        .browse-artist-card,
        .browse-empty-state {
            padding: 1rem;
            border-radius: 20px;
        }

        .browse-artist-top {
            flex-direction: column;
        }

        .browse-artist-footer,
        .browse-artist-actions {
            align-items: stretch;
            justify-content: flex-start;
        }

        .browse-card-button {
            width: 100%;
        }
    }
</style>
<script>
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('[data-follow-btn]');
        if (!btn) return;

        const username = btn.dataset.username;
        const card = btn.closest('[data-artist-card]');
        const countDisplay = card?.querySelector('[data-follower-count]');

        btn.disabled = true;

        try {
            const res = await fetch(`/follow/${username}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (res.status === 401) {
                window.location.href = '/login';
                return;
            }

            const data = await res.json();

            if (data.status === 'followed') {
                btn.textContent = 'Unfollow';
                btn.classList.remove('btn-ghost');
                btn.classList.add('btn-secondary');
            } else if (data.status === 'unfollowed') {
                btn.textContent = 'Follow';
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-ghost');
            }

            if (countDisplay && typeof data.follower_count !== 'undefined') {
                countDisplay.textContent = Number(data.follower_count).toLocaleString();
            }
        } catch (err) {
            console.error(err);
        } finally {
            btn.disabled = false;
        }
    });
</script>
@endpush
