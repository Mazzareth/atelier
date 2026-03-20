@php
    $columns = [
        \App\Models\CommissionRequest::TRACKER_QUEUE => [
            'label' => 'Queued',
            'hint' => 'Accepted and lined up',
            'color' => '#8ec5ff',
        ],
        \App\Models\CommissionRequest::TRACKER_ACTIVE => [
            'label' => 'In Progress',
            'hint' => 'Currently being worked on',
            'color' => 'var(--accent-color)',
        ],
        \App\Models\CommissionRequest::TRACKER_DELIVERY => [
            'label' => 'Review / Delivery',
            'hint' => 'Sent or awaiting final tweaks',
            'color' => '#ffd166',
        ],
        \App\Models\CommissionRequest::TRACKER_DONE => [
            'label' => 'Completed',
            'hint' => 'Wrapped and delivered',
            'color' => '#52d273',
        ],
    ];
@endphp

<div class="module-wrapper {{ $isEditMode ?? false ? 'draggable' : '' }}" data-id="{{ $module->id }}" data-type="kanban_tracker" data-settings="{{ json_encode($module->settings ?? []) }}">
    @if($isEditMode ?? false)
    <div class="module-edit-overlay">
        <div class="mod-btn drag-handle" title="Drag">↕</div>
        <div class="mod-btn mod-btn-edit" title="Edit Tracker">✏</div>
        <div class="mod-btn mod-btn-delete" title="Delete" style="color: #ff4d4d;">🗑</div>
    </div>
    @endif

    <div class="profile-module-card" style="background: var(--bg-panel); border: 1px solid var(--border-color); padding: 2rem; border-radius: 8px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <div>
                <h4 data-role="kanban-title" class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin: 0 0 0.45rem;">
                    {{ $module->settings['title'] ?? 'Commission Tracker' }}
                </h4>
                <div class="serif" style="font-size: 1.55rem;">Current commissions</div>
            </div>
            <span class="mono" style="font-size: 0.65rem; color: var(--accent-color); text-transform: uppercase;">Live status</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem;" class="commission-tracker-grid">
            @foreach($columns as $stage => $meta)
                @php $items = $trackerGroups[$stage] ?? collect(); @endphp
                <div style="background: color-mix(in srgb, var(--bg-color) 88%, transparent); border: 1px solid color-mix(in srgb, {{ $meta['color'] }} 35%, var(--border-color)); border-radius: 16px; padding: 1rem; min-width: 0;">
                    <div style="display:flex; justify-content:space-between; gap:0.75rem; align-items:center; margin-bottom:0.4rem;">
                        <div class="mono" style="font-size:0.68rem; color: {{ $meta['color'] }}; text-transform:uppercase;">{{ $meta['label'] }}</div>
                        <div class="mono" style="min-width:1.5rem; height:1.5rem; border-radius:999px; background: color-mix(in srgb, {{ $meta['color'] }} 18%, transparent); color: {{ $meta['color'] }}; font-size:0.68rem; font-weight:700; display:grid; place-items:center;">{{ $items->count() }}</div>
                    </div>
                    <div class="mono" style="font-size:0.62rem; color: var(--text-muted); text-transform:uppercase; margin-bottom:0.9rem;">{{ $meta['hint'] }}</div>

                    <div style="display:flex; flex-direction:column; gap:0.75rem;">
                        @forelse($items->take(6) as $requestItem)
                            <div style="display:block; background: var(--bg-panel); border: 1px solid var(--border-color); border-radius: 12px; padding: 0.85rem;">
                                <div style="font-size:0.95rem; color: var(--text-main); line-height:1.35; margin-bottom:0.35rem; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">{{ $requestItem->title }}</div>
                                <div class="mono" style="font-size:0.62rem; color: var(--text-muted); text-transform:uppercase; margin-bottom:0.45rem;">client commission</div>
                                <div class="mono" style="font-size:0.62rem; color: {{ $meta['color'] }};">Updated {{ optional($requestItem->tracker_stage_updated_at ?? $requestItem->updated_at)->diffForHumans() }}</div>
                            </div>
                        @empty
                            <div style="min-height:110px; border-radius:12px; border:1px dashed var(--border-color); display:flex; align-items:center; justify-content:center; padding:0.9rem; text-align:center;">
                                <span class="mono" style="font-size:0.62rem; color: var(--text-muted);">Nothing here yet.</span>
                            </div>
                        @endforelse

                        @if($items->count() > 6)
                            <div class="mono" style="font-size:0.62rem; color: var(--text-muted); text-align:center; padding-top:0.2rem;">+{{ $items->count() - 6 }} more</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
