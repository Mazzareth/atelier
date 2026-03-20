@extends('layouts.app')

@php
    $colors = [
        \App\Models\CommissionRequest::STATUS_PENDING => 'var(--accent-color)',
        \App\Models\CommissionRequest::STATUS_ACCEPTED => '#52d273',
        \App\Models\CommissionRequest::STATUS_DECLINED => '#ff7b7b',
        \App\Models\CommissionRequest::STATUS_NEEDS_INFO => '#ffd166',
    ];

    $trackerLabels = [
        \App\Models\CommissionRequest::TRACKER_QUEUE => 'Queued',
        \App\Models\CommissionRequest::TRACKER_ACTIVE => 'In Progress',
        \App\Models\CommissionRequest::TRACKER_DELIVERY => 'Review / Delivery',
        \App\Models\CommissionRequest::TRACKER_DONE => 'Completed',
    ];
@endphp

@section('content')
<div style="min-height: 80vh; padding: 3rem 1.25rem;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display:flex; justify-content:space-between; gap:1rem; align-items:flex-end; margin-bottom: 2rem; flex-wrap: wrap;">
            <div>
                <div class="pill mono" style="margin-bottom: 1rem; border-color: var(--accent-color); color: var(--accent-color); background: var(--accent-dim);">
                    <div class="dot" style="background: var(--accent-color);"></div>
                    ● client requests
                </div>
                <h1 class="serif" style="font-size: 3rem; margin-bottom: 0.5rem;">Commission requests</h1>
                <p class="mono" style="color: var(--text-muted);">Review incoming requests, answer them, then move accepted work through your public commission tracker.</p>
            </div>
            <a href="{{ route('artist.dashboard') }}" class="btn btn-ghost">Back to Work Dashboard</a>
        </div>

        <div style="background: var(--bg-panel); border:1px solid var(--border-color); border-radius: 16px; padding: 1.25rem; margin-bottom: 1.25rem;">
            <div style="display:flex; justify-content:space-between; gap:1rem; align-items:center; margin-bottom:1rem; flex-wrap:wrap;">
                <div>
                    <div class="mono" style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Formalized flow</div>
                    <div style="line-height:1.7; color: var(--text-main);">Request → accept / decline / ask for more info → accepted pieces enter the tracker → move to completion</div>
                </div>
            </div>
            <div style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap:0.75rem;">
                @foreach($trackerLabels as $stage => $label)
                    @php $items = $trackerGroups[$stage] ?? collect(); @endphp
                    <div style="background: var(--bg-color); border:1px solid var(--border-color); border-radius:12px; padding:0.9rem;">
                        <div class="mono" style="font-size:0.66rem; color: var(--accent-color); text-transform:uppercase; margin-bottom:0.45rem;">{{ $label }}</div>
                        <div class="serif" style="font-size:1.8rem;">{{ $items->count() }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="display: grid; gap: 1rem;">
            @forelse($requests as $requestItem)
                <a href="{{ route('commission.show', $requestItem) }}" style="text-decoration:none; color:inherit; background: var(--bg-panel); border:1px solid var(--border-color); border-radius: 16px; padding: 1.25rem; display:grid; grid-template-columns: minmax(0, 1.2fr) auto auto; gap: 1rem; align-items:center;">
                    <div style="min-width:0;">
                        <div class="serif" style="font-size: 1.5rem; margin-bottom: 0.35rem;">{{ $requestItem->title }}</div>
                        <div class="mono" style="font-size: 0.74rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">from {{ $requestItem->requester->name }} /{{ $requestItem->requester->username }}</div>
                        <div style="color: var(--text-muted); line-height:1.7; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">{{ $requestItem->details }}</div>
                    </div>
                    <div style="display:flex; gap:0.6rem; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
                        <div class="mono" style="padding:0.45rem 0.7rem; border-radius:999px; border:1px solid {{ $colors[$requestItem->status] ?? 'var(--accent-color)' }}; color: {{ $colors[$requestItem->status] ?? 'var(--accent-color)' }}; background: color-mix(in srgb, {{ $colors[$requestItem->status] ?? 'var(--accent-color)' }} 12%, transparent);">{{ str_replace('_', ' ', strtoupper($requestItem->status)) }}</div>
                        @if($requestItem->tracker_stage)
                            <div class="mono" style="padding:0.45rem 0.7rem; border-radius:999px; border:1px solid var(--accent-color); color: var(--accent-color); background: var(--accent-dim);">{{ strtoupper($trackerLabels[$requestItem->tracker_stage] ?? $requestItem->tracker_stage) }}</div>
                        @endif
                        @php $unread = $requestItem->unreadCountFor(auth()->user()); @endphp
                        @if($unread > 0)
                            <div class="mono" style="min-width:1.2rem; height:1.2rem; padding:0 0.3rem; border-radius:999px; background:var(--accent-color); color:#000; font-size:0.65rem; font-weight:bold; line-height:1.2rem; text-align:center;">{{ $unread }}</div>
                        @endif
                    </div>
                    <div class="mono" style="font-size:0.72rem; color: var(--text-muted);">{{ $requestItem->updated_at->diffForHumans() }}</div>
                </a>
            @empty
                <div style="background: var(--bg-panel); border:1px solid var(--border-color); border-radius:16px; padding:2rem; color:var(--text-muted);">No requests yet. Once logged-in users start asking for commissions, they’ll land here.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
