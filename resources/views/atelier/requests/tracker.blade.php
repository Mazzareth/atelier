@extends('layouts.app')

@php
    $trackerLabels = [
        \App\Models\CommissionRequest::TRACKER_QUEUE => 'Queued',
        \App\Models\CommissionRequest::TRACKER_ACTIVE => 'In Progress',
        \App\Models\CommissionRequest::TRACKER_DELIVERY => 'Review / Delivery',
        \App\Models\CommissionRequest::TRACKER_DONE => 'Completed',
    ];

    $trackerHints = [
        \App\Models\CommissionRequest::TRACKER_QUEUE => 'Accepted and waiting their turn.',
        \App\Models\CommissionRequest::TRACKER_ACTIVE => 'Currently being worked on.',
        \App\Models\CommissionRequest::TRACKER_DELIVERY => 'Sent out or waiting on tweaks.',
        \App\Models\CommissionRequest::TRACKER_DONE => 'Wrapped and fully delivered.',
    ];

    $trackerColors = [
        \App\Models\CommissionRequest::TRACKER_QUEUE => '#8ec5ff',
        \App\Models\CommissionRequest::TRACKER_ACTIVE => 'var(--accent-color)',
        \App\Models\CommissionRequest::TRACKER_DELIVERY => '#ffd166',
        \App\Models\CommissionRequest::TRACKER_DONE => '#52d273',
    ];
@endphp

@section('content')
<div style="min-height: 80vh; padding: 3rem 1.25rem;">
    <div style="max-width: 1450px; margin: 0 auto;">
        <div style="display:flex; justify-content:space-between; gap:1rem; align-items:flex-end; margin-bottom: 2rem; flex-wrap: wrap; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
            <div>
                <div class="pill mono" style="margin-bottom: 1rem; border-color: var(--accent-color); color: var(--accent-color); background: var(--accent-dim);">
                    <div class="dot" style="background: var(--accent-color);"></div>
                    ● commission tracker studio
                </div>
                <h1 class="serif" style="font-size: 3rem; margin-bottom: 0.5rem;">
                    <span class="light">Manage</span> <span class="highlight">accepted commissions.</span>
                </h1>
                <p class="mono" style="color: var(--text-muted); max-width: 760px; line-height: 1.7;">This is the full-page queue manager. Accepted requests live here, and whatever stage you assign here shows up in the public Commission Tracker module on your page.</p>
            </div>
            <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                <a href="{{ route('artist.requests.index') }}" class="btn btn-ghost">Review Requests</a>
                <a href="{{ url('/' . auth()->user()->username) }}" class="btn btn-primary">View Public Page</a>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; margin-bottom: 1.25rem;" class="tracker-stats-grid">
            <div style="background: var(--bg-panel); border:1px solid var(--border-color); border-radius:16px; padding:1rem 1.15rem;">
                <div class="mono" style="font-size:0.68rem; color:var(--text-muted); text-transform:uppercase; margin-bottom:0.45rem;">Accepted total</div>
                <div class="serif" style="font-size:2rem;">{{ $acceptedRequests->count() }}</div>
            </div>
            <div style="background: var(--bg-panel); border:1px solid var(--border-color); border-radius:16px; padding:1rem 1.15rem;">
                <div class="mono" style="font-size:0.68rem; color:var(--text-muted); text-transform:uppercase; margin-bottom:0.45rem;">Visible on tracker</div>
                <div class="serif" style="font-size:2rem;">{{ $acceptedRequests->whereNotNull('tracker_stage')->count() }}</div>
            </div>
            <div style="background: var(--bg-panel); border:1px solid var(--border-color); border-radius:16px; padding:1rem 1.15rem;">
                <div class="mono" style="font-size:0.68rem; color:var(--text-muted); text-transform:uppercase; margin-bottom:0.45rem;">Not yet placed</div>
                <div class="serif" style="font-size:2rem;">{{ $untrackedRequests->count() }}</div>
            </div>
            <div style="background: var(--bg-panel); border:1px solid var(--border-color); border-radius:16px; padding:1rem 1.15rem;">
                <div class="mono" style="font-size:0.68rem; color:var(--text-muted); text-transform:uppercase; margin-bottom:0.45rem;">Currently active</div>
                <div class="serif" style="font-size:2rem;">{{ ($trackerGroups[\App\Models\CommissionRequest::TRACKER_ACTIVE] ?? collect())->count() }}</div>
            </div>
        </div>

        @if($untrackedRequests->count())
            <div style="background: var(--bg-panel); border:1px solid var(--border-color); border-radius:16px; padding:1.25rem; margin-bottom: 1.25rem;">
                <div style="display:flex; justify-content:space-between; gap:1rem; align-items:flex-start; flex-wrap:wrap; margin-bottom: 1rem;">
                    <div>
                        <div class="mono" style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; margin-bottom:0.35rem;">Ready to place</div>
                        <div class="serif" style="font-size:1.7rem;">Accepted, but not on the tracker yet</div>
                    </div>
                    <div class="mono" style="font-size:0.72rem; color:var(--text-muted); max-width:460px; line-height:1.6;">If you want a commission to appear publicly, drop it into a stage here. If you’d rather keep it off the public queue for now, just leave it unplaced.</div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem;" class="untracked-grid">
                    @foreach($untrackedRequests as $requestItem)
                        <div style="background: var(--bg-color); border:1px solid var(--border-color); border-radius:16px; padding:1rem;">
                            <div style="display:flex; justify-content:space-between; gap:1rem; align-items:flex-start; margin-bottom:0.75rem;">
                                <div style="min-width:0;">
                                    <div style="font-size:1.1rem; margin-bottom:0.25rem;">{{ $requestItem->title }}</div>
                                    <div class="mono" style="font-size:0.68rem; color:var(--text-muted); text-transform:uppercase;">for {{ $requestItem->requester->name }} /{{ $requestItem->requester->username }}</div>
                                </div>
                                <a href="{{ route('commission.show', $requestItem) }}" class="btn btn-ghost" style="font-size:0.68rem; padding:0.45rem 0.7rem; white-space:nowrap;">Open Thread</a>
                            </div>
                            <div style="color:var(--text-muted); line-height:1.65; margin-bottom:1rem; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">{{ $requestItem->details }}</div>
                            <div style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap:0.55rem;">
                                @foreach($trackerLabels as $stage => $label)
                                    <form method="POST" action="{{ route('artist.requests.tracker-stage', $requestItem) }}">
                                        @csrf
                                        <input type="hidden" name="tracker_stage" value="{{ $stage }}">
                                        <button class="btn btn-ghost" style="width:100%; justify-content:center; font-size:0.68rem; padding:0.55rem 0.45rem; border-color: {{ $trackerColors[$stage] }}; color: {{ $trackerColors[$stage] }};">{{ $label }}</button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; align-items:start;" class="tracker-board-grid">
            @foreach($trackerLabels as $stage => $label)
                @php $items = $trackerGroups[$stage] ?? collect(); @endphp
                <section class="tracker-column" data-stage="{{ $stage }}" style="background: linear-gradient(180deg, color-mix(in srgb, {{ $trackerColors[$stage] }} 10%, var(--bg-panel)), var(--bg-panel)); border:1px solid color-mix(in srgb, {{ $trackerColors[$stage] }} 35%, var(--border-color)); border-radius:20px; padding:1rem; min-width:0; box-shadow: 0 18px 40px rgba(0,0,0,0.18);">
                    <div style="display:flex; justify-content:space-between; gap:0.75rem; align-items:center; margin-bottom:0.45rem;">
                        <div class="mono" style="font-size:0.68rem; color: {{ $trackerColors[$stage] }}; text-transform:uppercase; letter-spacing:0.08em;">{{ $label }}</div>
                        <div class="mono tracker-column-count" style="min-width:1.65rem; height:1.65rem; border-radius:999px; background: color-mix(in srgb, {{ $trackerColors[$stage] }} 18%, transparent); color: {{ $trackerColors[$stage] }}; font-size:0.68rem; font-weight:700; display:grid; place-items:center;">{{ $items->count() }}</div>
                    </div>
                    <div class="mono" style="font-size:0.62rem; color:var(--text-muted); text-transform:uppercase; margin-bottom:1rem;">{{ $trackerHints[$stage] }}</div>

                    <div class="tracker-dropzone" data-stage="{{ $stage }}" style="display:flex; flex-direction:column; gap:0.9rem; min-height:220px; border-radius:16px; transition: background 0.2s ease, border-color 0.2s ease;">
                        @forelse($items as $requestItem)
                            <article class="tracker-card" draggable="true" data-request-id="{{ $requestItem->id }}" data-current-stage="{{ $stage }}" style="background: linear-gradient(180deg, color-mix(in srgb, var(--bg-color) 94%, white 2%), var(--bg-color)); border:1px solid var(--border-color); border-radius:18px; padding:1rem; cursor:grab; box-shadow: 0 12px 24px rgba(0,0,0,0.15); transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;">
                                <div style="display:flex; justify-content:space-between; gap:0.75rem; align-items:flex-start; margin-bottom:0.75rem;">
                                    <div style="min-width:0; flex:1;">
                                        <div style="font-size:1rem; line-height:1.4; margin-bottom:0.3rem; color:var(--text-main);">{{ $requestItem->title }}</div>
                                        <div class="mono" style="font-size:0.64rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">for {{ $requestItem->requester->name }}</div>
                                    </div>
                                    <a href="{{ route('commission.show', $requestItem) }}" class="mono" style="font-size:0.64rem; color:var(--accent-color); text-decoration:none; white-space:nowrap;">Thread →</a>
                                </div>

                                <div style="display:flex; gap:0.45rem; flex-wrap:wrap; margin-bottom:0.65rem;">
                                    @if($requestItem->budget)
                                        <span class="mono" style="font-size:0.62rem; color:var(--text-main); padding:0.35rem 0.55rem; border-radius:999px; background:color-mix(in srgb, var(--bg-panel) 75%, transparent); border:1px solid var(--border-color);">${{ number_format($requestItem->budget, 2) }}</span>
                                    @endif
                                    <span class="mono" style="font-size:0.62rem; color: {{ $trackerColors[$stage] }}; padding:0.35rem 0.55rem; border-radius:999px; background: color-mix(in srgb, {{ $trackerColors[$stage] }} 12%, transparent); border:1px solid color-mix(in srgb, {{ $trackerColors[$stage] }} 35%, var(--border-color));">{{ $label }}</span>
                                </div>

                                <div style="color:var(--text-muted); line-height:1.6; margin-bottom:0.85rem; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical;">{{ $requestItem->details }}</div>

                                <div style="display:flex; justify-content:space-between; gap:0.75rem; align-items:center;">
                                    <div class="mono" style="font-size:0.62rem; color: {{ $trackerColors[$stage] }};">Updated {{ optional($requestItem->tracker_stage_updated_at ?? $requestItem->updated_at)->diffForHumans() }}</div>
                                    <div class="mono" style="font-size:0.58rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.08em;">Drag me</div>
                                </div>

                                <form method="POST" action="{{ route('artist.requests.tracker-stage', $requestItem) }}" class="tracker-stage-form" style="display:none;">
                                    @csrf
                                    <input type="hidden" name="tracker_stage" value="{{ $stage }}" class="tracker-stage-input">
                                </form>
                            </article>
                        @empty
                            <div class="tracker-empty-state" style="min-height:140px; border-radius:16px; border:1px dashed color-mix(in srgb, {{ $trackerColors[$stage] }} 35%, var(--border-color)); display:flex; align-items:center; justify-content:center; padding:1rem; text-align:center; background: color-mix(in srgb, {{ $trackerColors[$stage] }} 6%, transparent);">
                                <span class="mono" style="font-size:0.68rem; color:var(--text-muted);">Nothing in {{ strtolower($label) }} yet.</span>
                            </div>
                        @endforelse
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .tracker-card:hover {
        transform: translateY(-2px);
        border-color: var(--accent-color) !important;
        box-shadow: 0 18px 34px rgba(0,0,0,0.24);
    }

    .tracker-card.is-dragging {
        opacity: 0.55;
        cursor: grabbing;
        transform: rotate(1deg) scale(0.99);
    }

    .tracker-dropzone.is-over {
        background: color-mix(in srgb, var(--accent-color) 7%, transparent);
        outline: 2px dashed var(--accent-color);
        outline-offset: 8px;
    }

    .tracker-dropzone.is-saving {
        opacity: 0.7;
        pointer-events: none;
    }

    .tracker-column.is-over-column {
        transform: translateY(-2px);
    }

    @media (max-width: 1200px) {
        .tracker-board-grid,
        .tracker-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .untracked-grid {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 780px) {
        .tracker-board-grid,
        .tracker-stats-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const cards = Array.from(document.querySelectorAll('.tracker-card'));
        const zones = Array.from(document.querySelectorAll('.tracker-dropzone'));
        let draggedCard = null;
        let originZone = null;

        function refreshCounts() {
            document.querySelectorAll('.tracker-column').forEach(column => {
                const countEl = column.querySelector('.tracker-column-count');
                const zone = column.querySelector('.tracker-dropzone');
                if (!countEl || !zone) return;
                countEl.textContent = zone.querySelectorAll('.tracker-card').length;
            });
        }

        function ensureEmptyState(zone) {
            const existingEmpty = zone.querySelector('.tracker-empty-state');
            const hasCards = zone.querySelector('.tracker-card');

            if (!hasCards && !existingEmpty) {
                const label = zone.dataset.stage?.replace('_', ' ') || 'this stage';
                const empty = document.createElement('div');
                empty.className = 'tracker-empty-state';
                empty.style.minHeight = '140px';
                empty.style.borderRadius = '16px';
                empty.style.border = '1px dashed var(--border-color)';
                empty.style.display = 'flex';
                empty.style.alignItems = 'center';
                empty.style.justifyContent = 'center';
                empty.style.padding = '1rem';
                empty.style.textAlign = 'center';
                empty.innerHTML = `<span class="mono" style="font-size:0.68rem; color:var(--text-muted);">Nothing in ${label} yet.</span>`;
                zone.appendChild(empty);
            }

            if (hasCards && existingEmpty) {
                existingEmpty.remove();
            }
        }

        async function persistMove(card, targetStage) {
            const form = card.querySelector('.tracker-stage-form');
            const input = card.querySelector('.tracker-stage-input');
            if (!form || !input) return false;

            input.value = targetStage;
            const formData = new FormData(form);
            const zone = card.closest('.tracker-dropzone');
            if (zone) zone.classList.add('is-saving');

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html,application/xhtml+xml'
                    },
                    body: formData,
                });

                if (!response.ok) throw new Error('Failed to move card');
                return true;
            } catch (error) {
                return false;
            } finally {
                if (zone) zone.classList.remove('is-saving');
            }
        }

        cards.forEach(card => {
            card.addEventListener('dragstart', () => {
                draggedCard = card;
                originZone = card.closest('.tracker-dropzone');
                card.classList.add('is-dragging');
            });

            card.addEventListener('dragend', () => {
                card.classList.remove('is-dragging');
                zones.forEach(zone => {
                    zone.classList.remove('is-over', 'is-saving');
                    zone.closest('.tracker-column')?.classList.remove('is-over-column');
                });
                draggedCard = null;
                originZone = null;
            });
        });

        zones.forEach(zone => {
            zone.addEventListener('dragover', (event) => {
                event.preventDefault();
                zone.classList.add('is-over');
                zone.closest('.tracker-column')?.classList.add('is-over-column');
            });

            zone.addEventListener('dragleave', () => {
                zone.classList.remove('is-over');
                zone.closest('.tracker-column')?.classList.remove('is-over-column');
            });

            zone.addEventListener('drop', async (event) => {
                event.preventDefault();
                zone.classList.remove('is-over');
                zone.closest('.tracker-column')?.classList.remove('is-over-column');

                if (!draggedCard) return;

                const targetStage = zone.dataset.stage;
                const currentStage = draggedCard.dataset.currentStage;
                if (!targetStage || targetStage === currentStage) return;

                const previousZone = originZone;
                zone.querySelector('.tracker-empty-state')?.remove();
                zone.prepend(draggedCard);
                draggedCard.dataset.currentStage = targetStage;
                refreshCounts();
                ensureEmptyState(previousZone);
                ensureEmptyState(zone);

                const saved = await persistMove(draggedCard, targetStage);
                if (!saved && previousZone) {
                    previousZone.prepend(draggedCard);
                    draggedCard.dataset.currentStage = currentStage;
                    refreshCounts();
                    ensureEmptyState(previousZone);
                    ensureEmptyState(zone);
                    alert('Could not move that commission just yet. Try again, brat.');
                    return;
                }

                window.setTimeout(() => window.location.reload(), 180);
            });
        });

        refreshCounts();
        zones.forEach(ensureEmptyState);
    });
</script>
@endpush
