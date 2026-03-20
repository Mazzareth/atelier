@extends('layouts.app')

@section('content')
<div class="play-feed-page">
    <div class="play-feed-shell">
        <section class="play-feed-hero play-feed-hero--compact">
            <div class="play-feed-hero-copy">
                <div class="pill mono play-feed-pill">
                    <div class="dot"></div>
                    ● following feed
                </div>
                <p class="play-feed-subtitle">
                    The artists you follow, in order, without the sludge.
                </p>
                <div class="play-feed-hero-actions play-feed-hero-actions--left">
                    <a href="{{ route('browse') }}" class="btn btn-primary play-feed-primary-action">Find artists <span class="arrow">→</span></a>
                    <a href="{{ route('commission.index') }}" class="btn btn-ghost play-feed-secondary-action">My requests</a>
                </div>
            </div>
        </section>

        <section class="play-feed-layout">
            <main class="play-feed-main">
                <div class="play-feed-header-row">
                    <div>
                        <h2 class="serif play-feed-section-title">Artists you follow</h2>
                    </div>
                    <div class="play-feed-count mono">{{ ($followedArtists ?? collect())->count() }} following</div>
                </div>

                @if(($followedArtists ?? collect())->count())
                    <div class="play-feed-stream">
                        @foreach($followedArtists as $artist)
                            @php
                                $avatarModule = $artist->profileModules->firstWhere('type', 'avatar_info');
                                $bioModule = $artist->profileModules->firstWhere('type', 'bio');
                                $galleryModule = $artist->profileModules->firstWhere('type', 'gallery_feed');
                                $slotsModule = $artist->profileModules->firstWhere('type', 'comm_slots');
                                $avatar = $avatarModule->settings['avatar'] ?? null;
                                $rawBio = $bioModule->settings['text'] ?? 'A beautiful little corner of Atelier, waiting to be explored.';
                                $bioPreview = trim(preg_replace('/[#*_`>\-\[\]\(\)!]+/', ' ', strip_tags($rawBio)));
                                $bioPreview = preg_replace('/\s+/', ' ', $bioPreview);
                                $layout = $galleryModule->settings['layout'] ?? 'grid';
                                $slotsOpen = (int) ($slotsModule->settings['slots_open'] ?? 0);
                                $isOpen = $slotsOpen > 0;
                            @endphp

                            <article class="play-post-card play-post-card--compact">
                                <div class="play-post-topbar">
                                    <a href="{{ route('artist.profile', $artist->username) }}" class="play-post-artist-link">
                                        <div class="play-post-avatar">
                                            @if($avatar)
                                                <img src="{{ $avatar }}" alt="{{ $artist->name }} avatar">
                                            @else
                                                <div class="play-post-avatar-fallback serif">{{ strtoupper(substr($artist->username ?? 'A', 0, 1)) }}</div>
                                            @endif
                                        </div>
                                        <div class="play-post-artist-meta">
                                            <div class="play-post-artist-name">{{ $artist->name }}</div>
                                            <div class="mono play-post-artist-handle">/{{ $artist->username }}</div>
                                        </div>
                                    </a>

                                    <div class="play-post-badges">
                                        <span class="play-post-badge mono">{{ number_format($artist->follower_count ?? 0) }} followers</span>
                                        <span class="play-post-badge mono">{{ $isOpen ? 'Open' : 'Closed' }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('artist.profile', $artist->username) }}" class="play-post-body play-post-body--compact">
                                    <div class="play-post-copy">
                                        <div class="mono play-post-label">Latest from their space</div>
                                        <h3 class="serif play-post-title">A better view into {{ $artist->name }}’s atelier</h3>
                                        <p class="play-post-text">{{ \Illuminate\Support\Str::limit($bioPreview, 160) }}</p>
                                    </div>

                                    <div class="play-post-preview play-post-preview--compact play-post-preview--{{ $layout }}">
                                        <div class="play-post-preview-card play-post-preview-card--large"></div>
                                        <div class="play-post-preview-card"></div>
                                        <div class="play-post-preview-card"></div>
                                    </div>
                                </a>

                                <div class="play-post-footer">
                                    <a href="{{ route('artist.profile', $artist->username) }}" class="play-post-footer-link mono">Visit profile →</a>
                                    <a href="{{ route('commission.create', $artist->username) }}" class="play-post-footer-link play-post-footer-link--accent mono">Request commission →</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="play-empty-state">
                        <div class="play-feed-kicker mono">Starting fresh</div>
                        <h3 class="serif play-empty-title">Your feed is ready for artists.</h3>
                        <p class="play-empty-copy">
                            Follow a few creators and this space becomes a clean, chronological feed of the people you actually want to keep up with.
                        </p>
                        <a href="{{ route('browse') }}" class="btn btn-primary">Browse artists <span class="arrow">→</span></a>
                    </div>
                @endif
            </main>

            <aside class="play-feed-sidebar">
                <section class="play-sidebar-panel">
                    <div class="play-feed-kicker mono">Requests</div>
                    <div class="play-sidebar-title serif">Commission threads</div>

                    @if(($myRequests ?? collect())->count())
                        <div class="play-request-list">
                            @foreach($myRequests as $requestItem)
                                @php
                                    $dashboardTrackerLabels = [
                                        \App\Models\CommissionRequest::TRACKER_QUEUE => 'Queued',
                                        \App\Models\CommissionRequest::TRACKER_ACTIVE => 'In Progress',
                                        \App\Models\CommissionRequest::TRACKER_DELIVERY => 'Review',
                                        \App\Models\CommissionRequest::TRACKER_DONE => 'Completed',
                                    ];
                                    $dashboardUnread = $requestItem->unreadCountFor(auth()->user());
                                    $statusLabel = $requestItem->tracker_stage
                                        ? ($dashboardTrackerLabels[$requestItem->tracker_stage] ?? $requestItem->tracker_stage)
                                        : str_replace('_', ' ', ucfirst($requestItem->status));
                                    $statusClass = match ($requestItem->tracker_stage ?: $requestItem->status) {
                                        \App\Models\CommissionRequest::TRACKER_DONE => 'is-done',
                                        \App\Models\CommissionRequest::TRACKER_DELIVERY => 'is-review',
                                        \App\Models\CommissionRequest::TRACKER_ACTIVE, \App\Models\CommissionRequest::STATUS_ACCEPTED => 'is-active',
                                        \App\Models\CommissionRequest::TRACKER_QUEUE, \App\Models\CommissionRequest::STATUS_PENDING => 'is-queued',
                                        default => 'is-muted',
                                    };
                                @endphp
                                <a href="{{ $requestItem->conversation ? route('conversations.show', $requestItem->conversation) : route('commission.show', $requestItem) }}" class="play-request-item">
                                    <div>
                                        <div class="mono play-request-handle">/{{ $requestItem->artist->username }}</div>
                                        <div class="play-request-title">{{ $requestItem->title }}</div>
                                        <div class="play-request-meta-row">
                                            <span class="play-request-status-badge {{ $statusClass }} mono">{{ strtoupper($statusLabel) }}</span>
                                        </div>
                                    </div>
                                    @if($dashboardUnread > 0)
                                        <span class="play-request-unread mono">{{ $dashboardUnread }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="play-sidebar-copy">No request threads yet. When you send a commission request, it’ll stay here so you can jump back into the conversation anytime.</p>
                    @endif
                </section>

                <section class="play-sidebar-panel">
                    <div class="play-feed-kicker mono">Suggested</div>
                    <div class="play-sidebar-title serif">Artists to follow</div>

                    @if(($suggestedArtists ?? collect())->count())
                        <div class="play-suggestion-list">
                            @foreach($suggestedArtists as $artist)
                                @php
                                    $avatarModule = $artist->profileModules->firstWhere('type', 'avatar_info');
                                    $bioModule = $artist->profileModules->firstWhere('type', 'bio');
                                    $avatar = $avatarModule->settings['avatar'] ?? null;
                                    $bio = trim(preg_replace('/\s+/', ' ', strip_tags($bioModule->settings['text'] ?? 'Artist on Atelier.')));
                                @endphp
                                <a href="{{ route('artist.profile', $artist->username) }}" class="play-suggestion-item">
                                    <div class="play-suggestion-avatar">
                                        @if($avatar)
                                            <img src="{{ $avatar }}" alt="{{ $artist->name }} avatar">
                                        @else
                                            <div class="play-suggestion-avatar-fallback serif">{{ strtoupper(substr($artist->username ?? 'A', 0, 1)) }}</div>
                                        @endif
                                    </div>
                                    <div class="play-suggestion-meta">
                                        <div class="play-suggestion-name">{{ $artist->name }}</div>
                                        <div class="mono play-suggestion-handle">/{{ $artist->username }}</div>
                                        <div class="play-suggestion-context">{{ \Illuminate\Support\Str::limit($bio, 60) }}</div>
                                    </div>
                                    <div class="mono play-suggestion-count">{{ number_format($artist->follower_count ?? 0) }}</div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="play-sidebar-copy">You’ve already found everyone currently being suggested.</p>
                    @endif
                </section>
            </aside>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .play-feed-page {
        min-height: 80vh;
        padding: 1.2rem 1.25rem 3rem;
    }

    .play-feed-shell {
        max-width: 1240px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .play-feed-hero,
    .play-sidebar-panel,
    .play-post-card,
    .play-empty-state {
        border: 1px solid color-mix(in srgb, var(--accent-color) 14%, var(--border-color));
        background: linear-gradient(180deg, color-mix(in srgb, var(--bg-panel) 96%, transparent), color-mix(in srgb, var(--bg-color) 18%, var(--bg-panel)));
        box-shadow: 0 18px 44px rgba(0,0,0,0.14);
    }

    .play-feed-hero {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        padding: 1rem 1.2rem;
        border-radius: 24px;
        background:
            radial-gradient(circle at top right, color-mix(in srgb, var(--accent-color) 9%, transparent), transparent 28%),
            linear-gradient(180deg, color-mix(in srgb, var(--bg-panel) 96%, transparent), color-mix(in srgb, var(--bg-color) 18%, var(--bg-panel)));
    }

    .play-feed-hero--compact .play-feed-title {
        display: none;
    }

    .play-feed-hero-copy {
        max-width: 760px;
    }

    .play-feed-pill {
        margin-bottom: 0.65rem;
        border-color: color-mix(in srgb, var(--accent-color) 48%, var(--border-color));
        background: color-mix(in srgb, var(--accent-color) 10%, transparent);
    }

    .play-feed-subtitle,
    .play-post-text,
    .play-sidebar-copy,
    .play-empty-copy,
    .play-suggestion-context {
        color: color-mix(in srgb, var(--text-muted) 78%, white 22%);
        line-height: 1.65;
        font-size: 0.95rem;
    }

    .play-feed-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .play-feed-hero-actions--left {
        justify-content: flex-start;
        margin-top: 0.85rem;
    }

    .play-feed-primary-action,
    .play-feed-secondary-action {
        padding: 0.78rem 1rem;
        border-radius: 14px;
    }

    .play-feed-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(300px, 0.82fr);
        gap: 1rem;
        align-items: start;
    }

    .play-feed-main,
    .play-feed-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .play-feed-header-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: end;
        padding: 0 0.2rem;
    }

    .play-feed-kicker,
    .play-feed-count,
    .play-post-label,
    .play-post-badge,
    .play-request-handle,
    .play-suggestion-handle,
    .play-suggestion-count {
        font-size: 0.66rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .play-feed-kicker,
    .play-post-label,
    .play-request-handle {
        color: var(--accent-color);
    }

    .play-feed-count,
    .play-post-badge,
    .play-suggestion-handle,
    .play-suggestion-count {
        color: var(--text-muted);
    }

    .play-feed-section-title,
    .play-sidebar-title,
    .play-empty-title {
        font-size: 1.45rem;
        margin-top: 0.2rem;
    }

    .play-feed-stream,
    .play-request-list,
    .play-suggestion-list {
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
    }

    .play-post-card,
    .play-sidebar-panel,
    .play-empty-state {
        border-radius: 24px;
        padding: 1rem 1.1rem;
    }

    .play-post-topbar,
    .play-post-footer {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .play-post-artist-link,
    .play-post-body,
    .play-request-item,
    .play-suggestion-item {
        text-decoration: none;
        color: inherit;
    }

    .play-post-artist-link {
        display: flex;
        gap: 0.9rem;
        align-items: center;
    }

    .play-post-avatar,
    .play-suggestion-avatar {
        width: 3rem;
        height: 3rem;
        border-radius: 999px;
        overflow: hidden;
        background: color-mix(in srgb, var(--bg-color) 68%, var(--bg-panel));
        border: 1px solid color-mix(in srgb, var(--accent-color) 20%, var(--border-color));
        flex-shrink: 0;
    }

    .play-post-avatar img,
    .play-suggestion-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .play-post-avatar-fallback,
    .play-suggestion-avatar-fallback {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        color: var(--accent-color);
        background: var(--accent-dim);
        font-size: 1.1rem;
    }

    .play-post-artist-name,
    .play-request-title,
    .play-suggestion-name {
        font-size: 1rem;
        color: var(--text-main);
    }

    .play-post-artist-handle {
        color: var(--text-muted);
        font-size: 0.68rem;
        margin-top: 0.2rem;
    }

    .play-post-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
    }

    .play-post-badge {
        padding: 0.38rem 0.58rem;
        border-radius: 999px;
        border: 1px solid var(--border-color);
        background: color-mix(in srgb, var(--bg-color) 32%, var(--bg-panel));
    }

    .play-post-body--compact {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(220px, 0.8fr);
        gap: 0.9rem;
        margin: 0.9rem 0;
        align-items: center;
    }

    .play-post-title {
        font-size: 1.28rem;
        margin: 0.3rem 0 0.45rem;
    }

    .play-post-preview {
        display: grid;
        gap: 0.55rem;
        padding: 0.75rem;
        border-radius: 18px;
        border: 1px solid color-mix(in srgb, var(--accent-color) 12%, var(--border-color));
        background:
            linear-gradient(180deg, color-mix(in srgb, var(--accent-color) 6%, var(--bg-panel)), color-mix(in srgb, var(--bg-color) 16%, var(--bg-panel))),
            linear-gradient(135deg, rgba(255,255,255,0.02), transparent 45%);
    }

    .play-post-preview--compact {
        min-height: 140px;
        grid-template-columns: 1.2fr 1fr;
        grid-template-rows: repeat(2, minmax(0, 1fr));
    }

    .play-post-preview-card {
        border-radius: 14px;
        background:
            linear-gradient(135deg, color-mix(in srgb, var(--accent-color) 18%, transparent), transparent 50%),
            linear-gradient(180deg, color-mix(in srgb, var(--bg-color) 24%, var(--bg-panel)), color-mix(in srgb, var(--bg-panel) 90%, transparent));
        border: 1px solid color-mix(in srgb, var(--accent-color) 10%, var(--border-color));
        min-height: 48px;
        position: relative;
        overflow: hidden;
    }

    .play-post-preview-card::after {
        content: '';
        position: absolute;
        inset: auto 0 0 0;
        height: 36%;
        background: linear-gradient(180deg, transparent, rgba(0,0,0,0.16));
    }

    .play-post-preview-card--large {
        grid-row: span 2;
    }

    .play-post-footer-link {
        color: color-mix(in srgb, var(--text-main) 72%, white 28%);
        font-size: 0.68rem;
    }

    .play-post-footer-link--accent {
        color: var(--accent-color);
    }

    .play-request-item,
    .play-suggestion-item {
        display: flex;
        justify-content: space-between;
        gap: 0.8rem;
        align-items: center;
        padding: 0.9rem;
        border-radius: 16px;
        background: color-mix(in srgb, var(--bg-color) 34%, var(--bg-panel));
        border: 1px solid var(--border-color);
    }

    .play-request-item {
        align-items: flex-start;
    }

    .play-request-title {
        margin: 0.22rem 0 0.45rem;
    }

    .play-request-meta-row {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        flex-wrap: wrap;
    }

    .play-request-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.34rem 0.55rem;
        border-radius: 999px;
        font-size: 0.62rem;
        letter-spacing: 0.08em;
        border: 1px solid transparent;
    }

    .play-request-status-badge.is-active {
        color: #8ef0b0;
        background: rgba(52, 166, 83, 0.16);
        border-color: rgba(52, 166, 83, 0.35);
    }

    .play-request-status-badge.is-queued,
    .play-request-status-badge.is-review {
        color: #f2d48a;
        background: rgba(202, 152, 54, 0.16);
        border-color: rgba(202, 152, 54, 0.35);
    }

    .play-request-status-badge.is-done {
        color: #86d8ff;
        background: rgba(65, 138, 201, 0.16);
        border-color: rgba(65, 138, 201, 0.35);
    }

    .play-request-status-badge.is-muted {
        color: var(--text-muted);
        background: rgba(255,255,255,0.04);
        border-color: var(--border-color);
    }

    .play-request-unread {
        min-width: 1.25rem;
        height: 1.25rem;
        padding: 0 0.35rem;
        border-radius: 999px;
        background: var(--accent-color);
        color: #000;
        font-size: 0.65rem;
        font-weight: 700;
        line-height: 1.25rem;
        text-align: center;
        flex-shrink: 0;
    }

    .play-suggestion-meta {
        min-width: 0;
        flex: 1;
    }

    .play-suggestion-context {
        margin-top: 0.25rem;
        font-size: 0.82rem;
    }

    .play-empty-state {
        text-align: left;
    }

    .play-empty-title {
        margin: 0.25rem 0 0.5rem;
    }

    @media (max-width: 1060px) {
        .play-feed-hero,
        .play-feed-layout,
        .play-post-body--compact {
            grid-template-columns: 1fr;
            flex-direction: column;
            align-items: stretch;
        }
    }

    @media (max-width: 720px) {
        .play-feed-page {
            padding-inline: 0.9rem;
        }

        .play-feed-hero,
        .play-post-card,
        .play-sidebar-panel,
        .play-empty-state {
            padding: 1rem;
            border-radius: 20px;
        }
    }
</style>
@endpush
