@extends('layouts.app')

@php
    $colors = [
        'pending' => 'var(--accent-color)',
        'accepted' => '#52d273',
        'declined' => '#ff7b7b',
        'needs_info' => '#ffd166',
    ];

    $trackerLabels = [
        \App\Models\CommissionRequest::TRACKER_QUEUE => 'Queued',
        \App\Models\CommissionRequest::TRACKER_ACTIVE => 'In Progress',
        \App\Models\CommissionRequest::TRACKER_DELIVERY => 'Review / Delivery',
        \App\Models\CommissionRequest::TRACKER_DONE => 'Completed',
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
    <div style="max-width: 1200px; margin: 0 auto;">
        <div style="display:flex; justify-content:space-between; gap:1rem; align-items:flex-end; margin-bottom: 2rem; flex-wrap: wrap;">
            <div>
                <div class="pill mono" style="margin-bottom: 1rem; border-color: var(--accent-color); color: var(--accent-color); background: var(--accent-dim);">
                    <div class="dot" style="background: var(--accent-color);"></div>
                    ● my requests
                </div>
                <h1 class="serif" style="font-size: 3rem; margin-bottom: 0.5rem;">Your commission chats</h1>
                <p class="mono" style="color: var(--text-muted);">Reopen any request, check artist responses, and continue the conversation.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-ghost">Back to Play Dashboard</a>
        </div>

        <div style="display: grid; gap: 1rem;">
            @forelse($myRequests as $requestItem)
                <a href="{{ $requestItem->conversation ? route('conversations.show', $requestItem->conversation) : route('commission.show', $requestItem) }}" style="text-decoration:none; color:inherit; background: var(--bg-panel); border:1px solid var(--border-color); border-radius: 16px; padding: 1.25rem; display:grid; grid-template-columns: minmax(0, 1.2fr) auto auto; gap: 1rem; align-items:center;">
                    <div style="min-width:0;">
                        <div class="serif" style="font-size: 1.5rem; margin-bottom: 0.35rem;">{{ $requestItem->title }}</div>
                        <div class="mono" style="font-size: 0.74rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">for {{ $requestItem->artist->name }} /{{ $requestItem->artist->username }}</div>
                        <div style="color: var(--text-muted); line-height:1.7; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">{{ $requestItem->details }}</div>
                    </div>
                    <div style="display:flex; gap:0.6rem; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
                        <div class="mono" style="padding:0.45rem 0.7rem; border-radius:999px; border:1px solid {{ $colors[$requestItem->status] ?? 'var(--accent-color)' }}; color: {{ $colors[$requestItem->status] ?? 'var(--accent-color)' }}; background: color-mix(in srgb, {{ $colors[$requestItem->status] ?? 'var(--accent-color)' }} 12%, transparent);">{{ str_replace('_', ' ', strtoupper($requestItem->status)) }}</div>
                        @if($requestItem->tracker_stage)
                            <div class="mono" style="padding:0.45rem 0.7rem; border-radius:999px; border:1px solid {{ $trackerColors[$requestItem->tracker_stage] ?? 'var(--accent-color)' }}; color: {{ $trackerColors[$requestItem->tracker_stage] ?? 'var(--accent-color)' }}; background: color-mix(in srgb, {{ $trackerColors[$requestItem->tracker_stage] ?? 'var(--accent-color)' }} 12%, transparent);">{{ strtoupper($trackerLabels[$requestItem->tracker_stage] ?? $requestItem->tracker_stage) }}</div>
                        @endif
                        @php $unread = $requestItem->unreadCountFor(auth()->user()); @endphp
                        @if($unread > 0)
                            <div class="mono" style="min-width:1.2rem; height:1.2rem; padding:0 0.3rem; border-radius:999px; background:var(--accent-color); color:#000; font-size:0.65rem; font-weight:bold; line-height:1.2rem; text-align:center;">{{ $unread }}</div>
                        @endif
                    </div>
                    <div class="mono" style="font-size:0.72rem; color: var(--text-muted);">{{ $requestItem->updated_at->diffForHumans() }}</div>
                </a>
            @empty
                <div style="background: var(--bg-panel); border:1px solid var(--border-color); border-radius:16px; padding:2rem; color:var(--text-muted);">You haven't sent any commission requests yet. Browse artists and send one when something catches your eye.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
