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
    $activityMessages = $activeConversation
        ? $activeConversation->messages->where('kind', 'system')->sortByDesc('id')->take(8)
        : collect();
@endphp

@section('content')
<div class="chat-app-page">
    <div class="chat-app-shell {{ $activeConversation ? 'has-actions-rail' : '' }}">
        <aside class="chat-sidebar">
            <div class="chat-sidebar-top">
                <div>
                    <div class="pill mono chat-sidebar-pill">
                        <div class="dot"></div>
                        ● chats
                    </div>
                    <h1 class="serif chat-sidebar-title">Inbox</h1>
                </div>
                <a href="{{ route('browse') }}" class="btn btn-ghost chat-sidebar-browse">Browse</a>
            </div>

            <form method="GET" action="{{ route('conversations.index') }}" class="chat-search-form">
                <input
                    type="text"
                    name="q"
                    value="{{ $searchQuery ?? '' }}"
                    placeholder="Search chats..."
                    class="chat-search-input mono"
                >
            </form>

            <div class="chat-list" id="chat-list">
                @forelse($conversations as $conversation)
                    @php
                        $itemOtherParty = $conversation->otherPartyFor(auth()->user());
                        $itemUnread = $conversation->unreadCountFor(auth()->user());
                        $itemActive = ($activeConversation?->id === $conversation->id);
                        $avatarModule = collect([$itemOtherParty?->profileModules])->flatten()->firstWhere('type', 'avatar_info');
                        $avatar = $avatarModule->settings['avatar'] ?? null;
                        $latestPreviewMessage = $conversation->messages->where('kind', '!=', 'system')->sortByDesc('id')->first();
                        $previewText = $latestPreviewMessage
                            ? (filled($latestPreviewMessage->message)
                                ? \Illuminate\Support\Str::limit($latestPreviewMessage->message, 48)
                                : (count($latestPreviewMessage->attachments ?? []) ? 'Sent image' : 'No messages yet'))
                            : 'No messages yet';
                    @endphp

                    <a href="{{ route('conversations.show', ['conversation' => $conversation, 'q' => $searchQuery]) }}" class="chat-list-item {{ $itemActive ? 'is-active' : '' }}">
                        <div class="chat-list-avatar">
                            @if($avatar)
                                <img src="{{ $avatar }}" alt="{{ $itemOtherParty?->name ?? 'User' }} avatar">
                            @else
                                <div class="chat-list-avatar-fallback serif">{{ strtoupper(substr($itemOtherParty?->username ?? 'U', 0, 1)) }}</div>
                            @endif
                        </div>

                        <div class="chat-list-main">
                            <div class="chat-list-name-row">
                                <div class="chat-list-name">{{ $conversation->title ?: ($itemOtherParty->name ?? 'Unknown User') }}</div>
                                <div style="display:flex; align-items:center; gap:0.4rem; flex-shrink:0;">
                                    @if($itemUnread > 0)
                                        <span class="chat-list-unread mono">{{ $itemUnread }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="chat-list-preview-row">
                                <div class="chat-list-preview">{{ $previewText }}</div>
                                <div class="chat-list-time mono">{{ ($latestPreviewMessage?->created_at ?? $conversation->updated_at)?->format('M j') }}</div>
                            </div>
                            <div class="chat-list-meta-row">
                                @if($conversation->kind === 'commission')
                                    <span class="chat-kind-badge mono">Commission</span>
                                @endif
                                @if($conversation->commissionRequest)
                                    <span class="chat-kind-badge mono chat-kind-badge--muted">{{ $statusLabels[$conversation->commissionRequest->status] ?? ucfirst($conversation->commissionRequest->status) }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="chat-empty-list">
                        No chats yet.
                    </div>
                @endforelse
            </div>
        </aside>

        <section class="chat-main-panel">
            @if($activeConversation)
                <div class="chat-thread-shell">
                    <header class="chat-thread-header">
                        <div class="chat-thread-identity">
                            <div class="chat-thread-avatar">
                                @php
                                    $activeAvatarModule = collect([$otherParty?->profileModules])->flatten()->firstWhere('type', 'avatar_info');
                                    $activeAvatar = $activeAvatarModule->settings['avatar'] ?? null;
                                @endphp
                                @if($activeAvatar)
                                    <img src="{{ $activeAvatar }}" alt="{{ $otherParty->name ?? 'User' }} avatar">
                                @else
                                    <div class="chat-thread-avatar-fallback serif">{{ strtoupper(substr($otherParty?->username ?? 'U', 0, 1)) }}</div>
                                @endif
                            </div>
                            <div class="chat-thread-title-wrap">
                                <div class="chat-thread-title">{{ $activeConversation->title ?: ('Chat with ' . ($otherParty->name ?? 'User')) }}</div>
                                <div class="mono chat-thread-subtitle">{{ $otherParty->name ?? 'Unknown User' }} /{{ $otherParty->username ?? 'user' }}</div>
                            </div>
                        </div>
                        <div class="chat-thread-header-side">
                            @if($commissionRequest)
                                <span class="chat-pill mono">{{ $statusLabels[$commissionRequest->status] ?? ucfirst($commissionRequest->status) }}</span>
                                @if($commissionRequest->tracker_stage)
                                    <span class="chat-pill mono">{{ $trackerLabels[$commissionRequest->tracker_stage] ?? ucfirst($commissionRequest->tracker_stage) }}</span>
                                @endif
                            @endif
                        </div>
                    </header>

                    @if($isArtistView && $commissionRequest && $commissionRequest->status === \App\Models\CommissionRequest::STATUS_ACCEPTED)
                        <section class="chat-progress-strip">
                            <div class="chat-progress-strip-head">
                                <div class="chat-progress-strip-kicker mono">Commission progress</div>
                                <div class="chat-progress-strip-links">
                                    <a href="{{ route('artist.workspace.show') }}" class="chat-inline-link mono">Workspace</a>
                                    <a href="{{ url('/atelier/commissions') }}" class="chat-inline-link mono">Tracker board</a>
                                </div>
                            </div>

                            <div class="chat-progress-pills" role="group" aria-label="Commission progress stage">
                                @foreach($trackerLabels as $stage => $label)
                                    <form method="POST" action="{{ route('artist.requests.tracker-stage', $commissionRequest) }}">
                                        @csrf
                                        <input type="hidden" name="tracker_stage" value="{{ $stage }}">
                                        <button class="chat-progress-pill {{ $commissionRequest->tracker_stage === $stage ? 'is-active' : '' }}" type="submit" style="--stage-color: {{ $trackerColors[$stage] }};">
                                            {{ $label }}
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if($commissionRequest && !empty($commissionRequest->reference_images))
                        <div class="chat-reference-strip">
                            <div class="mono chat-reference-label">Commission references</div>
                            <div class="chat-reference-grid">
                                @foreach($commissionRequest->reference_images as $reference)
                                    <a href="{{ $reference['url'] ?? '#' }}" target="_blank" rel="noopener" class="chat-reference-card">
                                        <img src="{{ $reference['url'] ?? '' }}" alt="{{ $reference['name'] ?? 'Reference image' }}">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="chat-message-stream" id="chat-message-stream">
                        @php
                            $visibleMessages = $activeConversation->messages->where('kind', '!=', 'system')->sortBy('id');
                        @endphp
                        @forelse($visibleMessages as $message)
                            <div class="chat-message-row {{ $message->user_id === auth()->id() ? 'is-mine' : 'is-theirs' }}">
                                <div class="chat-message-bubble">
                                    <div class="mono chat-message-meta">{{ $message->user?->name ?? 'User' }} • {{ $message->created_at->format('M j, Y g:i A') }}</div>
                                    @if(filled($message->message))
                                        <div class="chat-message-body chat-markdown-body">{!! \Illuminate\Support\Str::markdown($message->message, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}</div>
                                    @endif
                                    @if(!empty($message->attachments))
                                        <div class="chat-attachment-grid">
                                            @foreach($message->attachments as $attachmentIndex => $attachment)
                                                @php
                                                    $attachmentUrl = !empty($attachment['path'])
                                                        ? route('conversations.messages.attachment', [$activeConversation, $message, $attachmentIndex])
                                                        : ($attachment['url'] ?? '#');
                                                @endphp
                                                <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener" class="chat-attachment-card">
                                                    <img src="{{ $attachmentUrl }}" alt="{{ $attachment['name'] ?? 'Attached image' }}">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="chat-empty-thread">
                                <div class="chat-empty-thread-illustration">✦</div>
                                <div class="mono chat-empty-thread-label">No messages yet</div>
                                <div class="chat-empty-thread-text">
                                    @if($isArtistView && $commissionRequest && $commissionRequest->status === \App\Models\CommissionRequest::STATUS_PENDING)
                                        Start with a short welcome note or make the request decision from the artist actions panel.
                                    @elseif($isArtistView && $commissionRequest && $commissionRequest->status === \App\Models\CommissionRequest::STATUS_ACCEPTED)
                                        Send the first update, confirm the next step, or move the commission into its current tracker stage.
                                    @else
                                        Start the thread with a message, share reference images, or reply to get the commission moving.
                                    @endif
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('conversations.messages.store', $activeConversation) }}" enctype="multipart/form-data" class="chat-composer">
                        @csrf
                        <div class="chat-composer-shell">
                            <div class="chat-composer-frame">
                                <textarea name="message" maxlength="4000" placeholder="Write a message to {{ $otherParty->name ?? 'them' }}. You can use Markdown, attach images, and press Ctrl+Enter to send." class="chat-composer-input">{{ old('message') }}</textarea>
                                <div class="chat-composer-toolbar">
                                    <div class="chat-composer-hint-wrap">
                                        <label class="chat-attach-button" title="Attach images">
                                            <input name="attachments[]" type="file" accept="image/*" multiple class="chat-file-input" data-chat-attachments>
                                            <span>＋</span>
                                        </label>
                                        <div class="chat-attachment-preview-strip" data-chat-attachment-preview></div>
                                        <div class="chat-composer-hint">Attach images if you need them.</div>
                                    </div>
                                    <button class="btn btn-primary chat-send-button" type="submit">Send</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <div class="chat-main-empty">
                    <div class="pill mono chat-sidebar-pill" style="margin-bottom:1rem;">
                        <div class="dot"></div>
                        ● no chat selected
                    </div>
                    <h2 class="serif chat-main-empty-title">Pick a conversation.</h2>
                    <a href="{{ route('browse') }}" class="btn btn-primary">Browse artists <span class="arrow">→</span></a>
                </div>
            @endif
        </section>

        @if($activeConversation)
            <aside class="chat-actions-rail">
                <div class="chat-actions-scroll">
                    <section class="chat-action-card chat-action-card--overview">
                        <div class="chat-action-card-label mono">Overview</div>
                        <div class="chat-action-card-title">Thread details</div>

                        <div class="chat-presence-card">
                            <div class="chat-presence-main">
                                <div class="chat-presence-name">{{ $otherParty->name ?? 'Unknown User' }}</div>
                                <div class="mono chat-presence-handle">/{{ $otherParty->username ?? 'user' }}</div>
                            </div>
                            @if($activeConversation->kind === 'commission' || $commissionRequest)
                                <span class="chat-kind-badge mono">Commission</span>
                            @endif
                        </div>

                        <div class="chat-detail-stack">
                            <div class="chat-detail-item chat-detail-item--pill-row">
                                <span class="chat-detail-label mono">Conversation</span>
                                <span class="chat-detail-value">{{ $activeConversation->title ?: ('Chat with ' . ($otherParty->name ?? 'User')) }}</span>
                            </div>
                            @if($commissionRequest)
                                <div class="chat-detail-item chat-detail-item--pill-row">
                                    <span class="chat-detail-label mono">Status</span>
                                    <div class="chat-detail-pill-row">
                                        <span class="chat-pill mono" style="border-color: {{ $statusColors[$commissionRequest->status] ?? 'var(--accent-color)' }}; color: {{ $statusColors[$commissionRequest->status] ?? 'var(--accent-color)' }}; background: color-mix(in srgb, {{ $statusColors[$commissionRequest->status] ?? 'var(--accent-color)' }} 12%, transparent); width: fit-content;">{{ $statusLabels[$commissionRequest->status] ?? ucfirst($commissionRequest->status) }}</span>
                                        @if($commissionRequest->tracker_stage)
                                            <span class="chat-pill mono" style="border-color: {{ $trackerColors[$commissionRequest->tracker_stage] ?? 'var(--accent-color)' }}; color: {{ $trackerColors[$commissionRequest->tracker_stage] ?? 'var(--accent-color)' }}; background: color-mix(in srgb, {{ $trackerColors[$commissionRequest->tracker_stage] ?? 'var(--accent-color)' }} 12%, transparent); width: fit-content;">{{ $trackerLabels[$commissionRequest->tracker_stage] ?? ucfirst($commissionRequest->tracker_stage) }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>

                    @if($activityMessages->isNotEmpty())
                        <section class="chat-action-card">
                            <div class="chat-action-card-label mono">Activity</div>
                            <div class="chat-action-card-title">Recent thread updates</div>
                            <div class="chat-activity-feed">
                                @foreach($activityMessages as $activity)
                                    <article class="chat-activity-item">
                                        <div class="chat-activity-icon">✦</div>
                                        <div class="chat-activity-content">
                                            <div class="chat-activity-text">{{ $activity->message }}</div>
                                            <div class="mono chat-activity-time">{{ $activity->created_at->format('M j, g:i A') }}</div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if($isArtistView && $commissionRequest)
                        <section class="chat-action-card">
                            @if($commissionRequest->status === \App\Models\CommissionRequest::STATUS_PENDING)
                                <div class="chat-action-card-label mono">Next step</div>
                                <div class="chat-action-card-title">Decide what to do with this request</div>
                                <div class="chat-action-summary chat-action-summary--tight">
                                    <div class="chat-action-summary-copy">Pick one path: accept it, ask for missing details, or decline it. This panel should help you decide, not make you think about interface rules.</div>
                                </div>

                                <div class="chat-decision-stack">
                                    <form method="POST" action="{{ route('artist.requests.respond', $commissionRequest) }}" class="chat-decision-card chat-decision-card--primary">
                                        @csrf
                                        <input type="hidden" name="action" value="accepted">
                                        <div class="chat-decision-head">
                                            <div>
                                                <div class="chat-decision-title">Accept request</div>
                                                <div class="chat-decision-copy">Tell them they’re in and give a quick next step.</div>
                                            </div>
                                            <button class="btn btn-primary chat-decision-btn" type="submit">Accept</button>
                                        </div>
                                        <input id="accept-note" name="reason" placeholder="Welcome note, estimate, or next step..." class="chat-mini-input">
                                    </form>

                                    <form method="POST" action="{{ route('artist.requests.respond', $commissionRequest) }}" class="chat-decision-card">
                                        @csrf
                                        <input type="hidden" name="action" value="needs_info">
                                        <div class="chat-decision-head">
                                            <div>
                                                <div class="chat-decision-title">Ask for more info</div>
                                                <div class="chat-decision-copy">Use this when the brief is incomplete or unclear.</div>
                                            </div>
                                            <button class="btn btn-ghost chat-decision-btn" type="submit">Ask</button>
                                        </div>
                                        <input id="needs-info-note" name="reason" placeholder="What details are still missing?" class="chat-mini-input">
                                    </form>

                                    <form method="POST" action="{{ route('artist.requests.respond', $commissionRequest) }}" class="chat-decision-card chat-decision-card--danger">
                                        @csrf
                                        <input type="hidden" name="action" value="declined">
                                        <div class="chat-decision-head">
                                            <div>
                                                <div class="chat-decision-title">Decline request</div>
                                                <div class="chat-decision-copy">Use this if it’s not a fit, not feasible, or not something you want to take.</div>
                                            </div>
                                            <button class="btn btn-ghost chat-decision-btn chat-action-btn--danger" type="submit">Decline</button>
                                        </div>
                                        <input id="decline-note" name="reason" placeholder="Short polite reason..." class="chat-mini-input">
                                    </form>
                                </div>
                            @elseif($commissionRequest->status === \App\Models\CommissionRequest::STATUS_ACCEPTED)
                                <div class="chat-action-card-label mono">Accepted commission</div>
                                <div class="chat-action-card-title">Manage thread</div>
                            @elseif($commissionRequest->status === \App\Models\CommissionRequest::STATUS_DECLINED)
                                <div class="chat-action-card-label mono">Declined request</div>
                                <div class="chat-action-card-title">Nothing else needs doing here</div>
                                <div class="chat-action-summary">
                                    <div class="chat-action-summary-copy">This request is already closed out. Keep the thread if you want the record, or undo the decision if it needs another look.</div>
                                </div>
                            @endif

                            @if($commissionRequest->status !== \App\Models\CommissionRequest::STATUS_PENDING)
                                <form method="POST" action="{{ route('artist.requests.undo', $commissionRequest) }}" class="chat-undo-form">
                                    @csrf
                                    <button class="chat-undo-link" type="submit">Undo decision</button>
                                </form>
                            @endif
                        </section>
                    @endif

                    <section class="chat-action-card chat-action-card--danger-zone">
                        <div class="chat-action-card-label mono">Danger zone</div>
                        <div class="chat-action-card-title">Delete thread</div>
                        <div class="chat-action-summary chat-action-summary--danger">
                            <div class="chat-action-summary-copy">Delete the full conversation and everything tied to it here. Commission threads will also remove related request data, messages, references, and workspace items.</div>
                        </div>
                        <form method="POST" action="{{ route('conversations.destroy', $activeConversation) }}" onsubmit="return confirm('Delete this entire chat? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-ghost chat-action-btn chat-action-btn--danger">Delete chat</button>
                        </form>
                    </section>
                </div>
            </aside>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<style>
    .chat-app-page {
        height: calc(100vh - 73px);
        padding: 0.75rem;
        overflow: hidden;
        background:
            radial-gradient(circle at top, color-mix(in srgb, var(--accent-color) 10%, transparent), transparent 34%),
            linear-gradient(180deg, color-mix(in srgb, var(--bg-color) 92%, #06060a), var(--bg-color));
    }

    .chat-app-shell {
        height: 100%;
        max-width: 100%;
        margin: 0;
        display: grid;
        grid-template-columns: 300px minmax(0, 1fr);
        gap: 0.75rem;
        overflow: hidden;
    }

    .chat-app-shell.has-actions-rail {
        grid-template-columns: 280px minmax(0, 1fr) 340px;
    }

    .chat-sidebar,
    .chat-main-panel,
    .chat-actions-rail,
    .chat-action-bar,
    .chat-composer,
    .chat-main-empty,
    .chat-action-card {
        background: linear-gradient(180deg, color-mix(in srgb, var(--bg-panel) 97%, transparent), color-mix(in srgb, var(--bg-color) 16%, var(--bg-panel)));
        border: 1px solid color-mix(in srgb, var(--accent-color) 10%, var(--border-color));
        box-shadow: 0 22px 56px rgba(0,0,0,0.18);
        backdrop-filter: blur(10px);
    }

    .chat-sidebar,
    .chat-main-panel,
    .chat-actions-rail {
        border-radius: 24px;
        overflow: hidden;
        min-height: 0;
        height: 100%;
    }

    .chat-sidebar {
        display: flex;
        flex-direction: column;
    }

    .chat-sidebar-top {
        padding: 1rem 1rem 0.85rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        align-items: flex-start;
        flex-shrink: 0;
    }

    .chat-sidebar-pill {
        margin-bottom: 0.7rem;
        border-color: color-mix(in srgb, var(--accent-color) 48%, var(--border-color));
        background: color-mix(in srgb, var(--accent-color) 10%, transparent);
    }

    .chat-sidebar-title {
        font-size: 1.9rem;
        margin-bottom: 0;
    }

    .chat-sidebar-browse {
        border-radius: 12px;
        padding: 0.72rem 0.9rem;
        flex-shrink: 0;
    }

    .chat-search-form {
        padding: 0.8rem 0.9rem;
        border-bottom: 1px solid var(--border-color);
        flex-shrink: 0;
    }

    .chat-search-input,
    .chat-mini-input,
    .chat-composer-input {
        width: 100%;
        background: color-mix(in srgb, var(--bg-color) 68%, var(--bg-panel));
        color: var(--text-main);
        border: 1px solid var(--border-color);
        outline: none;
    }

    .chat-search-input {
        border-radius: 12px;
        padding: 0.85rem 0.95rem;
        font-size: 0.75rem;
    }

    .chat-list {
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0.55rem;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        min-height: 0;
    }

    .chat-list-item {
        display: grid;
        grid-template-columns: 44px minmax(0, 1fr);
        gap: 0.8rem;
        text-decoration: none;
        color: inherit;
        padding: 0.8rem 0.85rem;
        border-radius: 18px;
        border: 1px solid transparent;
        transition: background 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
        align-items: start;
    }

    .chat-list-item:hover,
    .chat-list-item.is-active {
        background: color-mix(in srgb, var(--bg-color) 28%, var(--bg-panel));
        border-color: color-mix(in srgb, var(--accent-color) 28%, var(--border-color));
        transform: translateY(-1px);
    }

    .chat-list-avatar {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid color-mix(in srgb, var(--accent-color) 18%, var(--border-color));
        background: color-mix(in srgb, var(--bg-color) 68%, var(--bg-panel));
    }

    .chat-list-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .chat-list-avatar-fallback {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        background: var(--accent-dim);
        color: var(--accent-color);
        font-size: 1rem;
    }

    .chat-list-main {
        min-width: 0;
    }

    .chat-list-name-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.6rem;
        min-width: 0;
    }

    .chat-list-name,
    .chat-thread-title,
    .chat-main-empty-title {
        color: var(--text-main);
    }

    .chat-list-name {
        font-size: 0.95rem;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chat-list-preview-row,
    .chat-list-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.6rem;
        margin-top: 0.35rem;
    }

    .chat-list-preview {
        color: var(--text-muted);
        font-size: 0.82rem;
        line-height: 1.35;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .chat-list-time {
        color: var(--text-muted);
        font-size: 0.58rem;
        flex-shrink: 0;
    }

    .chat-kind-badge--muted {
        color: var(--text-muted);
        border-color: color-mix(in srgb, var(--text-muted) 28%, var(--border-color));
    }

    .chat-kind-badge,
    .chat-list-unread,
    .chat-thread-subtitle,
    .chat-action-bar-title,
    .chat-message-meta,
    .chat-pill {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .chat-thread-subtitle,
    .chat-message-meta {
        color: var(--text-muted);
    }

    .chat-kind-badge,
    .chat-pill {
        padding: 0.24rem 0.42rem;
        border-radius: 999px;
        border: 1px solid color-mix(in srgb, var(--accent-color) 14%, var(--border-color));
        background: color-mix(in srgb, var(--bg-color) 18%, var(--bg-panel));
        color: color-mix(in srgb, var(--accent-color) 70%, var(--text-muted));
        white-space: nowrap;
        opacity: 0.9;
    }

    .chat-list-unread {
        min-width: 1.2rem;
        height: 1.2rem;
        padding: 0 0.3rem;
        border-radius: 999px;
        background: var(--accent-color);
        color: #000;
        font-weight: 700;
        line-height: 1.2rem;
        text-align: center;
    }

    .chat-empty-list {
        padding: 1rem;
        color: var(--text-muted);
        line-height: 1.6;
    }

    .chat-main-panel {
        min-width: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .chat-actions-rail {
        min-width: 0;
        padding: 0.75rem;
        background: transparent;
        border-left: 1px solid color-mix(in srgb, var(--accent-color) 6%, var(--border-color));
    }

    .chat-actions-scroll {
        height: 100%;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        padding-right: 0.1rem;
    }

    .chat-action-card {
        border-radius: 20px;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
        background: linear-gradient(180deg, color-mix(in srgb, var(--bg-panel) 96%, transparent), color-mix(in srgb, var(--bg-color) 18%, var(--bg-panel)));
        border: 1px solid color-mix(in srgb, var(--accent-color) 10%, var(--border-color));
        box-shadow: 0 14px 30px rgba(0,0,0,0.14);
    }

    .chat-action-card--overview {
        background:
            radial-gradient(circle at top right, color-mix(in srgb, var(--accent-color) 10%, transparent), transparent 42%),
            linear-gradient(180deg, color-mix(in srgb, var(--bg-panel) 97%, transparent), color-mix(in srgb, var(--bg-color) 18%, var(--bg-panel)));
    }

    .chat-action-card--danger-zone {
        margin-top: auto;
        border-color: color-mix(in srgb, #ff7b7b 24%, var(--border-color));
    }

    .chat-action-card-label {
        color: var(--accent-color);
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.09em;
    }

    .chat-action-card-title {
        font-size: 1.05rem;
        font-weight: 700;
    }

    .chat-presence-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.9rem 0.95rem;
        border-radius: 16px;
        background: color-mix(in srgb, var(--bg-color) 28%, var(--bg-panel));
        border: 1px solid color-mix(in srgb, var(--accent-color) 14%, var(--border-color));
    }

    .chat-presence-main {
        min-width: 0;
    }

    .chat-presence-name {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.3;
    }

    .chat-presence-handle {
        color: var(--text-muted);
        font-size: 0.68rem;
        margin-top: 0.18rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .chat-detail-stack {
        display: flex;
        flex-direction: column;
        gap: 0.7rem;
        padding: 0.1rem 0 0;
    }

    .chat-detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.42rem;
        padding: 0.8rem 0.9rem;
        border-radius: 14px;
        background: color-mix(in srgb, var(--bg-color) 32%, var(--bg-panel));
        border: 1px solid color-mix(in srgb, var(--accent-color) 10%, var(--border-color));
    }

    .chat-detail-item--pill-row {
        gap: 0.55rem;
    }

    .chat-detail-pill-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        align-items: center;
    }

    .chat-detail-label {
        color: var(--text-muted);
        font-size: 0.62rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .chat-detail-value {
        color: var(--text-main);
        line-height: 1.45;
    }

    .chat-activity-feed {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }

    .chat-activity-item {
        display: flex;
        gap: 0.7rem;
        align-items: flex-start;
        padding: 0.8rem;
        border-radius: 15px;
        background: color-mix(in srgb, var(--bg-color) 32%, var(--bg-panel));
        border: 1px solid color-mix(in srgb, var(--accent-color) 12%, var(--border-color));
    }

    .chat-activity-icon {
        width: 1.9rem;
        height: 1.9rem;
        border-radius: 999px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        background: var(--accent-dim);
        color: var(--accent-color);
        border: 1px solid color-mix(in srgb, var(--accent-color) 30%, var(--border-color));
        font-size: 0.85rem;
    }

    .chat-activity-content {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .chat-activity-text {
        line-height: 1.5;
        color: var(--text-main);
    }

    .chat-activity-time {
        font-size: 0.62rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .chat-thread-shell {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 0;
    }

    .chat-thread-header {
        padding: 1rem 1.15rem;
        border-bottom: 1px solid color-mix(in srgb, var(--accent-color) 10%, var(--border-color));
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        flex-shrink: 0;
        background: linear-gradient(180deg, color-mix(in srgb, var(--accent-color) 6%, transparent), transparent);
    }

    .chat-thread-identity {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        min-width: 0;
    }

    .chat-thread-avatar {
        width: 52px;
        height: 52px;
        border-radius: 18px;
        overflow: hidden;
        background: color-mix(in srgb, var(--bg-color) 60%, var(--bg-panel));
        border: 1px solid color-mix(in srgb, var(--accent-color) 20%, var(--border-color));
        flex-shrink: 0;
    }

    .chat-thread-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .chat-thread-avatar-fallback {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        color: var(--accent-color);
        background: var(--accent-dim);
        font-size: 1.1rem;
    }

    .chat-thread-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
    }

    .chat-thread-header-side {
        display: flex;
        gap: 0.45rem;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .chat-action-bar,
    .chat-composer,
    .chat-main-empty,
    .chat-reference-strip {
        margin: 0.8rem;
        border-radius: 18px;
        padding: 1rem;
        flex-shrink: 0;
    }

    .chat-reference-strip {
        background: linear-gradient(180deg, color-mix(in srgb, var(--bg-panel) 96%, transparent), color-mix(in srgb, var(--bg-color) 18%, var(--bg-panel)));
        border: 1px solid color-mix(in srgb, var(--accent-color) 12%, var(--border-color));
        box-shadow: 0 18px 44px rgba(0,0,0,0.14);
    }

    .chat-action-bar {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .chat-action-grid,
    .chat-tracker-grid {
        display: grid;
        gap: 0.7rem;
    }

    .chat-action-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .chat-action-grid--stacked,
    .chat-action-grid--progress-links,
    .chat-tracker-grid--rail {
        grid-template-columns: 1fr;
    }

    .chat-progress-strip {
        margin: 0.65rem 0.8rem 0;
        padding: 0.4rem 0 0;
        border-top: 1px solid color-mix(in srgb, var(--accent-color) 10%, var(--border-color));
        flex-shrink: 0;
    }

    .chat-progress-strip-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.65rem;
    }

    .chat-progress-strip-kicker {
        color: var(--text-muted);
        font-size: 0.64rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .chat-progress-strip-links {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        flex-wrap: wrap;
    }

    .chat-inline-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 2.1rem;
        padding: 0.5rem 0.82rem;
        border-radius: 999px;
        border: 1px solid color-mix(in srgb, var(--accent-color) 42%, var(--border-color));
        background: transparent;
        color: color-mix(in srgb, var(--text-main) 94%, white 6%);
        text-decoration: none;
        font-size: 0.64rem;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        line-height: 1;
        box-shadow: 0 8px 18px rgba(0,0,0,0.10);
        transition: transform 0.16s ease, border-color 0.16s ease, background 0.16s ease, color 0.16s ease, box-shadow 0.16s ease;
    }

    .chat-inline-link:hover {
        color: var(--accent-color);
        border-color: var(--accent-color);
        background: color-mix(in srgb, var(--bg-color) 16%, var(--bg-panel));
        box-shadow: 0 10px 22px rgba(0,0,0,0.14);
        transform: translateY(-1px);
    }

    .chat-progress-pills {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .chat-progress-pills form {
        margin: 0;
    }

    .chat-progress-pill {
        appearance: none;
        border: 1px solid color-mix(in srgb, var(--stage-color) 50%, var(--border-color));
        background: color-mix(in srgb, var(--bg-color) 24%, var(--bg-panel));
        color: color-mix(in srgb, var(--stage-color) 82%, var(--text-main));
        border-radius: 999px;
        padding: 0.55rem 0.82rem;
        font: inherit;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.16s ease, border-color 0.16s ease, color 0.16s ease, transform 0.16s ease;
    }

    .chat-progress-pill:hover {
        transform: translateY(-1px);
        border-color: var(--stage-color);
    }

    .chat-progress-pill.is-active {
        background: var(--stage-color);
        color: #000;
        border-color: var(--stage-color);
        box-shadow: 0 8px 18px color-mix(in srgb, var(--stage-color) 22%, transparent);
    }

    .chat-action-form {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }

    .chat-tracker-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .chat-action-field-label {
        font-size: 0.72rem;
        font-family: var(--font-mono);
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--text-muted);
    }

    .chat-mini-input {
        border-radius: 12px;
        padding: 0.82rem 0.9rem;
        margin-bottom: 0.1rem;
        background: color-mix(in srgb, var(--bg-color) 42%, var(--bg-panel));
        border: 1px solid color-mix(in srgb, var(--accent-color) 12%, var(--border-color));
        color: var(--text-main);
    }

    .chat-mini-input::placeholder {
        color: color-mix(in srgb, var(--text-muted) 88%, white 12%);
    }

    .chat-action-btn {
        width: 100%;
        justify-content: center;
    }

    .chat-action-btn--danger {
        border-color: #ff7b7b;
        color: #ff7b7b;
    }

    .chat-action-summary {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        padding: 0.9rem 0.95rem;
        margin-bottom: 0.85rem;
        border-radius: 16px;
        background: color-mix(in srgb, var(--bg-color) 32%, var(--bg-panel));
        border: 1px solid color-mix(in srgb, var(--accent-color) 12%, var(--border-color));
    }

    .chat-action-summary--danger {
        margin-bottom: 0;
        background: color-mix(in srgb, #ff7b7b 8%, var(--bg-panel));
        border-color: color-mix(in srgb, #ff7b7b 22%, var(--border-color));
    }

    .chat-action-summary--tight {
        margin-bottom: 1rem;
    }

    .chat-action-summary-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .chat-action-summary-copy {
        font-size: 0.9rem;
        line-height: 1.6;
        color: var(--text-muted);
    }

    .chat-undo-form {
        margin-top: 0.85rem;
    }

    .chat-undo-link {
        border: 0;
        background: transparent;
        padding: 0;
        color: var(--text-muted);
        font-size: 0.8rem;
        text-decoration: underline;
        text-underline-offset: 0.18em;
        cursor: pointer;
        align-self: flex-start;
    }

    .chat-undo-link:hover {
        color: var(--text-main);
    }

    .chat-decision-stack {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .chat-decision-card {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding: 0.95rem;
        border-radius: 18px;
        border: 1px solid color-mix(in srgb, var(--accent-color) 12%, var(--border-color));
        background: color-mix(in srgb, var(--bg-color) 30%, var(--bg-panel));
    }

    .chat-decision-card--primary {
        border-color: color-mix(in srgb, var(--accent-color) 32%, var(--border-color));
        box-shadow: 0 0 0 1px color-mix(in srgb, var(--accent-color) 14%, transparent);
    }

    .chat-decision-card--danger {
        border-color: color-mix(in srgb, #ff7b7b 30%, var(--border-color));
    }

    .chat-decision-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.8rem;
    }

    .chat-decision-title {
        font-size: 0.98rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.18rem;
    }

    .chat-decision-copy {
        font-size: 0.86rem;
        line-height: 1.55;
        color: var(--text-muted);
    }

    .chat-decision-btn {
        width: auto;
        min-width: 96px;
        flex-shrink: 0;
    }

    .chat-action-grid--compact {
        margin-top: 0.9rem;
    }

    .chat-tracker-btn {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
        gap: 0.28rem;
        padding: 0.8rem 0.9rem;
        min-height: 72px;
        white-space: normal;
        border-radius: 16px;
    }

    .chat-tracker-btn-title {
        font-weight: 600;
    }

    .chat-tracker-btn-hint {
        font-size: 0.72rem;
        line-height: 1.4;
        opacity: 0.88;
    }

    .chat-message-stream {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 1rem 1.1rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        min-height: 0;
        background:
            linear-gradient(180deg, color-mix(in srgb, var(--bg-color) 18%, transparent), transparent 14%),
            linear-gradient(180deg, transparent, color-mix(in srgb, var(--bg-color) 10%, transparent));
    }

    .chat-message-row {
        display: flex;
    }

    .chat-message-row.is-mine {
        justify-content: flex-end;
    }

    .chat-message-row.is-system {
        justify-content: center;
    }

    .chat-message-bubble {
        max-width: min(760px, 82%);
        border-radius: 20px;
        padding: 0.95rem 1rem;
        border: 1px solid color-mix(in srgb, var(--accent-color) 10%, var(--border-color));
        background: color-mix(in srgb, var(--bg-color) 34%, var(--bg-panel));
        box-shadow: 0 12px 28px rgba(0,0,0,0.12);
    }

    .chat-message-row.is-mine .chat-message-bubble {
        background: color-mix(in srgb, var(--accent-color) 12%, var(--bg-color));
        border-color: color-mix(in srgb, var(--accent-color) 18%, var(--border-color));
    }

    .chat-message-row.is-system .chat-message-bubble {
        max-width: min(860px, 94%);
        background: var(--accent-dim);
        border-color: color-mix(in srgb, var(--accent-color) 34%, var(--border-color));
    }

    .chat-message-meta {
        margin-bottom: 0.35rem;
    }

    .chat-message-body {
        line-height: 1.7;
    }

    .chat-markdown-body > :first-child {
        margin-top: 0;
    }

    .chat-markdown-body > :last-child {
        margin-bottom: 0;
    }

    .chat-markdown-body p,
    .chat-markdown-body ul,
    .chat-markdown-body ol,
    .chat-markdown-body blockquote,
    .chat-markdown-body pre,
    .chat-markdown-body h1,
    .chat-markdown-body h2,
    .chat-markdown-body h3,
    .chat-markdown-body h4 {
        margin: 0 0 0.75rem;
    }

    .chat-markdown-body ul,
    .chat-markdown-body ol {
        padding-left: 1.25rem;
    }

    .chat-markdown-body code {
        background: color-mix(in srgb, var(--bg-color) 60%, #000);
        border: 1px solid color-mix(in srgb, var(--accent-color) 10%, var(--border-color));
        border-radius: 8px;
        padding: 0.1rem 0.35rem;
        font-size: 0.9em;
    }

    .chat-markdown-body pre {
        background: color-mix(in srgb, var(--bg-color) 72%, #000);
        border: 1px solid color-mix(in srgb, var(--accent-color) 12%, var(--border-color));
        border-radius: 12px;
        padding: 0.85rem 0.95rem;
        overflow: auto;
    }

    .chat-markdown-body pre code {
        background: transparent;
        border: 0;
        padding: 0;
    }

    .chat-markdown-body blockquote {
        margin-left: 0;
        padding-left: 0.9rem;
        border-left: 3px solid color-mix(in srgb, var(--accent-color) 45%, var(--border-color));
        color: var(--text-muted);
    }

    .chat-markdown-body a {
        color: var(--accent-color);
        text-decoration: underline;
        text-underline-offset: 0.15em;
    }

    .chat-reference-label {
        color: var(--text-muted);
        font-size: 0.66rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .chat-composer-hint {
        color: var(--text-muted);
        font-size: 0.82rem;
        letter-spacing: 0;
        text-transform: none;
    }

    .chat-reference-grid,
    .chat-attachment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.75rem;
        margin-top: 0.75rem;
    }

    .chat-reference-card,
    .chat-attachment-card {
        display: block;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid color-mix(in srgb, var(--accent-color) 14%, var(--border-color));
        background: color-mix(in srgb, var(--bg-color) 34%, var(--bg-panel));
    }

    .chat-reference-card img,
    .chat-attachment-card img {
        display: block;
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
    }

    .chat-composer-shell {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .chat-composer-frame {
        border: 1px solid color-mix(in srgb, var(--accent-color) 14%, var(--border-color));
        background: color-mix(in srgb, var(--bg-color) 55%, var(--bg-panel));
        border-radius: 18px;
        padding: 0.85rem;
    }

    .chat-composer-input {
        min-height: 92px;
        resize: vertical;
        border-radius: 14px;
        padding: 0.35rem 0.2rem 0.8rem;
        border: 0;
        background: transparent;
    }

    .chat-composer-input:focus {
        box-shadow: none;
    }

    .chat-composer-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.8rem;
        flex-wrap: wrap;
        border-top: 1px solid color-mix(in srgb, var(--accent-color) 10%, var(--border-color));
        padding-top: 0.8rem;
    }

    .chat-composer-hint-wrap {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        min-width: 0;
        flex-wrap: wrap;
        flex: 1;
    }

    .chat-attach-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.2rem;
        height: 2.2rem;
        border-radius: 999px;
        border: 1px solid color-mix(in srgb, var(--accent-color) 18%, var(--border-color));
        background: color-mix(in srgb, var(--bg-color) 36%, var(--bg-panel));
        color: var(--text-main);
        cursor: pointer;
        font-size: 1.2rem;
        line-height: 1;
        flex-shrink: 0;
    }

    .chat-file-input {
        position: absolute;
        opacity: 0;
        width: 1px;
        height: 1px;
        pointer-events: none;
    }

    .chat-attachment-preview-strip {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        flex-wrap: wrap;
        min-width: 0;
    }

    .chat-attachment-preview-item {
        position: relative;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid color-mix(in srgb, var(--accent-color) 18%, var(--border-color));
        background: color-mix(in srgb, var(--bg-color) 42%, var(--bg-panel));
        flex-shrink: 0;
    }

    .chat-attachment-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .chat-attachment-preview-remove {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 16px;
        height: 16px;
        border: 0;
        border-radius: 999px;
        background: rgba(0, 0, 0, 0.72);
        color: #fff;
        font-size: 0.7rem;
        line-height: 1;
        cursor: pointer;
        display: grid;
        place-items: center;
        padding: 0;
    }

    .chat-send-button {
        margin-left: auto;
        min-width: 0;
        padding: 0.62rem 0.95rem;
        border-radius: 999px;
        font-size: 0.88rem;
    }

    .chat-empty-thread {
        margin: auto;
        width: min(560px, 100%);
        padding: 2rem 1.4rem;
        border-radius: 24px;
        border: 1px dashed color-mix(in srgb, var(--accent-color) 24%, var(--border-color));
        background: color-mix(in srgb, var(--bg-color) 26%, var(--bg-panel));
        text-align: center;
    }

    .chat-empty-thread-illustration {
        width: 52px;
        height: 52px;
        margin: 0 auto 0.9rem;
        border-radius: 16px;
        display: grid;
        place-items: center;
        background: var(--accent-dim);
        color: var(--accent-color);
        border: 1px solid color-mix(in srgb, var(--accent-color) 22%, var(--border-color));
        font-size: 1.1rem;
    }

    .chat-empty-thread-label {
        color: var(--accent-color);
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.45rem;
    }

    .chat-empty-thread-text {
        color: var(--text-muted);
        line-height: 1.6;
    }

    .chat-composer-footer {
        margin-top: 0.75rem;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .chat-main-empty {
        height: calc(100% - 1.6rem);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        width: min(420px, calc(100% - 2rem));
        margin: 0.8rem auto;
    }

    .chat-main-empty-title {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 1280px) {
        .chat-app-shell.has-actions-rail {
            grid-template-columns: 280px minmax(0, 1fr) 260px;
        }
    }

    @media (max-width: 1100px) {
        .chat-app-shell,
        .chat-app-shell.has-actions-rail {
            grid-template-columns: 280px minmax(0, 1fr);
        }

        .chat-actions-rail {
            display: none;
        }

        .chat-action-grid,
        .chat-tracker-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 920px) {
        .chat-app-page {
            height: auto;
            min-height: calc(100vh - 73px);
            overflow: visible;
        }

        .chat-app-shell {
            height: auto;
            grid-template-columns: 1fr;
        }

        .chat-sidebar {
            max-height: 360px;
        }

        .chat-main-panel {
            min-height: 70vh;
        }

        .chat-progress-strip-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .chat-progress-strip-links {
            gap: 0.65rem;
        }

        .chat-action-grid,
        .chat-tracker-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const stream = document.getElementById('chat-message-stream');
        if (stream) stream.scrollTop = stream.scrollHeight;

        const composerForm = document.querySelector('.chat-composer');
        const composerInput = composerForm?.querySelector('.chat-composer-input');
        const fileInput = composerForm?.querySelector('[data-chat-attachments]');
        const previewStrip = composerForm?.querySelector('[data-chat-attachment-preview]');
        let queuedFiles = [];

        const applyQueuedFiles = () => {
            if (!fileInput || !previewStrip) return;

            const dt = new DataTransfer();
            queuedFiles.forEach((file) => dt.items.add(file));
            fileInput.files = dt.files;

            previewStrip.innerHTML = '';

            queuedFiles.forEach((file, index) => {
                const item = document.createElement('div');
                item.className = 'chat-attachment-preview-item';

                const img = document.createElement('img');
                img.alt = file.name;
                img.src = URL.createObjectURL(file);
                img.addEventListener('load', () => URL.revokeObjectURL(img.src), { once: true });

                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'chat-attachment-preview-remove';
                remove.setAttribute('aria-label', `Remove ${file.name}`);
                remove.textContent = '×';
                remove.addEventListener('click', () => {
                    queuedFiles = queuedFiles.filter((_, queuedIndex) => queuedIndex !== index);
                    applyQueuedFiles();
                });

                item.appendChild(img);
                item.appendChild(remove);
                previewStrip.appendChild(item);
            });
        };

        fileInput?.addEventListener('change', () => {
            const incoming = Array.from(fileInput.files || []);
            if (!incoming.length) return;

            queuedFiles = [...queuedFiles, ...incoming];
            applyQueuedFiles();
            fileInput.value = '';
        });

        composerInput?.addEventListener('keydown', (event) => {
            if ((event.ctrlKey || event.metaKey) && event.key === 'Enter' && composerForm) {
                event.preventDefault();
                composerForm.requestSubmit();
            }
        });
    });
</script>
@endpush
