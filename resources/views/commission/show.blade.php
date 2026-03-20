@extends('layouts.app')

@php
    $statusLabels = [
        \App\Models\CommissionRequest::STATUS_PENDING => 'Pending Review',
        \App\Models\CommissionRequest::STATUS_ACCEPTED => 'Accepted',
        \App\Models\CommissionRequest::STATUS_DECLINED => 'Declined',
        \App\Models\CommissionRequest::STATUS_NEEDS_INFO => 'Needs More Info',
    ];

    $statusColors = [
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

    $trackerColors = [
        \App\Models\CommissionRequest::TRACKER_QUEUE => '#8ec5ff',
        \App\Models\CommissionRequest::TRACKER_ACTIVE => 'var(--accent-color)',
        \App\Models\CommissionRequest::TRACKER_DELIVERY => '#ffd166',
        \App\Models\CommissionRequest::TRACKER_DONE => '#52d273',
    ];
@endphp

@section('content')
<div style="min-height: 80vh; padding: 3rem 1.25rem;">
    <div style="max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: minmax(320px, 0.95fr) minmax(0, 1.65fr); gap: 1.5rem; align-items: start;">
        <div style="background: var(--bg-panel); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.75rem; position: sticky; top: 6rem;">
            <div class="pill mono" style="margin-bottom: 1rem; border-color: var(--accent-color); color: var(--accent-color); background: var(--accent-dim);">
                <div class="dot" style="background: var(--accent-color);"></div>
                ● commission thread
            </div>
            <h2 class="serif" style="font-size: 2rem; margin-bottom: 0.75rem;">{{ $commissionRequest->title }}</h2>

            <div style="display:flex; gap:0.6rem; align-items:center; flex-wrap:wrap; margin-bottom: 1.25rem;">
                <div class="mono" style="display: inline-flex; padding: 0.45rem 0.7rem; border-radius: 999px; border: 1px solid {{ $statusColors[$commissionRequest->status] ?? 'var(--accent-color)' }}; background: color-mix(in srgb, {{ $statusColors[$commissionRequest->status] ?? 'var(--accent-color)' }} 12%, transparent); color: {{ $statusColors[$commissionRequest->status] ?? 'var(--accent-color)' }};">{{ $statusLabels[$commissionRequest->status] ?? ucfirst($commissionRequest->status) }}</div>
                @if($commissionRequest->tracker_stage)
                    <div class="mono" style="display: inline-flex; padding: 0.45rem 0.7rem; border-radius: 999px; border: 1px solid {{ $trackerColors[$commissionRequest->tracker_stage] ?? 'var(--accent-color)' }}; background: color-mix(in srgb, {{ $trackerColors[$commissionRequest->tracker_stage] ?? 'var(--accent-color)' }} 12%, transparent); color: {{ $trackerColors[$commissionRequest->tracker_stage] ?? 'var(--accent-color)' }};">{{ $trackerLabels[$commissionRequest->tracker_stage] ?? ucfirst($commissionRequest->tracker_stage) }}</div>
                @endif
            </div>

            <div style="display: grid; gap: 1rem; margin-bottom: 1.5rem;">
                <div>
                    <div class="mono" style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Flow</div>
                    <div style="line-height:1.7; color: var(--text-muted);">
                        Request sent → artist reviews → accept / decline / ask for more info
                        @if($commissionRequest->status === \App\Models\CommissionRequest::STATUS_ACCEPTED)
                            → track progress until complete
                        @endif
                    </div>
                </div>
                <div>
                    <div class="mono" style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Artist</div>
                    <div>{{ $commissionRequest->artist->name }} /{{ $commissionRequest->artist->username }}</div>
                </div>
                <div>
                    <div class="mono" style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Requester</div>
                    <div>{{ $commissionRequest->requester->name }} /{{ $commissionRequest->requester->username }}</div>
                </div>
                @if($commissionRequest->budget)
                <div>
                    <div class="mono" style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">Budget</div>
                    <div>${{ number_format($commissionRequest->budget, 2) }}</div>
                </div>
                @endif
            </div>

            <div class="mono" style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Original Request</div>
            <div style="background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem; white-space: pre-wrap; line-height: 1.7;">{{ $commissionRequest->details }}</div>

            @if($commissionRequest->artist_response)
            <div style="margin-top: 1rem;">
                <div class="mono" style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Latest Artist Note</div>
                <div style="background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem; white-space: pre-wrap; line-height: 1.7;">{{ $commissionRequest->artist_response }}</div>
            </div>
            @endif
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @if($isArtistView)
            <div style="background: var(--bg-panel); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.25rem;">
                <div class="mono" style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1rem;">Artist Actions</div>
                <div style="display: grid; gap: 0.75rem; grid-template-columns: repeat(3, minmax(0, 1fr));">
                    <form method="POST" action="{{ route('artist.requests.respond', $commissionRequest) }}">@csrf<input type="hidden" name="action" value="accepted"><input name="reason" placeholder="Optional acceptance note" style="width:100%; margin-bottom:0.5rem; background: var(--bg-color); color: var(--text-main); border:1px solid var(--border-color); padding:0.75rem; border-radius:10px;"><button class="btn btn-primary" style="width:100%; justify-content:center;">Accept</button></form>
                    <form method="POST" action="{{ route('artist.requests.respond', $commissionRequest) }}">@csrf<input type="hidden" name="action" value="declined"><input name="reason" placeholder="Optional decline reason" style="width:100%; margin-bottom:0.5rem; background: var(--bg-color); color: var(--text-main); border:1px solid var(--border-color); padding:0.75rem; border-radius:10px;"><button class="btn btn-ghost" style="width:100%; justify-content:center; border-color:#ff7b7b; color:#ff7b7b;">Decline</button></form>
                    <form method="POST" action="{{ route('artist.requests.respond', $commissionRequest) }}">@csrf<input type="hidden" name="action" value="needs_info"><input name="reason" placeholder="What info do you need?" style="width:100%; margin-bottom:0.5rem; background: var(--bg-color); color: var(--text-main); border:1px solid var(--border-color); padding:0.75rem; border-radius:10px;"><button class="btn btn-ghost" style="width:100%; justify-content:center; border-color:var(--accent-color); color:var(--accent-color);">Request More Info</button></form>
                </div>
                @if($commissionRequest->status !== \App\Models\CommissionRequest::STATUS_PENDING)
                <form method="POST" action="{{ route('artist.requests.undo', $commissionRequest) }}" style="margin-top: 1rem;">
                    @csrf
                    <button class="btn btn-ghost">Undo decision</button>
                </form>
                @endif
            </div>
            @endif

            @if($isArtistView && $commissionRequest->status === \App\Models\CommissionRequest::STATUS_ACCEPTED)
            <div style="background: var(--bg-panel); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.25rem;">
                <div class="mono" style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1rem;">Commission Tracker</div>
                <div style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap:0.65rem;">
                    @foreach($trackerLabels as $stage => $label)
                        <form method="POST" action="{{ route('artist.requests.tracker-stage', $commissionRequest) }}">
                            @csrf
                            <input type="hidden" name="tracker_stage" value="{{ $stage }}">
                            <button class="btn {{ $commissionRequest->tracker_stage === $stage ? 'btn-primary' : 'btn-ghost' }}" style="width:100%; justify-content:center; border-color: {{ $trackerColors[$stage] }}; color: {{ $commissionRequest->tracker_stage === $stage ? '#000' : $trackerColors[$stage] }}; {{ $commissionRequest->tracker_stage === $stage ? 'background:' . $trackerColors[$stage] . ';' : '' }}">{{ $label }}</button>
                        </form>
                    @endforeach
                </div>
                <div class="mono" style="font-size:0.68rem; color: var(--text-muted); margin-top:0.75rem;">Accepted requests now move through queue → in progress → review / delivery → completed, and the public tracker module reflects it live.</div>
            </div>
            @endif

            <div style="background: var(--bg-panel); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.25rem;">
                <div class="mono" style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.75rem;">What happens next</div>
                <div style="line-height:1.7; color: var(--text-muted); margin-bottom: 0.25rem;">
                    @if($commissionRequest->status === \App\Models\CommissionRequest::STATUS_PENDING)
                        The artist still needs to review this request. Either side can use the thread below to add context before a decision.
                    @elseif($commissionRequest->status === \App\Models\CommissionRequest::STATUS_NEEDS_INFO)
                        The artist asked for more information. Reply in the thread below and they can review again.
                    @elseif($commissionRequest->status === \App\Models\CommissionRequest::STATUS_DECLINED)
                        This request was declined. You can still keep the thread as a record of what was discussed.
                    @elseif($commissionRequest->status === \App\Models\CommissionRequest::STATUS_ACCEPTED)
                        This request is accepted. The artist can move it through the commission tracker, and that progress will show here as the piece advances.
                    @endif
                </div>
            </div>

            <div style="background: var(--bg-panel); border: 1px solid var(--border-color); border-radius: 16px; padding: 1.25rem;">
                <div class="mono" style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1rem;">Chat</div>
                <div style="display: flex; flex-direction: column; gap: 0.85rem; max-height: 520px; overflow: auto; padding-right: 0.35rem; margin-bottom: 1rem;">
                    @foreach($commissionRequest->messages->sortBy('id') as $message)
                        <div style="padding: 0.9rem 1rem; border-radius: 14px; background: {{ $message->kind === 'system' ? 'var(--accent-dim)' : ($message->user_id === auth()->id() ? 'color-mix(in srgb, var(--accent-color) 12%, var(--bg-color))' : 'var(--bg-color)') }}; border: 1px solid {{ $message->kind === 'system' ? 'color-mix(in srgb, var(--accent-color) 45%, var(--border-color))' : 'var(--border-color)' }};">
                            <div class="mono" style="font-size: 0.68rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.4rem;">{{ $message->kind === 'system' ? 'System' : ($message->user?->name ?? 'User') }} • {{ $message->created_at->format('M j, Y g:i A') }}</div>
                            @if(filled($message->message))
                                <div style="white-space: pre-wrap; line-height: 1.7;">{{ $message->message }}</div>
                            @endif
                            @if(!empty($message->attachments))
                                <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap:0.75rem; margin-top:0.75rem;">
                                    @foreach($message->attachments as $attachmentIndex => $attachment)
                                        @php
                                            $attachmentUrl = !empty($attachment['path'])
                                                ? route('commission.messages.attachment', [$commissionRequest, $message, $attachmentIndex])
                                                : ($attachment['url'] ?? '#');
                                        @endphp
                                        <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener" style="display:block; border-radius:12px; overflow:hidden; border:1px solid var(--border-color);">
                                            <img src="{{ $attachmentUrl }}" alt="{{ $attachment['name'] ?? 'Attached image' }}" style="display:block; width:100%; aspect-ratio:1/1; object-fit:cover;">
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('commission.messages.store', $commissionRequest) }}" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @csrf
                    <textarea name="message" maxlength="4000" placeholder="Send a message..." style="width: 100%; min-height: 130px; background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 1rem; border-radius: 12px; outline: none; resize: vertical;">{{ old('message') }}</textarea>
                    <input name="attachments[]" type="file" accept="image/*" multiple style="width:100%; background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: 12px; outline: none;">
                    <div style="display:flex; justify-content:space-between; gap:1rem; align-items:center; flex-wrap:wrap;">
                        <div class="mono" style="font-size: 0.7rem; color: var(--text-muted);">Keep all request-related discussion in this thread. You can send text, images, or both.</div>
                        <button class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
