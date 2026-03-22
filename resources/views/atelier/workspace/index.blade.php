@extends('layouts.app')

@php
    $trackerLabels = [
        \App\Models\CommissionRequest::TRACKER_QUEUE => 'Queued',
        \App\Models\CommissionRequest::TRACKER_ACTIVE => 'In Progress',
        \App\Models\CommissionRequest::TRACKER_DELIVERY => 'Review / Delivery',
        \App\Models\CommissionRequest::TRACKER_DONE => 'Completed',
    ];
@endphp

@section('content')
<div class="workspace-page">
    <div class="workspace-shell">
        <aside class="workspace-sidebar">
            <div class="workspace-sidebar-top">
                <div class="workspace-sidebar-intro">
                    <div class="workspace-eyebrow mono">Workspace</div>
                    <h1 class="workspace-page-title">Artist Workspace</h1>
                    <p class="workspace-page-copy">Every commission on the left, active board on the right. The structure stays stable so the content can breathe.</p>
                </div>

                <details class="workspace-manual-box">
                    <summary class="mono">+ Add manual commission</summary>
                    <form method="POST" action="{{ route('artist.workspace.manual.store') }}" class="workspace-manual-form">
                        @csrf
                        <input name="client_name" placeholder="Client name" required>
                        <input name="client_contact" placeholder="Contact / handle / email">
                        <input name="title" placeholder="Commission title" required>
                        <input name="budget" type="number" min="0" step="0.01" placeholder="Budget">
                        <select name="tracker_stage">
                            @foreach($trackerLabels as $stage => $label)
                                <option value="{{ $stage }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <textarea name="details" placeholder="What’s the piece? notes, delivery details, refs, etc." required></textarea>
                        <button class="btn btn-primary btn-full" type="submit">Create</button>
                    </form>
                </details>
            </div>

            <div class="workspace-thread-list">
                @forelse($commissions as $commission)
                    @php
                        $clientLabel = $commission->is_manual
                            ? ($commission->client_name ?: 'Manual client')
                            : ($commission->requester->name ?? 'Client');
                    @endphp
                    <a href="{{ route('artist.workspace.show', $commission) }}" class="workspace-thread-item {{ $activeCommission && $activeCommission->id === $commission->id ? 'is-active' : '' }}">
                        <div class="workspace-thread-meta mono">{{ strtoupper($trackerLabels[$commission->tracker_stage] ?? 'Unplaced') }}</div>
                        <div class="workspace-thread-title">{{ $commission->title }}</div>
                        <div class="workspace-thread-client mono">{{ $clientLabel }}</div>
                    </a>
                @empty
                    <div class="workspace-empty mono">No commissions yet. Add one manually or accept one from the site.</div>
                @endforelse
            </div>
        </aside>

        <main class="workspace-main">
            @if($activeCommission)
                <section class="workspace-canvas-panel">
                    <div id="workspace-dropzone" class="workspace-canvas-wrap">
                        <div class="workspace-canvas-hud">
                            <div class="workspace-commission-card">
                                <div class="mono workspace-commission-kicker">Current commission</div>
                                <div class="workspace-current-title">{{ $activeCommission->title }}</div>
                                <div class="mono workspace-commission-meta">
                                    {{ $activeCommission->is_manual ? ($activeCommission->client_name ?: 'Manual client') : ($activeCommission->requester->name ?? 'Client') }}
                                    @if($activeCommission->client_contact)
                                        • {{ $activeCommission->client_contact }}
                                    @endif
                                </div>
                                <div class="workspace-stagebar">
                                    @foreach($trackerLabels as $stage => $label)
                                        <form method="POST" action="{{ route('artist.workspace.stage', $activeCommission) }}">
                                            @csrf
                                            <input type="hidden" name="tracker_stage" value="{{ $stage }}">
                                            <button class="workspace-stage-pill {{ $activeCommission->tracker_stage === $stage ? 'is-active' : '' }}">{{ $label }}</button>
                                        </form>
                                    @endforeach
                                </div>
                            </div>

                            <div class="workspace-toolbar-card">
                                <div class="mono workspace-canvas-help">Drag empty space to pan. Right click the grid for tools. Right click a node to start a connection.</div>
                                <div class="workspace-toolbar-actions">
                                    <a href="{{ route('commission.show', $activeCommission) }}" class="btn btn-ghost">Open thread</a>
                                    <a href="{{ route('artist.artist.commissions.index') }}" class="btn btn-ghost">Full kanban</a>
                                    <button id="workspace-zoom-out-btn" class="btn btn-ghost" type="button">Zoom out</button>
                                    <button id="workspace-zoom-in-btn" class="btn btn-ghost" type="button">Zoom in</button>
                                    <button id="workspace-center-btn" class="btn btn-ghost" type="button">Return to center</button>
                                    <button id="add-note-btn" class="btn btn-ghost" type="button">Add note</button>
                                    <button id="add-group-btn" class="btn btn-ghost" type="button">Add group</button>
                                    <label class="btn btn-primary workspace-upload-trigger">
                                        Upload refs
                                        <input id="workspace-upload-input" type="file" accept="image/*" multiple hidden>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div id="workspace-context-menu" class="workspace-context-menu is-hidden">
                            <button type="button" data-action="import-image">Import image</button>
                            <button type="button" data-action="new-text">New text</button>
                            <button type="button" data-action="new-group">New group</button>
                            <button type="button" data-action="add-connection" class="node-only">Add connection</button>
                        </div>
                        <div id="workspace-grid" class="workspace-grid"></div>
                        <div id="workspace-world" class="workspace-world">
                            <svg id="workspace-connections" class="workspace-connections"></svg>
                            <div id="workspace-canvas" class="workspace-canvas">
                                @foreach($activeCommission->workspaceItems as $item)
                                    <div class="workspace-item workspace-item--{{ $item->type }}" data-item-id="{{ $item->id }}" data-type="{{ $item->type }}" data-x="{{ $item->x }}" data-y="{{ $item->y }}" style="width:{{ $item->width }}px; height:{{ $item->height }}px; z-index:{{ $item->z_index }}; {{ $item->type === 'group' && $item->background ? 'background:' . $item->background . ';' : '' }}">
                                    <div class="workspace-item-header">
                                        <div class="mono workspace-item-kicker">{{ strtoupper($item->type) }}</div>
                                        <div class="workspace-item-controls">
                                            <button type="button" class="workspace-item-delete">✕</button>
                                        </div>
                                    </div>
                                        @if($item->type === 'image')
                                            <img src="{{ route('artist.workspace.items.asset', [$activeCommission, $item]) }}" alt="{{ $item->title }}" class="workspace-image">
                                            <input class="workspace-title-input mono" value="{{ $item->title }}" placeholder="Image title">
                                        @elseif($item->type === 'group')
                                            <input class="workspace-title-input mono" value="{{ $item->title }}" placeholder="Group title">
                                            <div class="workspace-group-body mono">Drop related refs and notes inside this colored region however you want.</div>
                                        @else
                                            <input class="workspace-title-input mono" value="{{ $item->title }}" placeholder="Note title">
                                            <textarea class="workspace-note-input">{{ $item->content }}</textarea>
                                            <div class="workspace-note-preview"></div>
                                        @endif
                                        <div class="workspace-resize-handle"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @else
                <div class="workspace-no-active">Pick a commission or add one manually to start building a workspace board.</div>
            @endif
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('workspace-canvas');
    const svg = document.getElementById('workspace-connections');
    const dropzone = document.getElementById('workspace-dropzone');
    const grid = document.getElementById('workspace-grid');
    const world = document.getElementById('workspace-world');
    const uploadInput = document.getElementById('workspace-upload-input');
    const zoomOutBtn = document.getElementById('workspace-zoom-out-btn');
    const zoomInBtn = document.getElementById('workspace-zoom-in-btn');
    const centerBtn = document.getElementById('workspace-center-btn');
    const addNoteBtn = document.getElementById('add-note-btn');
    const addGroupBtn = document.getElementById('add-group-btn');
    const menu = document.getElementById('workspace-context-menu');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const commissionId = @json($activeCommission?->id);
    const existingConnections = @json(($activeCommission?->workspaceConnections ?? collect())->map(fn($c) => ['id' => $c->id, 'from_workspace_item_id' => $c->from_workspace_item_id, 'to_workspace_item_id' => $c->to_workspace_item_id])->values()->all());
    if (!canvas || !commissionId) return;

    const uploadUrl = @json($activeCommission ? route('artist.workspace.items.upload', $activeCommission) : null);
    const noteUrl = @json($activeCommission ? route('artist.workspace.items.note', $activeCommission) : null);
    const groupUrl = @json($activeCommission ? route('artist.workspace.items.group', $activeCommission) : null);
    const connectionUrl = @json($activeCommission ? route('artist.workspace.connections.store', $activeCommission) : null);
    const connectionDeleteBase = @json($activeCommission ? url('/atelier/workspace/' . $activeCommission->id . '/connections') : null);
    const updateBase = @json($activeCommission ? url('/atelier/workspace/' . $activeCommission->id . '/items') : null);

    let menuTarget = null;
    let menuPoint = { x: 120, y: 120 };
    let pendingConnectionFrom = null;
    let tempPath = null;
    let isPanning = false;
    let panStartX = 0;
    let panStartY = 0;
    let cameraX = 0;
    let cameraY = 0;
    let panCameraX = 0;
    let panCameraY = 0;
    let zoom = 1;
    let pinchStartDistance = 0;
    let pinchStartZoom = 1;
    let pinchAnchor = null;
    const connections = [...existingConnections];
    const BOARD_SIZE = 24000;
    const WORLD_CENTER = BOARD_SIZE / 2;

    function renderMarkdown(text='') {
        return String(text)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/^# (.+)/gm, '<h1 class="workspace-markdown-h1">$1</h1>')
            .replace(/^## (.+)/gm, '<h2 class="workspace-markdown-h2">$1</h2>')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.+?)\*/g, '<em>$1</em>')
            .replace(/\[(.+?)\]\((.+?)\)/g, '<a href="$2" target="_blank" class="workspace-markdown-link">$1</a>')
            .replace(/^- (.+)/gm, '<li class="workspace-markdown-li">$1</li>')
            .replace(/\n/g, '<br>');
    }

    function gridSnap(n) { return Math.round(n / 24) * 24; }

    function setItemPosition(el, x, y) {
        el.dataset.x = String(x);
        el.dataset.y = String(y);
        el.style.left = `${WORLD_CENTER + x}px`;
        el.style.top = `${WORLD_CENTER + y}px`;
    }

    function applyCamera() {
        world.style.transform = `translate3d(${cameraX}px, ${cameraY}px, 0) scale(${zoom})`;
        if (grid) {
            grid.style.backgroundSize = `${24 * zoom}px ${24 * zoom}px`;
            grid.style.backgroundPosition = `${cameraX}px ${cameraY}px, ${cameraX}px ${cameraY}px`;
        }
    }

    function resetCamera() {
        cameraX = Math.round(dropzone.clientWidth / 2) - WORLD_CENTER;
        cameraY = Math.round(dropzone.clientHeight / 2) - WORLD_CENTER;
        applyCamera();
        drawConnections();
    }

    function viewportToCanvasPoint(clientX, clientY) {
        const rect = dropzone.getBoundingClientRect();
        return {
            x: (clientX - rect.left - cameraX) / zoom - WORLD_CENTER,
            y: (clientY - rect.top - cameraY) / zoom - WORLD_CENTER,
        };
    }

    function viewportToBoardPoint(clientX, clientY) {
        const worldPoint = viewportToCanvasPoint(clientX, clientY);
        return {
            x: WORLD_CENTER + worldPoint.x,
            y: WORLD_CENTER + worldPoint.y,
        };
    }

    function nodeCenter(el) {
        return {
            x: WORLD_CENTER + (parseInt(el.dataset.x) || 0) + (el.offsetWidth / 2),
            y: WORLD_CENTER + (parseInt(el.dataset.y) || 0) + (el.offsetHeight / 2),
        };
    }

    function orthPath(a, b) {
        const midX = gridSnap((a.x + b.x) / 2);
        return `M ${gridSnap(a.x)} ${gridSnap(a.y)} L ${midX} ${gridSnap(a.y)} L ${midX} ${gridSnap(b.y)} L ${gridSnap(b.x)} ${gridSnap(b.y)}`;
    }

    function drawConnections() {
        svg.innerHTML = '';
        connections.forEach((conn) => {
            const from = canvas.querySelector(`.workspace-item[data-item-id="${conn.from_workspace_item_id}"]`);
            const to = canvas.querySelector(`.workspace-item[data-item-id="${conn.to_workspace_item_id}"]`);
            if (!from || !to) return;
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', orthPath(nodeCenter(from), nodeCenter(to)));
            path.setAttribute('class', 'workspace-connection-line');
            path.dataset.connectionId = conn.id;
            path.style.pointerEvents = 'auto';
            path.addEventListener('click', async (e) => {
                e.stopPropagation();
                await fetch(`${connectionDeleteBase}/${conn.id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
                const idx = connections.findIndex(c => String(c.id) === String(conn.id));
                if (idx > -1) connections.splice(idx, 1);
                drawConnections();
            });
            svg.appendChild(path);
        });
    }

    function setZoom(nextZoom, anchorClientX = null, anchorClientY = null) {
        const clamped = Math.min(1.8, Math.max(0.45, nextZoom));
        if (clamped === zoom) return;

        if (anchorClientX === null || anchorClientY === null) {
            const rect = dropzone.getBoundingClientRect();
            anchorClientX = rect.left + rect.width / 2;
            anchorClientY = rect.top + rect.height / 2;
        }

        const rect = dropzone.getBoundingClientRect();
        const boardX = (anchorClientX - rect.left - cameraX) / zoom;
        const boardY = (anchorClientY - rect.top - cameraY) / zoom;
        zoom = clamped;
        cameraX = anchorClientX - rect.left - (boardX * zoom);
        cameraY = anchorClientY - rect.top - (boardY * zoom);
        applyCamera();
        drawConnections();
    }

    function nudgeZoom(direction) {
        const factor = direction > 0 ? 1.12 : (1 / 1.12);
        setZoom(Number((zoom * factor).toFixed(3)));
    }

    function hideMenu() {
        if (!menu) return;
        menu.classList.add('is-hidden');
        menu.classList.remove('is-node-target');
    }

    function showMenu(viewportX, viewportY, targetItem = null) {
        if (!menu) return;
        menuTarget = targetItem;
        menu.style.left = `${viewportX}px`;
        menu.style.top = `${viewportY}px`;
        menu.classList.toggle('is-node-target', Boolean(targetItem));
        menu.classList.remove('is-hidden');
    }

    async function postJson(url, payload) {
        const res = await fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body: JSON.stringify(payload) });
        return res.json();
    }

    function wireItem(el) {
        let dragging = false, resizing = false, startX=0, startY=0, startLeft=0, startTop=0, startWidth=0, startHeight=0;
        let saveTimer = null;
        const header = el.querySelector('.workspace-item-header');
        const resize = el.querySelector('.workspace-resize-handle');
        const deleteBtn = el.querySelector('.workspace-item-delete');
        const titleInput = el.querySelector('.workspace-title-input');
        const noteInput = el.querySelector('.workspace-note-input');
        const preview = el.querySelector('.workspace-note-preview');
        setItemPosition(el, parseInt(el.dataset.x) || 0, parseInt(el.dataset.y) || 0);
        if (noteInput && preview) { const sync = () => preview.innerHTML = renderMarkdown(noteInput.value || ''); noteInput.addEventListener('input', sync); sync(); }

        async function persistItem() {
            const itemId = el.dataset.itemId;
            await fetch(`${updateBase}/${itemId}`, {
                method:'PATCH',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
                body: JSON.stringify({
                    title: titleInput?.value || '',
                    content: noteInput?.value || '',
                    x: parseInt(el.dataset.x)||0,
                    y: parseInt(el.dataset.y)||0,
                    width: el.offsetWidth,
                    height: el.offsetHeight,
                    z_index: parseInt(el.style.zIndex)||1,
                })
            });
            drawConnections();
        }

        function queuePersist(delay = 350) {
            window.clearTimeout(saveTimer);
            saveTimer = window.setTimeout(() => {
                persistItem().catch(() => {});
            }, delay);
        }

        header?.addEventListener('mousedown', (e) => {
            if (e.target.closest('button,input,textarea')) return;
            el.style.zIndex = String(Math.max(...Array.from(canvas.querySelectorAll('.workspace-item')).map((item) => parseInt(item.style.zIndex) || 1), 1) + 1);
            dragging = true; startX = e.clientX; startY = e.clientY; startLeft = parseInt(el.dataset.x)||0; startTop = parseInt(el.dataset.y)||0; e.preventDefault();
        });
        el.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            const rect = dropzone.getBoundingClientRect();
            menuPoint = viewportToCanvasPoint(e.clientX, e.clientY);
            showMenu(e.clientX - rect.left, e.clientY - rect.top, el);
        });
        resize?.addEventListener('mousedown', (e) => { resizing = true; startX = e.clientX; startY = e.clientY; startWidth = el.offsetWidth; startHeight = el.offsetHeight; e.preventDefault(); e.stopPropagation(); });
        document.addEventListener('mousemove', (e) => {
            if (dragging) { setItemPosition(el, gridSnap(startLeft + (e.clientX - startX)), gridSnap(startTop + (e.clientY - startY))); drawConnections(); }
            if (resizing) { el.style.width = Math.max(120, gridSnap(startWidth + (e.clientX - startX))) + 'px'; el.style.height = Math.max(80, gridSnap(startHeight + (e.clientY - startY))) + 'px'; drawConnections(); }
            if (pendingConnectionFrom && tempPath) {
                const from = nodeCenter(pendingConnectionFrom);
                const to = viewportToBoardPoint(e.clientX, e.clientY);
                tempPath.setAttribute('d', orthPath(from, to));
            }
            if (isPanning) {
                cameraX = panCameraX + (e.clientX - panStartX);
                cameraY = panCameraY + (e.clientY - panStartY);
                applyCamera();
                drawConnections();
            }
        });
        document.addEventListener('mouseup', () => {
            const shouldPersist = dragging || resizing;
            dragging = false;
            resizing = false;
            if (shouldPersist) queuePersist(0);
        });
        el.addEventListener('click', async (e) => {
            if (pendingConnectionFrom && pendingConnectionFrom !== el) {
                const result = await postJson(connectionUrl, { from_workspace_item_id: pendingConnectionFrom.dataset.itemId, to_workspace_item_id: el.dataset.itemId });
                if (result.connection) { connections.push(result.connection); drawConnections(); }
                pendingConnectionFrom = null; tempPath?.remove(); tempPath = null;
                e.stopPropagation();
            }
        });
        titleInput?.addEventListener('input', () => queuePersist());
        titleInput?.addEventListener('blur', () => queuePersist(0));
        noteInput?.addEventListener('input', () => queuePersist());
        noteInput?.addEventListener('blur', () => queuePersist(0));
        deleteBtn?.addEventListener('click', async () => {
            window.clearTimeout(saveTimer);
            const itemId = el.dataset.itemId;
            await fetch(`${updateBase}/${itemId}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'} });
            el.remove();
            for (let i = connections.length - 1; i >= 0; i--) {
                if (String(connections[i].from_workspace_item_id) === String(itemId) || String(connections[i].to_workspace_item_id) === String(itemId)) connections.splice(i, 1);
            }
            drawConnections();
        });
    }

    document.querySelectorAll('.workspace-item').forEach(wireItem);
    drawConnections();

    async function uploadFiles(files) {
        for (const file of files) {
            const data = new FormData(); data.append('asset', file);
            const res = await fetch(uploadUrl, { method:'POST', headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}, body:data });
            if (res.ok) window.location.reload();
        }
    }

    uploadInput?.addEventListener('change', () => { if (uploadInput.files?.length) uploadFiles(uploadInput.files); });
    dropzone?.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('is-dragover'); });
    dropzone?.addEventListener('dragleave', () => dropzone.classList.remove('is-dragover'));
    dropzone?.addEventListener('drop', (e) => { e.preventDefault(); dropzone.classList.remove('is-dragover'); if (e.dataTransfer.files?.length) uploadFiles(e.dataTransfer.files); });
    dropzone?.addEventListener('mousedown', (e) => {
        if (e.button !== 0) return;
        if (e.target.closest('.workspace-item, .workspace-canvas-hud, .workspace-context-menu')) return;
        isPanning = true;
        panStartX = e.clientX;
        panStartY = e.clientY;
        panCameraX = cameraX;
        panCameraY = cameraY;
        dropzone.classList.add('is-panning');
        hideMenu();
        e.preventDefault();
    });
    document.addEventListener('mouseup', () => {
        isPanning = false;
        dropzone?.classList.remove('is-panning');
    });
    dropzone?.addEventListener('mouseleave', () => {
        if (!isPanning) return;
        dropzone.classList.remove('is-panning');
    });
    dropzone?.addEventListener('contextmenu', (e) => {
        if (e.target.closest('.workspace-item')) return;
        e.preventDefault();
        const rect = dropzone.getBoundingClientRect();
        menuPoint = viewportToCanvasPoint(e.clientX, e.clientY);
        showMenu(e.clientX - rect.left, e.clientY - rect.top, null);
    });
    dropzone?.addEventListener('wheel', (e) => {
        e.preventDefault();
        const direction = e.deltaY < 0 ? 1 : -1;
        const factor = direction > 0 ? 1.08 : (1 / 1.08);
        setZoom(Number((zoom * factor).toFixed(3)), e.clientX, e.clientY);
    }, { passive: false });
    dropzone?.addEventListener('touchstart', (e) => {
        if (e.touches.length !== 2) return;
        const [a, b] = e.touches;
        pinchStartDistance = Math.hypot(a.clientX - b.clientX, a.clientY - b.clientY);
        pinchStartZoom = zoom;
        pinchAnchor = {
            x: (a.clientX + b.clientX) / 2,
            y: (a.clientY + b.clientY) / 2,
        };
    }, { passive: true });
    dropzone?.addEventListener('touchmove', (e) => {
        if (e.touches.length !== 2 || !pinchAnchor) return;
        e.preventDefault();
        const [a, b] = e.touches;
        const distance = Math.hypot(a.clientX - b.clientX, a.clientY - b.clientY);
        setZoom((distance / pinchStartDistance) * pinchStartZoom, pinchAnchor.x, pinchAnchor.y);
    }, { passive: false });
    dropzone?.addEventListener('touchend', (e) => {
        if (e.touches.length < 2) pinchAnchor = null;
    });
    document.addEventListener('click', () => hideMenu());
    zoomOutBtn?.addEventListener('click', () => nudgeZoom(-1));
    zoomInBtn?.addEventListener('click', () => nudgeZoom(1));
    centerBtn?.addEventListener('click', resetCamera);
    window.addEventListener('resize', resetCamera);

    menu?.addEventListener('click', async (e) => {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;
        const action = btn.dataset.action;
        hideMenu();
        if (action === 'import-image') uploadInput?.click();
        if (action === 'new-text') {
            const res = await postJson(noteUrl, { title:'Reference note', content:'# Note\nWrite whatever the commission needs here.' });
            if (res.item) window.location.reload();
        }
        if (action === 'new-group') {
            const res = await postJson(groupUrl, { title:'New group', x: menuPoint.x, y: menuPoint.y });
            if (res.item) window.location.reload();
        }
        if (action === 'add-connection' && menuTarget) {
            pendingConnectionFrom = menuTarget;
            tempPath?.remove();
            tempPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            tempPath.setAttribute('class', 'workspace-connection-line');
            tempPath.style.opacity = '0.45';
            svg.appendChild(tempPath);
        }
    });

    resetCamera();
    addNoteBtn?.addEventListener('click', async () => { const res = await postJson(noteUrl, { title:'Reference note', content:'# Note\nWrite whatever the commission needs here.' }); if (res.item) window.location.reload(); });
    addGroupBtn?.addEventListener('click', async () => { const res = await postJson(groupUrl, { title:'New group', x: 120, y: 120 }); if (res.item) window.location.reload(); });
});
</script>
@endpush
