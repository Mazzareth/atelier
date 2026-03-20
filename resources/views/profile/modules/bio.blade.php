<div class="module-wrapper {{ $isEditMode ?? false ? 'draggable' : '' }}" data-id="{{ $module->id }}" data-type="bio" data-settings="{{ json_encode($module->settings ?? []) }}">
    @if($isEditMode ?? false)
    <div class="module-edit-overlay">
        <div class="mod-btn drag-handle" title="Drag">↕</div>
        <div class="mod-btn mod-btn-edit" title="Edit Text">✏</div>
        <div class="mod-btn mod-btn-delete" title="Delete" style="color: #ff4d4d;">🗑</div>
    </div>
    @endif
    <!-- Bio / Text Module -->
    <div style="background: var(--bg-panel); border: 1px solid var(--border-color); padding: 2.5rem; border-radius: 8px; border-left: 4px solid var(--accent-color);">
        <div class="module-content" style="color: var(--text-main); line-height: 1.6; white-space: pre-wrap;">
            @php
                $text = $module->settings['text'] ?? 'Welcome to my atelier. I draw weird, wonderful, and beautiful things.';
                
                // Helper to render basic markdown for Atelier
                $html = e($text);
                
                // Headers
                $html = preg_replace('/^# (.+)/m', '<h1 class="serif" style="font-size: 2.2rem; color: var(--accent-color); margin-bottom: 1rem;">$1</h1>', $html);
                $html = preg_replace('/^## (.+)/m', '<h2 class="serif" style="font-size: 1.8rem; color: var(--accent-color); margin-bottom: 0.8rem;">$1</h2>', $html);
                $html = preg_replace('/^### (.+)/m', '<h3 class="serif" style="font-size: 1.4rem; color: var(--accent-color); margin-bottom: 0.6rem;">$1</h3>', $html);

                // Formatting
                $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
                $html = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $html);
                $html = preg_replace('/`(.+?)`/', '<code style="background: var(--bg-color); padding: 0.2rem 0.4rem; border-radius: 4px; color: var(--accent-color); font-family: monospace;">$1</code>', $html);
                
                // Media / Links
                $html = preg_replace('/!\[(.*?)\]\((.+?)\)/', '<img src="$2" alt="$1" style="max-width: 100%; border-radius: 8px; margin: 1rem 0; border: 1px solid var(--border-color);">', $html);
                $html = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2" target="_blank" style="color: var(--accent-color); text-decoration: underline;">$1</a>', $html);
                
                // Structure
                $html = preg_replace('/^&gt; (.+)/m', '<blockquote style="border-left: 4px solid var(--accent-color); padding: 0.5rem 1rem; color: var(--text-muted); background: var(--bg-color); border-radius: 0 4px 4px 0; margin: 1rem 0;">$1</blockquote>', $html);
                $html = preg_replace('/^- (.+)/m', '<li style="margin-left: 1.5rem;">$1</li>', $html);
                
                $html = nl2br($html);
            @endphp
            {!! $html !!}
        </div>
    </div>
</div>
