@props(['module', 'isEditMode' => false, 'type' => 'generic'])

<div 
    class="profile-module-wrapper relative {{ $isEditMode ? 'profile-module-draggable' : '' }} {{ $attributes->get('class') }}" 
    data-id="{{ $module->id ?? '' }}" 
    data-type="{{ $type }}" 
    data-settings="{{ json_encode($module->settings ?? []) }}"
    {{ $attributes->except('class') }}
>
    @if($isEditMode)
        <div class="absolute top-2 right-2 flex gap-2 z-20 opacity-0 transition-opacity duration-200 group-hover:opacity-100 bg-surface/90 p-1.5 rounded-lg border border-border/50 shadow-sm backdrop-blur-sm">
            <button class="drag-handle hover:text-accent w-6 h-6 flex items-center justify-center rounded cursor-move text-muted" title="Drag">↕</button>
            <button class="mod-btn-edit hover:text-accent w-6 h-6 flex items-center justify-center rounded text-muted" title="Edit">✏</button>
            <button class="mod-btn-delete hover:text-red-500 w-6 h-6 flex items-center justify-center rounded text-muted" title="Delete">🗑</button>
        </div>
    @endif

    <div class="profile-module-content">
        {{ $slot }}
    </div>
</div>
