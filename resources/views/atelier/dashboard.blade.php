@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $totalRevenue = (float) $user->total_revenue;
    $averageCommissionValue = ($user->commission_count ?? 0) > 0 ? $totalRevenue / max(1, $user->commission_count) : 0;
@endphp

<div class="atelier-work-page">
    <div class="atelier-work-shell">
        <section class="atelier-work-hero">
            <div class="atelier-work-hero-copy">
                <div class="pill mono atelier-work-pill">
                    <div class="dot"></div>
                    ● creator atelier
                </div>
                <h1 class="serif atelier-work-title">
                    <span class="light">Your</span> <span class="highlight">work.</span>
                </h1>
                <p class="atelier-work-subtitle">
                    A calmer studio for handling requests, moving commissions forward, and keeping your page updated without everything shouting at once.
                </p>
                <div class="atelier-work-actions atelier-work-actions--inline">
                    <a href="{{ route('artist.workspace.show') }}" class="btn btn-primary atelier-work-action-primary">
                        Open workspace <span class="arrow">→</span>
                    </a>
                    <a href="{{ route('artist.requests.index') }}" class="btn btn-ghost atelier-work-action-secondary">
                        Review requests
                    </a>
                </div>
            </div>

            <div class="atelier-hero-sidecard atelier-panel">
                <div class="atelier-panel-kicker mono">Quick actions</div>
                <div class="atelier-quick-actions">
                    <a href="{{ url('/' . $user->username) }}" class="atelier-quick-link mono">Edit artist page →</a>
                    <a href="{{ url('/' . $user->username) }}" class="atelier-quick-link mono">Share profile link →</a>
                </div>
            </div>
        </section>

        <section class="atelier-work-summary-grid">
            <div class="atelier-panel atelier-panel-featured">
                <div class="atelier-panel-kicker mono">Now</div>
                <h2 class="serif atelier-panel-title">What matters most today</h2>
                <p class="atelier-panel-body">
                    Keep your active commission queue moving, answer new request threads quickly, and make sure your public page still reflects how you want clients to see you.
                </p>

                <div class="atelier-focus-list">
                    <a href="{{ route('artist.workspace.show') }}" class="atelier-focus-item">
                        <div>
                            <div class="atelier-focus-label mono">Primary</div>
                            <div class="atelier-focus-title">Workspace <span class="atelier-inline-count">{{ $activeCount }}</span></div>
                            <div class="atelier-focus-copy">{{ $activeCount }} active commission{{ $activeCount === 1 ? '' : 's' }} currently in queue, progress, or delivery.</div>
                        </div>
                        <span class="atelier-focus-arrow mono">Open →</span>
                    </a>

                    <a href="{{ route('artist.requests.index') }}" class="atelier-focus-item">
                        <div>
                            <div class="atelier-focus-label mono">Incoming</div>
                            <div class="atelier-focus-title">Requests inbox <span class="atelier-inline-count">{{ $newRequestsCount }}</span></div>
                            <div class="atelier-focus-copy">{{ $newRequestsCount }} new request{{ $newRequestsCount === 1 ? '' : 's' }} waiting for a response.</div>
                        </div>
                        <span class="atelier-focus-arrow mono">Open →</span>
                    </a>

                    <a href="{{ url('/' . $user->username) }}" class="atelier-focus-item">
                        <div>
                            <div class="atelier-focus-label mono">Public</div>
                            <div class="atelier-focus-title">Artist page</div>
                            <div class="atelier-focus-copy">
                                Last updated {{ $lastEditedAt ? $lastEditedAt->diffForHumans() : 'a while ago' }}.
                            </div>
                        </div>
                        <span class="atelier-focus-arrow mono">Edit →</span>
                    </a>
                </div>
            </div>

            <aside class="atelier-side-stack">
                <div class="atelier-panel atelier-stat-panel">
                    <div class="atelier-stat-heading">
                        <div class="atelier-panel-kicker mono">Snapshot</div>
                        <div class="atelier-stat-period mono">All time</div>
                    </div>
                    <div class="atelier-stat-list">
                        <div class="atelier-stat-row">
                            <span class="atelier-stat-label mono">Revenue</span>
                            <span class="atelier-stat-value serif">${{ number_format($totalRevenue, 2) }}</span>
                        </div>
                        <div class="atelier-stat-row">
                            <span class="atelier-stat-label mono">Closed commissions</span>
                            <span class="atelier-stat-value">{{ number_format($closedCount ?: $user->commission_count) }}</span>
                        </div>
                        <div class="atelier-stat-row">
                            <span class="atelier-stat-label mono">In progress</span>
                            <span class="atelier-stat-value">{{ number_format($activeCount) }}</span>
                        </div>
                        <div class="atelier-stat-row">
                            <span class="atelier-stat-label mono">Pending revenue</span>
                            <span class="atelier-stat-value">${{ number_format($pendingRevenue, 2) }}</span>
                        </div>
                        <div class="atelier-stat-row">
                            <span class="atelier-stat-label mono">Avg. commission value</span>
                            <span class="atelier-stat-value">${{ number_format($averageCommissionValue, 2) }}</span>
                        </div>
                        <div class="atelier-stat-row">
                            <span class="atelier-stat-label mono">Followers</span>
                            <span class="atelier-stat-value">{{ number_format($user->subscriber_count) }}</span>
                        </div>
                    </div>
                </div>

                <div class="atelier-panel atelier-note-panel">
                    <div class="atelier-panel-kicker mono">Studio status</div>
                    <h3 class="serif atelier-note-title">A better quick-read than a promo block.</h3>
                    <div class="atelier-status-stack">
                        <div class="atelier-status-chip">
                            <span class="mono">Requests waiting</span>
                            <strong>{{ $newRequestsCount }}</strong>
                        </div>
                        <div class="atelier-status-chip">
                            <span class="mono">Commissions moving</span>
                            <strong>{{ $activeCount }}</strong>
                        </div>
                        <div class="atelier-status-chip">
                            <span class="mono">Page freshness</span>
                            <strong>{{ $lastEditedAt ? $lastEditedAt->diffForHumans() : 'No edits yet' }}</strong>
                        </div>
                    </div>
                </div>
            </aside>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .atelier-work-page {
        min-height: 80vh;
        padding: 2rem 1.25rem 3rem;
    }

    .atelier-work-shell {
        max-width: 1180px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .atelier-panel {
        background: linear-gradient(180deg, color-mix(in srgb, var(--bg-panel) 95%, transparent), color-mix(in srgb, var(--bg-color) 14%, var(--bg-panel)));
        border: 1px solid color-mix(in srgb, var(--accent-color) 12%, var(--border-color));
        border-radius: 22px;
        padding: 1.35rem;
        box-shadow: 0 16px 40px rgba(0,0,0,0.14);
    }

    .atelier-work-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(280px, 0.72fr);
        gap: 1rem;
        align-items: stretch;
    }

    .atelier-work-hero-copy {
        padding: 1.5rem;
        border: 1px solid color-mix(in srgb, var(--accent-color) 18%, var(--border-color));
        border-radius: 26px;
        background:
            radial-gradient(circle at top right, color-mix(in srgb, var(--accent-color) 8%, transparent), transparent 30%),
            linear-gradient(180deg, color-mix(in srgb, var(--bg-panel) 96%, transparent), color-mix(in srgb, var(--bg-color) 18%, var(--bg-panel)));
        box-shadow: 0 24px 60px rgba(0,0,0,0.16);
    }

    .atelier-work-pill {
        margin-bottom: 0.85rem;
        border-color: color-mix(in srgb, var(--accent-color) 48%, var(--border-color));
        background: color-mix(in srgb, var(--accent-color) 10%, transparent);
    }

    .atelier-work-title {
        font-size: clamp(2.3rem, 4vw, 3.7rem);
        line-height: 0.98;
        margin-bottom: 0.7rem;
    }

    .atelier-work-subtitle,
    .atelier-panel-body,
    .atelier-focus-copy {
        color: color-mix(in srgb, var(--text-muted) 78%, white 22%);
        line-height: 1.65;
        font-size: 0.96rem;
    }

    .atelier-work-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .atelier-work-actions--inline {
        margin-top: 1.1rem;
    }

    .atelier-work-action-primary,
    .atelier-work-action-secondary {
        padding: 0.82rem 1rem;
        border-radius: 14px;
    }

    .atelier-hero-sidecard {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .atelier-quick-actions {
        display: flex;
        flex-direction: column;
        gap: 0.7rem;
        margin-top: 1rem;
    }

    .atelier-quick-link {
        text-decoration: none;
        color: var(--text-main);
        padding: 0.85rem 0.95rem;
        border-radius: 16px;
        border: 1px solid var(--border-color);
        background: color-mix(in srgb, var(--bg-color) 32%, var(--bg-panel));
        font-size: 0.72rem;
        text-transform: uppercase;
    }

    .atelier-work-summary-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(300px, 0.8fr);
        gap: 1rem;
        align-items: start;
    }

    .atelier-panel-kicker,
    .atelier-focus-label,
    .atelier-stat-label {
        font-size: 0.62rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--accent-color);
    }

    .atelier-panel-title {
        font-size: 1.7rem;
        margin: 0.25rem 0 0.5rem;
    }

    .atelier-focus-list,
    .atelier-side-stack,
    .atelier-stat-list,
    .atelier-status-stack {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .atelier-focus-item {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        text-decoration: none;
        color: inherit;
        padding: 1rem;
        border-radius: 18px;
        background: color-mix(in srgb, var(--bg-color) 32%, var(--bg-panel));
        border: 1px solid var(--border-color);
        transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
    }

    .atelier-focus-item:hover {
        transform: translateY(-2px);
        border-color: color-mix(in srgb, var(--accent-color) 34%, var(--border-color));
        box-shadow: 0 14px 28px rgba(0,0,0,0.14);
    }

    .atelier-focus-title,
    .atelier-note-title {
        color: var(--text-main);
    }

    .atelier-focus-title {
        font-size: 1rem;
        margin: 0.18rem 0 0.25rem;
    }

    .atelier-inline-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.35rem;
        height: 1.35rem;
        margin-left: 0.45rem;
        padding: 0 0.42rem;
        border-radius: 999px;
        background: var(--accent-dim);
        color: var(--accent-color);
        border: 1px solid color-mix(in srgb, var(--accent-color) 44%, var(--border-color));
        font-size: 0.78rem;
        vertical-align: middle;
    }

    .atelier-focus-arrow,
    .atelier-stat-period {
        color: var(--text-muted);
        white-space: nowrap;
        flex-shrink: 0;
        font-size: 0.68rem;
        text-transform: uppercase;
    }

    .atelier-stat-heading {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        margin-bottom: 0.8rem;
    }

    .atelier-stat-row,
    .atelier-status-chip {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: baseline;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid color-mix(in srgb, var(--border-color) 85%, transparent);
    }

    .atelier-status-chip:last-child,
    .atelier-stat-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .atelier-stat-value,
    .atelier-status-chip strong {
        color: var(--text-main);
        font-size: 1rem;
        font-weight: 600;
        text-align: right;
    }

    .atelier-stat-panel .atelier-stat-row:first-child .atelier-stat-value {
        font-size: 1.75rem;
        line-height: 1;
        font-weight: 400;
    }

    .atelier-note-title {
        font-size: 1.35rem;
        margin: 0.2rem 0 0.45rem;
    }

    .atelier-status-chip span {
        color: var(--text-muted);
        font-size: 0.65rem;
        text-transform: uppercase;
    }

    @media (max-width: 1040px) {
        .atelier-work-hero,
        .atelier-work-summary-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .atelier-work-page {
            padding-inline: 0.9rem;
        }

        .atelier-work-hero-copy,
        .atelier-panel {
            padding: 1rem;
            border-radius: 20px;
        }

        .atelier-focus-item {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush
