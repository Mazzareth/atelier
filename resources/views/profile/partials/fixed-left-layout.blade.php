<!-- Fixed Left Layout Partial -->
<!-- SIDEBAR ZONE (Left, Sticky) -->
<div style="position: sticky; top: 2rem; height: calc(100vh - 4rem); overflow-y: auto; overflow-x: hidden;">
    <div id="sidebar-zone" class="{{ $isEditMode ? 'sortable-zone edit-zone' : '' }}" data-zone="sidebar" 
         style="display: flex; flex-direction: column; gap: 2rem; min-height: 100px; width: 100%;">
        @foreach($modules->get('sidebar', []) as $mod)
            @include('profile.modules.' . $mod->type, ['module' => $mod])
        @endforeach
    </div>
</div>

<!-- MAIN ZONE (Right, Scrollable) -->
<div id="main-zone" class="{{ $isEditMode ? 'sortable-zone edit-zone' : '' }}" data-zone="main" 
     style="display: flex; flex-direction: column; gap: 2rem; min-height: 100px; overflow-x: hidden;">
    @foreach($modules->get('main', []) as $mod)
        @include('profile.modules.' . $mod->type, ['module' => $mod])
    @endforeach
</div>
