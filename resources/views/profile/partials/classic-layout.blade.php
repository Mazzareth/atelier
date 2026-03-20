<!-- Flexible Layout Partial -->
<div class="profile-main-column-wrap">
    <div id="main-zone" class="profile-main-column {{ $isEditMode ? 'sortable-zone edit-zone' : '' }}" data-zone="main">
        @foreach($modules->get('main', []) as $mod)
            @include('profile.modules.' . $mod->type, ['module' => $mod])
        @endforeach
    </div>
</div>

<div class="profile-sidebar-column-wrap {{ ($pageLayout ?? 'classic') === 'fixed_left' ? 'profile-sidebar-column-wrap--sticky' : '' }}">
    <div id="sidebar-zone" class="profile-sidebar-column {{ $isEditMode ? 'sortable-zone edit-zone' : '' }}" data-zone="sidebar">
        @foreach($modules->get('sidebar', []) as $mod)
            @include('profile.modules.' . $mod->type, ['module' => $mod])
        @endforeach
    </div>
</div>
