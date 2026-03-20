<div class="module-wrapper {{ $isEditMode ?? false ? 'draggable' : '' }}" 
     data-id="{{ $module->id }}" 
     data-type="links" 
     data-settings="{{ json_encode($module->settings ?? []) }}">
    @if($isEditMode ?? false)
    <div class="module-edit-overlay">
        <div class="mod-btn drag-handle" title="Drag">↕</div>
        <div class="mod-btn mod-btn-edit" title="Edit Links">✏</div>
        <div class="mod-btn mod-btn-delete" title="Delete" style="color: #ff4d4d;">🗑</div>
    </div>
    @endif
    <!-- Social Links Module -->
    <div class="profile-module-card" style="background: var(--bg-panel); border: 1px solid var(--border-color); padding: 2rem; border-radius: 8px;">
        <h4 class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1.5rem;">Connect</h4>
        <div data-role="links-list" style="display: flex; flex-direction: column; gap: 0.75rem;">
            @php
                $links = $module->settings['links'] ?? ['Twitter' => '#', 'Bluesky' => '#'];
            @endphp
            @foreach($links as $name => $url)
                <a href="{{ $url }}" target="_blank" class="profile-link-card"
                   style="padding: 0.9rem 1rem; text-decoration: none; border: 1px solid var(--border-color); border-radius: 10px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: space-between; gap: 1rem; background: color-mix(in srgb, var(--bg-color) 85%, transparent);">
                    <span style="display: inline-flex; align-items: center; gap: 0.65rem; min-width: 0;">
                        <span style="color: var(--accent-color); font-size: 0.6rem;">◆</span>
                        <span class="mono" style="font-size: 0.8rem; color: var(--text-main); text-transform: uppercase;">{{ $name }}</span>
                    </span>
                    <span class="mono" style="font-size: 0.68rem; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 50%;">{{ preg_replace('#^https?://#', '', $url) }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
