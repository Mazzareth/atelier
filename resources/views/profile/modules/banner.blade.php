<div class="module-wrapper {{ $isEditMode ?? false ? 'draggable' : '' }}" data-id="{{ $module->id }}" data-type="banner" data-settings="{{ json_encode($module->settings ?? []) }}">
    @if($isEditMode ?? false)
    <div class="module-edit-overlay">
        <div class="mod-btn drag-handle" title="Drag">↕</div>
        <div class="mod-btn mod-btn-edit" title="Edit Banner">✏</div>
        <div class="mod-btn mod-btn-delete" title="Delete" style="color: #ff4d4d;">🗑</div>
    </div>
    @endif
    <!-- Banner Module -->
    <div class="module-banner-box" style="width: 100%; height: 400px; background: {{ $module->settings['color'] ?? 'var(--bg-panel)' }}; border-bottom: 1px solid var(--border-color); position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; border-radius: 16px; margin-bottom: 2rem; box-shadow: inset 0 0 100px rgba(0,0,0,0.2);">
        <img class="module-banner-image" src="{{ $module->settings['image'] ?? '' }}" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; z-index: 1; display: {{ !empty($module->settings['image']) ? 'block' : 'none' }};">
        <div class="watermark" style="font-size: 15vw; color: rgba(255,255,255,0.02); pointer-events: none; z-index: 2;">{{ strtoupper($artist->username ?? '') }}</div>
        @if($isEditMode ?? false)
        <div class="module-banner-placeholder mono" style="color: var(--text-muted); opacity: 0.5; display: {{ empty($module->settings['image']) ? 'block' : 'none' }}; z-index: 2;">[Customizable Banner Module]</div>
        @endif
    </div>
</div>
