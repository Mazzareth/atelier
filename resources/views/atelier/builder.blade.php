@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; padding: 2rem 4rem; display: flex; gap: 3rem; background: var(--bg-color);">

    <!-- Available Modules (Toolbox) -->
    <div style="width: 250px; display: flex; flex-direction: column; gap: 1rem;">
        <div class="pill mono" style="border-color: var(--text-muted); color: var(--text-muted); background: var(--bg-panel);">
            <div class="dot" style="background: var(--text-muted);"></div>
            ● toolbox
        </div>
        <h3 class="serif" style="color: var(--text-main); font-size: 1.2rem;">Available Modules</h3>
        <p class="mono" style="color: var(--text-muted); font-size: 0.75rem; margin-bottom: 1rem;">Drag these onto your page.</p>

        <!-- Dummy Draggables -->
        <div id="toolbox-zone" class="sortable-zone" data-zone="toolbox" style="display: flex; flex-direction: column; gap: 1rem; min-height: 100px;">
            <div class="module-card mono" data-type="bio" style="background: var(--bg-panel); border: 1px dashed var(--text-muted); padding: 1rem; border-radius: 4px; cursor: grab; font-size: 0.8rem; color: var(--text-main);">
                ☰ [Bio / Text Block]
            </div>
            <div class="module-card mono" data-type="comm_slots" style="background: var(--bg-panel); border: 1px dashed var(--text-muted); padding: 1rem; border-radius: 4px; cursor: grab; font-size: 0.8rem; color: var(--text-main);">
                ☰ [Commission Status]
            </div>
            <div class="module-card mono" data-type="tip_jar" style="background: var(--bg-panel); border: 1px dashed var(--text-muted); padding: 1rem; border-radius: 4px; cursor: grab; font-size: 0.8rem; color: var(--text-main);">
                ☰ [Tip Jar]
            </div>
        </div>
    </div>

    <!-- Active Canvas -->
    <div style="flex: 1; border: 1px solid var(--border-color); border-radius: 8px; background: var(--bg-panel); overflow: hidden; position: relative;">
        <!-- Toolbar -->
        <div style="background: var(--bg-color); border-bottom: 1px solid var(--border-color); padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center;">
            <span class="mono" style="color: var(--text-main); font-weight: bold; font-size: 0.9rem;">Page Builder</span>
            <button id="save-layout-btn" class="btn btn-primary" style="padding: 0.4rem 1rem; font-size: 0.8rem;">Save Layout</button>
        </div>

        <div style="padding: 2rem; display: flex; flex-direction: column; gap: 2rem;">
            
            <!-- HEADER ZONE -->
            <div style="border: 2px dashed var(--border-color); padding: 1rem; min-height: 100px;">
                <div class="mono" style="color: var(--text-muted); font-size: 0.7rem; text-transform: uppercase; margin-bottom: 1rem;">[ Header Zone ]</div>
                <div id="header-zone" class="sortable-zone" data-zone="header" style="min-height: 50px; display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($modules->get('header', []) as $mod)
                        <div class="module-card mono" data-id="{{ $mod->id }}" style="background: var(--bg-color); border: 1px solid var(--border-color); border-left: 4px solid var(--accent-color); padding: 1.5rem; border-radius: 4px; cursor: grab;">
                            ☰ {{ strtoupper($mod->type) }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                <!-- MAIN ZONE -->
                <div style="border: 2px dashed var(--border-color); padding: 1rem; min-height: 300px;">
                    <div class="mono" style="color: var(--text-muted); font-size: 0.7rem; text-transform: uppercase; margin-bottom: 1rem;">[ Main Zone ]</div>
                    <div id="main-zone" class="sortable-zone" data-zone="main" style="min-height: 200px; display: flex; flex-direction: column; gap: 1rem;">
                        @foreach($modules->get('main', []) as $mod)
                            <div class="module-card mono" data-id="{{ $mod->id }}" style="background: var(--bg-color); border: 1px solid var(--border-color); border-left: 4px solid var(--accent-color); padding: 1.5rem; border-radius: 4px; cursor: grab;">
                                ☰ {{ strtoupper($mod->type) }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- SIDEBAR ZONE -->
                <div style="border: 2px dashed var(--border-color); padding: 1rem; min-height: 300px;">
                    <div class="mono" style="color: var(--text-muted); font-size: 0.7rem; text-transform: uppercase; margin-bottom: 1rem;">[ Sidebar Zone ]</div>
                    <div id="sidebar-zone" class="sortable-zone" data-zone="sidebar" style="min-height: 200px; display: flex; flex-direction: column; gap: 1rem;">
                        @foreach($modules->get('sidebar', []) as $mod)
                            <div class="module-card mono" data-id="{{ $mod->id }}" style="background: var(--bg-color); border: 1px solid var(--border-color); border-left: 4px solid var(--accent-color); padding: 1.5rem; border-radius: 4px; cursor: grab;">
                                ☰ {{ strtoupper($mod->type) }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const zones = document.querySelectorAll('.sortable-zone');
        
        zones.forEach(zone => {
            new Sortable(zone, {
                group: 'shared', // set both lists to same group
                animation: 150,
                ghostClass: 'ghost-card',
                dragClass: 'dragging-card'
            });
        });

        document.getElementById('save-layout-btn').addEventListener('click', async (e) => {
            const btn = e.target;
            btn.innerText = 'Saving...';

            const payload = { zones: {} };
            
            ['header', 'main', 'sidebar'].forEach(zoneName => {
                const zoneEl = document.getElementById(zoneName + '-zone');
                const moduleIds = Array.from(zoneEl.children).map(child => child.dataset.id).filter(id => id);
                payload.zones[zoneName] = moduleIds;
            });

            try {
                const response = await fetch('{{ route("artist.builder.save") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });
                
                if (response.ok) {
                    btn.innerText = 'Saved!';
                    setTimeout(() => btn.innerText = 'Save Layout', 2000);
                }
            } catch (err) {
                console.error(err);
                btn.innerText = 'Error';
            }
        });
    });
</script>
<style>
    .ghost-card { opacity: 0.4; background: var(--bg-color) !important; }
    .dragging-card { opacity: 1; box-shadow: 0 10px 30px rgba(0,0,0,0.5); cursor: grabbing !important; }
</style>
@endpush
