@extends('layouts.app')

@section('content')
<div class="play-feed-page">
    <div class="play-feed-shell">
        <section class="play-feed-banner">
            <div class="play-feed-banner-copy">
                <p class="play-feed-subtitle">The artists you follow, in order, without the sludge.</p>
            </div>
            <div class="play-feed-banner-actions">
                <a href="{{ route('browse') }}" class="btn btn-primary play-feed-primary-action">Find artists <span class="arrow">→</span></a>
                <a href="{{ route('commission.index') }}" class="btn btn-ghost play-feed-secondary-action">My requests</a>
            </div>
        </section>

        <section class="play-feed-layout">
            <main class="play-feed-main">
                <div class="play-feed-header-row">
                    <div>
                        <h1 class="play-feed-section-title">Artists you follow</h1>
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
                                        <h3 class="play-post-title">{{ $artist->name }}’s atelier</h3>
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
                    <div class="play-sidebar-header">
                        <div>
                            <div class="play-feed-kicker mono">Requests</div>
                            <div class="play-sidebar-title">Commission threads</div>
                        </div>
                        <a href="{{ route('commission.index') }}" class="play-sidebar-link mono">Open all</a>
                    </div>

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
                                    <div class="play-request-copy">
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
            </aside>
        </section>
    </div>
</div>
@endsection

@push('scripts')
@endpush
