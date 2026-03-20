<div class="module-wrapper {{ $isEditMode ?? false ? 'draggable' : '' }}" 
     data-id="{{ $module->id }}" 
     data-type="comm_slots" 
     data-settings="{{ json_encode($module->settings ?? []) }}">
    @if($isEditMode ?? false)
    <div class="module-edit-overlay">
        <div class="mod-btn drag-handle" title="Drag">↕</div>
        <div class="mod-btn mod-btn-edit" title="Edit Slots & Pricing">✏</div>
        <div class="mod-btn mod-btn-delete" title="Delete" style="color: #ff4d4d;">🗑</div>
    </div>
    @endif
    
    <!-- Commission Status Module -->
    <div style="background: var(--bg-panel); border: 1px solid var(--border-color); padding: 2rem; border-radius: 8px;">
        <div class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1.5rem;">Commission Status</div>
        
        @php
            $slotsOpen = (int)($module->settings['slots_open'] ?? 0);
            $isOpen = $slotsOpen > 0;
            $statusColor = $isOpen ? 'var(--accent-color)' : '#ff4d4d';
        @endphp

        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.2rem;">
            <div style="width: 12px; height: 12px; border-radius: 50%; background: {{ $statusColor }}; box-shadow: 0 0 10px {{ $statusColor }};"></div>
            <span data-role="comm-status-label" class="mono" style="font-size: 1rem; color: var(--text-main); font-weight: bold;">
                {{ $isOpen ? 'OPEN FOR ' . $slotsOpen . ' SLOTS' : 'COMMISSIONS CLOSED' }}
            </span>
        </div>

        <div data-role="comm-next-open" class="mono" style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem; display: {{ !empty($module->settings['next_open_date']) ? 'flex' : 'none' }}; align-items: center; gap: 0.5rem;">
            <span style="opacity: 0.5;">◆</span> Next opening: <span data-role="comm-next-open-text">{{ $module->settings['next_open_date'] ?? '' }}</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <button class="btn btn-ghost open-pricing-sheet" 
               style="width: 100%; justify-content: center; font-size: 0.8rem; border-color: var(--accent-color); color: var(--accent-color); cursor: pointer;">
                View Pricing & Quote
            </button>
            @auth
                @if(auth()->id() !== $artist->id)
                <a href="{{ route('commission.create', $artist->username) }}" class="btn btn-primary" style="width: 100%; justify-content: center; text-decoration: none;">Request Commission</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary" style="width: 100%; justify-content: center; text-decoration: none;">Log in to Request</a>
            @endauth
        </div>
    </div>
</div>
