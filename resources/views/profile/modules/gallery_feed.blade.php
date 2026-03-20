<div class="module-wrapper {{ $isEditMode ?? false ? 'draggable' : '' }}" data-id="{{ $module->id }}" data-type="gallery_feed" data-settings="{{ json_encode($module->settings ?? []) }}">
    @if($isEditMode ?? false)
    <div class="module-edit-overlay">
        <div class="mod-btn drag-handle" title="Drag">↕</div>
        <div class="mod-btn mod-btn-edit" title="Edit Gallery">✏</div>
        <div class="mod-btn mod-btn-delete" title="Delete" style="color: #ff4d4d;">🗑</div>
    </div>
    @endif
    @php
        $galleryImages = collect($module->settings['images'] ?? []);
    @endphp
    <!-- Gallery Feed Module -->
    <div class="profile-module-card" style="background: var(--bg-panel); border: 1px solid var(--border-color); padding: 2rem; border-radius: 8px; min-height: 400px; display: flex; flex-direction: column; gap: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
            <div>
                <div class="mono" style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.35rem;">Featured Work</div>
                <div class="serif" style="font-size: 1.5rem; color: var(--text-main);">Gallery Feed</div>
            </div>
            <span data-role="gallery-layout-pill" class="mono" style="font-size: 0.65rem; color: var(--accent-color); text-transform: uppercase; padding: 0.35rem 0.6rem; border: 1px solid var(--accent-color); border-radius: 999px; background: var(--accent-dim);">{{ $module->settings['layout'] ?? 'grid' }}</span>
        </div>

        @if($galleryImages->isNotEmpty())
            <div data-role="gallery-preview" class="gallery-feed-preview gallery-feed-preview--{{ $module->settings['layout'] ?? 'grid' }}">
                @foreach($galleryImages as $imageIndex => $image)
                    @php
                        $imageUrl = !empty($image['path'])
                            ? route('artist.profile.gallery.show', [$module->id, $imageIndex])
                            : ($image['url'] ?? '');
                    @endphp
                    <div class="gallery-preview-card gallery-preview-card--image" style="position: relative; overflow: hidden;">
                        <img src="{{ $imageUrl }}" alt="{{ $image['name'] ?? 'Gallery image' }}" style="width: 100%; height: 100%; object-fit: cover; display:block;">
                        @if($isEditMode ?? false)
                            <form method="POST" action="{{ route('artist.profile.gallery.delete', [$module->id, $imageIndex]) }}" style="position:absolute; top:0.55rem; right:0.55rem; z-index:2;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="mono" style="width:1.8rem; height:1.8rem; border-radius:999px; border:1px solid rgba(255,255,255,0.14); background:rgba(0,0,0,0.7); color:#fff; cursor:pointer;">×</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div data-role="gallery-preview" class="gallery-feed-preview gallery-feed-preview--{{ $module->settings['layout'] ?? 'grid' }}">
                <div class="gallery-preview-card"></div>
                <div class="gallery-preview-card"></div>
                <div class="gallery-preview-card"></div>
                <div class="gallery-preview-card"></div>
                <div class="gallery-preview-card"></div>
                <div class="gallery-preview-card"></div>
            </div>
        @endif

        @if($isEditMode ?? false)
            <form method="POST" action="{{ route('artist.profile.gallery.upload', $module->id) }}" enctype="multipart/form-data" style="display:flex; flex-wrap:wrap; gap:0.75rem; align-items:center;">
                @csrf
                <input type="file" name="images[]" accept="image/*" multiple style="max-width: 320px; background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.7rem 0.85rem; border-radius: 10px;">
                <button type="submit" class="btn btn-primary" style="padding:0.7rem 1rem;">Add Photos</button>
                <span class="mono" style="color: var(--text-muted); font-size: 0.72rem;">Upload gallery images right here.</span>
            </form>
        @else
            <span class="mono" style="color: var(--text-muted); font-size: 0.72rem;">Selected work from this artist’s gallery.</span>
        @endif
    </div>
</div>
