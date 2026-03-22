@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $totalRevenue = (float) $user->total_revenue;
    $closedCommissions = (int) ($closedCount ?: $user->commission_count);
    $averageCommissionValue = $closedCommissions > 0 ? $totalRevenue / max(1, $closedCommissions) : 0;
    $followersCount = (int) ($user->subscriber_count ?? 0);
    $hasMeaningfulStats = $totalRevenue > 0 || $closedCommissions > 0 || $activeCount > 0 || $pendingRevenue > 0 || $followersCount > 0;
    $formattedLastEdited = $lastEditedAt ? $lastEditedAt->format('M j, Y') : null;
    $freshnessLabel = $lastEditedAt ? $lastEditedAt->diffForHumans() : 'No edits yet';
    $workspacePrimaryLabel = $activeCount > 0 ? 'Open workspace' : 'Set up workspace';
    $requestsPrimaryLabel = $newRequestsCount > 0 ? 'Review requests' : 'Check inbox';
    $profilePrimaryLabel = $lastEditedAt ? 'Refresh artist page' : 'Finish artist page';
@endphp

<div class="atelier-work-page">
    <div class="atelier-work-shell">
        <section class="atelier-work-hero">
            <div class="atelier-work-hero-copy atelier-panel">
                <div class="atelier-work-pill mono">Creator atelier</div>
                <h1 class="atelier-work-title">Your studio.</h1>
                <p class="atelier-work-subtitle">
                    Start with the next useful action, keep commissions moving, and keep your public page aligned with how you want clients to see you.
                </p>
                <div class="atelier-work-actions atelier-work-actions--inline">
                    <a href="{{ route('artist.workspace.show') }}" class="btn btn-primary atelier-work-action-primary">
                        {{ $workspacePrimaryLabel }} <span class="arrow">→</span>
                    </a>
                    <a href="{{ route('artist.requests.index') }}" class="btn {{ $newRequestsCount > 0 ? 'btn-secondary' : 'btn-ghost' }} atelier-work-action-secondary">
                        {{ $requestsPrimaryLabel }}
                    </a>
                    <a href="{{ url('/' . $user->username) }}" class="btn btn-ghost atelier-work-action-secondary">
                        {{ $profilePrimaryLabel }}
                    </a>
                </div>
            </div>

            <div class="atelier-hero-sidecard atelier-panel atelier-quick-panel">
                <div>
                    <div class="atelier-panel-kicker mono">Quick actions</div>
                    <h2 class="atelier-side-title serif">Useful next steps</h2>
                    <p class="atelier-side-body">
                        Keep this short and stateful. The goal is to surface what helps right now, not fill the card with decorative links.
                    </p>
                </div>

                <div class="atelier-quick-actions">
                    <a href="{{ url('/' . $user->username) }}" class="atelier-quick-link">
                        <span class="atelier-quick-label mono">Public page</span>
                        <strong>{{ $lastEditedAt ? 'Update profile details' : 'Finish your artist page' }}</strong>
                    </a>

                    @if($newRequestsCount > 0)
                        <a href="{{ route('artist.requests.index') }}" class="atelier-quick-link">
                            <span class="atelier-quick-label mono">Requests</span>
                            <strong>{{ $newRequestsCount }} waiting for a reply</strong>
                        </a>
                    @elseif($activeCount > 0)
                        <a href="{{ route('artist.workspace.show') }}" class="atelier-quick-link">
                            <span class="atelier-quick-label mono">Active work</span>
                            <strong>{{ $activeCount }} commission{{ $activeCount === 1 ? '' : 's' }} in motion</strong>
                        </a>
                    @else
                        <a href="{{ route('artist.workspace.show') }}" class="atelier-quick-link">
                            <span class="atelier-quick-label mono">Workspace</span>
                            <strong>Start organizing your next project</strong>
                        </a>
                    @endif

                    <a href="{{ url('/' . $user->username) }}" class="atelier-quick-link">
                        <span class="atelier-quick-label mono">Share</span>
                        <strong>Copy and share your public profile link</strong>
                    </a>
                </div>
            </div>
        </section>

        <section class="atelier-work-summary-grid">
            <div class="atelier-panel atelier-panel-featured">
                <div class="atelier-panel-kicker mono">Focus</div>
                <h2 class="serif atelier-panel-title">What matters most today</h2>
                <p class="atelier-panel-body">
                    Make one good choice first: answer waiting requests, move active work forward, or refresh the public page clients land on.
                </p>

                <div class="atelier-focus-list">
                    <a href="{{ route('artist.requests.index') }}" class="atelier-focus-item {{ $newRequestsCount > 0 ? 'is-priority' : '' }}">
                        <div>
                            <div class="atelier-focus-label mono">Incoming</div>
                            <div class="atelier-focus-title">Requests inbox <span class="atelier-inline-count">{{ $newRequestsCount }}</span></div>
                            <div class="atelier-focus-copy">
                                @if($newRequestsCount > 0)
                                    {{ $newRequestsCount }} new request{{ $newRequestsCount === 1 ? '' : 's' }} waiting for a response right now.
                                @else
                                    No unanswered requests at the moment — good. This lane is clear.
                                @endif
                            </div>
                        </div>
                        <span class="atelier-focus-arrow mono">Open →</span>
                    </a>

                    <a href="{{ route('artist.workspace.show') }}" class="atelier-focus-item {{ $activeCount > 0 && $newRequestsCount === 0 ? 'is-priority' : '' }}">
                        <div>
                            <div class="atelier-focus-label mono">Primary</div>
                            <div class="atelier-focus-title">Workspace <span class="atelier-inline-count">{{ $activeCount }}</span></div>
                            <div class="atelier-focus-copy">
                                @if($activeCount > 0)
                                    {{ $activeCount }} active commission{{ $activeCount === 1 ? '' : 's' }} currently in queue, progress, or delivery.
                                @else
                                    No commissions in motion yet. Use the workspace as your control room once work starts coming in.
                                @endif
                            </div>
                        </div>
                        <span class="atelier-focus-arrow mono">Open →</span>
                    </a>

                    <a href="{{ url('/' . $user->username) }}" class="atelier-focus-item {{ $lastEditedAt === null ? 'is-priority' : '' }}">
                        <div>
                            <div class="atelier-focus-label mono">Public</div>
                            <div class="atelier-focus-title">Artist page</div>
                            <div class="atelier-focus-copy">
                                @if($lastEditedAt)
                                    Last updated {{ $freshnessLabel }} on {{ $formattedLastEdited }}.
                                @else
                                    No profile edits yet. Finish the public page before sending people there.
                                @endif
                            </div>
                        </div>
                        <span class="atelier-focus-arrow mono">Edit →</span>
                    </a>
                </div>
            </div>

            <aside class="atelier-side-stack">
                <div class="atelier-panel atelier-stat-panel">
                    <div class="atelier-stat-heading">
                        <div>
                            <div class="atelier-panel-kicker mono">Snapshot</div>
                            <h3 class="atelier-side-title serif">Studio at a glance</h3>
                        </div>
                        <div class="atelier-stat-period mono">All time</div>
                    </div>

                    @if($hasMeaningfulStats)
                        <div class="atelier-stat-list">
                            @if($totalRevenue > 0)
                                <div class="atelier-stat-row atelier-stat-row-emphasis">
                                    <span class="atelier-stat-label mono">Revenue earned</span>
                                    <span class="atelier-stat-value serif">${{ number_format($totalRevenue, 2) }}</span>
                                </div>
                            @endif

                            @if($closedCommissions > 0)
                                <div class="atelier-stat-row">
                                    <span class="atelier-stat-label mono">Closed commissions</span>
                                    <span class="atelier-stat-value">{{ number_format($closedCommissions) }}</span>
                                </div>
                            @endif

                            <div class="atelier-stat-row">
                                <span class="atelier-stat-label mono">In progress</span>
                                <span class="atelier-stat-value">{{ number_format($activeCount) }}</span>
                            </div>

                            @if($pendingRevenue > 0)
                                <div class="atelier-stat-row">
                                    <span class="atelier-stat-label mono">Pending revenue</span>
                                    <span class="atelier-stat-value">${{ number_format($pendingRevenue, 2) }}</span>
                                </div>
                            @endif

                            @if($averageCommissionValue > 0)
                                <div class="atelier-stat-row">
                                    <span class="atelier-stat-label mono">Avg. commission value</span>
                                    <span class="atelier-stat-value">${{ number_format($averageCommissionValue, 2) }}</span>
                                </div>
                            @endif

                            @if($followersCount > 0)
                                <div class="atelier-stat-row">
                                    <span class="atelier-stat-label mono">Followers</span>
                                    <span class="atelier-stat-value">{{ number_format($followersCount) }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="atelier-zero-state">
                            <div class="atelier-zero-icon">◌</div>
                            <h3 class="atelier-zero-title serif">You’re still setting the room up.</h3>
                            <p class="atelier-zero-body">
                                No numbers worth staring at yet. Start by tightening your public page and answering requests quickly once they come in.
                            </p>
                        </div>
                    @endif
                </div>

                <div class="atelier-panel atelier-note-panel">
                    <div class="atelier-panel-kicker mono">Studio status</div>
                    <h3 class="serif atelier-note-title">A fast read on what needs attention.</h3>
                    <div class="atelier-status-stack">
                        <div class="atelier-status-chip">
                            <span class="mono">Requests waiting</span>
                            <strong>{{ $newRequestsCount > 0 ? $newRequestsCount : 'Clear' }}</strong>
                        </div>
                        <div class="atelier-status-chip">
                            <span class="mono">Commissions moving</span>
                            <strong>{{ $activeCount > 0 ? $activeCount : 'None yet' }}</strong>
                        </div>
                        <div class="atelier-status-chip">
                            <span class="mono">Page freshness</span>
                            <strong>{{ $lastEditedAt ? $freshnessLabel . ' · ' . $formattedLastEdited : 'No edits yet' }}</strong>
                        </div>
                    </div>
                </div>
            </aside>
        </section>
    </div>
</div>
@endsection

@push('scripts')
@endpush
