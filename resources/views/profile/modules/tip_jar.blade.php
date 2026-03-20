<div class="module-wrapper {{ $isEditMode ?? false ? 'draggable' : '' }}" data-id="{{ $module->id }}" data-type="tip_jar" data-settings="{{ json_encode($module->settings ?? []) }}">
    @if($isEditMode ?? false)
    <div class="module-edit-overlay">
        <div class="mod-btn drag-handle" title="Drag">↕</div>
        <div class="mod-btn mod-btn-edit" title="Edit Tip Jar">✏</div>
        <div class="mod-btn mod-btn-delete" title="Delete" style="color: #ff4d4d;">🗑</div>
    </div>
    @endif
    <!-- Tip Jar Module -->
    <div class="profile-module-card profile-module-card--spotlight" style="background: var(--bg-panel); border: 1px solid var(--border-color); padding: 2rem; border-radius: 8px; border-top: 4px solid var(--accent-color); text-align: center; position: relative; overflow: hidden;">
        <div class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1.5rem;">Support the Artist</div>
        
        <div data-role="tip-jar-emoji" style="font-size: 2.5rem; margin-bottom: 1rem;">{{ $module->settings['emoji'] ?? '☕' }}</div>
        <p data-role="tip-jar-message" class="serif" style="color: var(--text-main); line-height: 1.4; margin-bottom: 1.5rem; font-size: 1.1rem;">
            {{ $module->settings['message'] ?? 'Drop a coffee in the jar to support my work.' }}
        </p>

        <div class="mono" style="font-size: 0.68rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 1rem;">Quick support • no subscription needed</div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.6rem; margin-bottom: 1rem;">
            <button type="button" class="btn btn-ghost quick-tip-btn" data-amount="3" style="justify-content: center; padding: 0.65rem 0.5rem;">$3</button>
            <button type="button" class="btn btn-ghost quick-tip-btn" data-amount="5" style="justify-content: center; padding: 0.65rem 0.5rem;">$5</button>
            <button type="button" class="btn btn-ghost quick-tip-btn" data-amount="10" style="justify-content: center; padding: 0.65rem 0.5rem;">$10</button>
        </div>

        <button class="btn btn-primary" style="width: 100%; justify-content: center;">
            Send a Tip
        </button>
    </div>
</div>
