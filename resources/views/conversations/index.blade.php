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
                                <div class="chat-empty-thread-text">Start the thread with a message, send an image, or use the artist tools on the right to move the commission forward.</div>
                            </div>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('conversations.messages.store', $activeConversation) }}" enctype="multipart/form-data" class="chat-composer">
                        @csrf
                        <div class="chat-composer-shell">
                            <div class="chat-composer-frame">
                                <textarea name="message" maxlength="4000" placeholder="Message {{ $otherParty->name ?? 'them' }}..." class="chat-composer-input">{{ old('message') }}</textarea>
                                <div class="chat-composer-toolbar">
                                    <div class="chat-composer-hint-wrap">
                                        <label class="chat-attach-button" title="Attach images">
                                            <input name="attachments[]" type="file" accept="image/*" multiple class="chat-file-input" data-chat-attachments>
                                            <span>＋</span>
                                        </label>
                                        <div class="chat-attachment-preview-strip" data-chat-attachment-preview></div>
                                        <div class="mono chat-composer-hint">Markdown works here. Text, images, or both. Ctrl+Enter sends.</div>
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
                    <section class="chat-action-card">
                        <div class="chat-action-card-label mono">Overview</div>
                        <div class="chat-action-card-title">Thread details</div>
                        <div class="chat-detail-stack">
                            <div class="chat-detail-item">
                                <span class="chat-detail-label mono">Conversation</span>
                                <span class="chat-detail-value">{{ $activeConversation->title ?: ('Chat with ' . ($otherParty->name ?? 'User')) }}</span>
                            </div>
                            <div class="chat-detail-item">
                                <span class="chat-detail-label mono">With</span>
                                <span class="chat-detail-value">{{ $otherParty->name ?? 'Unknown User' }} /{{ $otherParty->username ?? 'user' }}</span>
                            </div>
                            @if($commissionRequest)
                                <div class="chat-detail-item">
                                    <span class="chat-detail-label mono">Status</span>
                                    <span class="chat-pill mono" style="border-color: {{ $statusColors[$commissionRequest->status] ?? 'var(--accent-color)' }}; color: {{ $statusColors[$commissionRequest->status] ?? 'var(--accent-color)' }}; background: color-mix(in srgb, {{ $statusColors[$commissionRequest->status] ?? 'var(--accent-color)' }} 12%, transparent); width: fit-content;">{{ $statusLabels[$commissionRequest->status] ?? ucfirst($commissionRequest->status) }}</span>
                                </div>
                                @if($commissionRequest->tracker_stage)
                                    <div class="chat-detail-item">
                                        <span class="chat-detail-label mono">Stage</span>
                                        <span class="chat-pill mono" style="border-color: {{ $trackerColors[$commissionRequest->tracker_stage] ?? 'var(--accent-color)' }}; color: {{ $trackerColors[$commissionRequest->tracker_stage] ?? 'var(--accent-color)' }}; background: color-mix(in srgb, {{ $trackerColors[$commissionRequest->tracker_stage] ?? 'var(--accent-color)' }} 12%, transparent); width: fit-content;">{{ $trackerLabels[$commissionRequest->tracker_stage] ?? ucfirst($commissionRequest->tracker_stage) }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </section>

                    <section class="chat-action-card">
                        <div class="chat-action-card-label mono">Danger zone</div>
                        <div class="chat-action-card-title">Delete thread</div>
                        <div class="chat-detail-value" style="color: var(--text-muted);">Either participant can delete this entire chat. If this is a commission thread, the related request, messages, references, and workspace items will be removed too.</div>
                        <form method="POST" action="{{ route('conversations.destroy', $activeConversation) }}" onsubmit="return confirm('Delete this entire chat? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-ghost chat-action-btn chat-action-btn--danger">Delete Chat</button>
                        </form>
                    </section>

                    @if($activityMessages->isNotEmpty())
                        <section class="chat-action-card">
                            <div class="chat-action-card-label mono">Activity</div>
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
                            <div class="chat-action-card-label mono">Artist actions</div>
                            <div class="chat-action-grid chat-action-grid--stacked">
                                <form method="POST" action="{{ route('artist.requests.respond', $commissionRequest) }}" class="chat-action-form">@csrf<input type="hidden" name="action" value="accepted"><input name="reason" placeholder="Optional acceptance note" class="chat-mini-input"><button class="btn btn-primary chat-action-btn">Accept</button></form>
                                <form method="POST" action="{{ route('artist.requests.respond', $commissionRequest) }}" class="chat-action-form">@csrf<input type="hidden" name="action" value="declined"><input name="reason" placeholder="Optional decline reason" class="chat-mini-input"><button class="btn btn-ghost chat-action-btn chat-action-btn--danger">Decline</button></form>
                                <form method="POST" action="{{ route('artist.requests.respond', $commissionRequest) }}" class="chat-action-form">@csrf<input type="hidden" name="action" value="needs_info"><input name="reason" placeholder="What info do you need?" class="chat-mini-input"><button class="btn btn-ghost chat-action-btn">Need info</button></form>
                            </div>
                            @if($commissionRequest->status !== \App\Models\CommissionRequest::STATUS_PENDING)
                                <form method="POST" action="{{ route('artist.requests.undo', $commissionRequest) }}">
                                    @csrf
                                    <button class="btn btn-ghost chat-action-btn">Undo decision</button>
                                </form>
                            @endif
                        </section>
                    @endif

                    @if($isArtistView && $commissionRequest && $commissionRequest->status === \App\Models\CommissionRequest::STATUS_ACCEPTED)
                        <section class="chat-action-card">
                            <div class="chat-action-card-label mono">Commission tracker</div>
                            <div class="chat-tracker-grid chat-tracker-grid--rail">
                                @foreach($trackerLabels as $stage => $label)
                                    <form method="POST" action="{{ route('artist.requests.tracker-stage', $commissionRequest) }}">
                                        @csrf
                                        <input type="hidden" name="tracker_stage" value="{{ $stage }}">
                                        <button class="btn {{ $commissionRequest->tracker_stage === $stage ? 'btn-primary' : 'btn-ghost' }} chat-action-btn" style="width:100%; justify-content:center; border-color: {{ $trackerColors[$stage] }}; color: {{ $commissionRequest->tracker_stage === $stage ? '#000' : $trackerColors[$stage] }}; {{ $commissionRequest->tracker_stage === $stage ? 'background:' . $trackerColors[$stage] . ';' : '' }}">{{ $label }}</button>
                                    </form>
                                @endforeach
                            </div>
                        </section>
                    @endif
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
        grid-template-columns: 300px minmax(0, 1fr) 290px;
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
        padding: 0.32rem 0.5rem;
        border-radius: 999px;
        border: 1px solid var(--border-color);
        background: color-mix(in srgb, var(--bg-color) 30%, var(--bg-panel));
        color: var(--accent-color);
        white-space: nowrap;
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
        padding: 0.9rem;
    }

    .chat-actions-scroll {
        height: 100%;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        padding-right: 0.1rem;
    }

    .chat-action-card {
        border-radius: 20px;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
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

    .chat-detail-stack {
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
    }

    .chat-detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.38rem;
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
        gap: 0.75rem;
    }

    .chat-activity-item {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        padding: 0.85rem;
        border-radius: 16px;
        background: color-mix(in srgb, var(--bg-color) 34%, var(--bg-panel));
        border: 1px solid color-mix(in srgb, var(--accent-color) 16%, var(--border-color));
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
    .chat-tracker-grid--rail {
        grid-template-columns: 1fr;
    }

    .chat-action-form {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
    }

    .chat-tracker-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .chat-mini-input {
        border-radius: 10px;
        padding: 0.72rem 0.8rem;
        margin-bottom: 0.45rem;
    }

    .chat-action-btn {
        width: 100%;
        justify-content: center;
    }

    .chat-action-btn--danger {
        border-color: #ff7b7b;
        color: #ff7b7b;
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

    .chat-reference-label,
    .chat-composer-hint {
        color: var(--text-muted);
        font-size: 0.66rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
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
