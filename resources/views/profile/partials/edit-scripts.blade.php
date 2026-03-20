<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Drawer Logic (Only for Owner)
        const drawer = document.getElementById('edit-drawer');
        const openBtn = document.getElementById('open-drawer-btn');
        const closeBtn = document.getElementById('toggle-drawer');
        const drawerWidth = drawer ? `${drawer.offsetWidth || 350}px` : '350px';

        function openDrawer() {
            if (!drawer || !openBtn) return;
            drawer.style.right = '0';
            openBtn.style.display = 'none';
        }

        function closeDrawer() {
            if (!drawer || !openBtn) return;
            drawer.style.right = `-${drawerWidth}`;
            setTimeout(() => { openBtn.style.display = 'flex'; }, 400);
        }

        if (drawer && openBtn && closeBtn) {
            openBtn.addEventListener('click', openDrawer);
            closeBtn.addEventListener('click', closeDrawer);
        }

        document.querySelectorAll('.theme-swatch-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const theme = btn.dataset.theme || 'default';
                localStorage.setItem('atelier_theme', theme);
                if (theme === 'default') document.documentElement.removeAttribute('data-theme');
                else document.documentElement.setAttribute('data-theme', theme);

                const topSelector = document.getElementById('theme-selector');
                if (topSelector) topSelector.value = theme;
            });
        });

        function escapeHtml(value = '') {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function prettifyModuleName(type = 'module') {
            return type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        }

        function defaultSettingsForType(type) {
            const defaults = {
                banner: { color: 'var(--bg-panel)' },
                avatar_info: { show_follow: true },
                bio: { text: 'Welcome to my atelier.' },
                text_block: { text: 'New custom text block.' },
                gallery_feed: { layout: 'grid' },
                comm_slots: { slots_open: 3, next_open_date: '', base_types: [], addons: [] },
                kanban_tracker: { title: 'Commission Tracker' },
                links: { links: { Twitter: '#', Bluesky: '#' } },
                tip_jar: { message: 'Drop a coffee in the jar to support my work.' },
            };

            return defaults[type] ? JSON.parse(JSON.stringify(defaults[type])) : {};
        }

        function createPendingModuleCard(type) {
            const wrapper = document.createElement('div');
            wrapper.className = 'module-wrapper draggable pending-module';
            wrapper.dataset.id = `new-${type}`;
            wrapper.dataset.type = type;
            wrapper.dataset.settings = JSON.stringify(defaultSettingsForType(type));
            wrapper.innerHTML = `
                <div class="module-edit-overlay module-edit-overlay-visible">
                    <div class="mod-btn drag-handle" title="Drag">↕</div>
                    <div class="mod-btn mod-btn-edit" title="Edit ${escapeHtml(prettifyModuleName(type))}">✏</div>
                    <div class="mod-btn mod-btn-delete" title="Delete" style="color: #ff4d4d;">🗑</div>
                </div>
                <div class="pending-module-card">
                    <div class="pending-module-label mono">New ${escapeHtml(prettifyModuleName(type))}</div>
                    <div class="pending-module-hint mono">Drag to place • Edit to customize • Save when ready</div>
                </div>
            `;
            return wrapper;
        }

        // Drag and Drop Logic (Only for Owner)
        const zones = document.querySelectorAll('.sortable-zone');
        if (typeof Sortable !== 'undefined') {
            zones.forEach(zone => {
                if (zone.id === 'toolbox-zone') return;
                new Sortable(zone, {
                    group: 'shared',
                    animation: 150,
                    handle: '.drag-handle',
                    ghostClass: 'ghost-card',
                    dragClass: 'dragging-card',
                    onAdd: (evt) => {
                        const item = evt.item;
                        if (!item) return;

                        if (item.classList.contains('toolbox-item')) {
                            const type = item.dataset.type || (item.dataset.id || '').replace('new-', '');
                            const pendingCard = createPendingModuleCard(type);
                            item.replaceWith(pendingCard);
                        }
                    }
                });
            });
        }

        // Toolbox Logic (Only for Owner)
        const toolbox = document.getElementById('toolbox-zone');
        if (toolbox && typeof Sortable !== 'undefined') {
            new Sortable(toolbox, {
                group: { name: 'shared', pull: 'clone', put: false },
                animation: 150,
                sort: false,
                draggable: '.toolbox-item',
                ghostClass: 'ghost-card'
            });
        }

        function collectLayoutPayload() {
            const payload = {
                zones: {},
                page_layout: document.getElementById('layout-selector')?.value || 'classic'
            };

            ['header', 'main', 'sidebar'].forEach(zoneName => {
                const zoneEl = document.getElementById(zoneName + '-zone');
                if (zoneEl) {
                    const moduleIds = Array.from(zoneEl.children).map(child => {
                        const id = child.getAttribute('data-id');
                        const type = child.getAttribute('data-type');
                        const settings = child.dataset.settings ? JSON.parse(child.dataset.settings) : defaultSettingsForType(type || (id || '').replace('new-', ''));
                        if (id && id.startsWith('new-')) return { type: type || id.replace('new-', ''), is_new: true, settings };
                        return id ? id.toString() : null;
                    }).filter(id => id);
                    payload.zones[zoneName] = moduleIds;
                }
            });

            return payload;
        }

        async function postJson(url, payload, method = 'POST') {
            const response = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(result.message || 'Request failed.');
            }
            return result;
        }

        function renderSavedConfigRow(config) {
            return `
                <div class="saved-config-row" data-config-id="${config.id}" style="display: flex; gap: 0.45rem; align-items: stretch; background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 12px; padding: 0.75rem;">
                    <button type="button" class="load-config-btn" data-config-id="${config.id}" style="flex: 1; text-align: left; background: transparent; border: none; color: var(--text-main); cursor: pointer;">
                        <div class="mono" style="font-size: 0.78rem; text-transform: uppercase; margin-bottom: 0.2rem;">${escapeHtml(config.name)}</div>
                        <div class="mono" style="font-size: 0.66rem; color: var(--text-muted);">Saved config</div>
                    </button>
                    <button type="button" class="delete-config-btn" data-config-id="${config.id}" style="background: transparent; border: 1px solid #ff4d4d; color: #ff6b6b; border-radius: 10px; padding: 0 0.8rem; cursor: pointer;">✕</button>
                </div>
            `;
        }

        function ensureEmptyConfigState() {
            const list = document.getElementById('saved-configs-list');
            if (!list) return;
            const hasRows = list.querySelector('.saved-config-row');
            let emptyState = document.getElementById('empty-config-state');
            if (!hasRows && !emptyState) {
                emptyState = document.createElement('div');
                emptyState.id = 'empty-config-state';
                emptyState.className = 'mono';
                emptyState.style.cssText = 'font-size: 0.72rem; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: 12px; padding: 0.85rem;';
                emptyState.textContent = 'No saved configs yet. Save one before you start getting reckless.';
                list.appendChild(emptyState);
            }
            if (hasRows && emptyState) emptyState.remove();
        }

        async function loadConfig(payload, confirmText = 'Load this config? Your current unsaved page changes will be replaced.') {
            if (!confirm(confirmText)) return;
            try {
                await postJson('{{ route("artist.profile.configs.load") }}', payload);
                window.location.reload();
            } catch (error) {
                alert(error.message || 'Failed to load config.');
            }
        }

        // Save Layout Logic (Only for Owner)
        const saveLayoutBtn = document.getElementById('save-layout-btn');
        if (saveLayoutBtn) {
            saveLayoutBtn.addEventListener('click', async (e) => {
                const btn = e.target;
                const originalText = btn.innerText;
                btn.innerText = 'Saving...';
                btn.disabled = true;

                try {
                    await postJson('{{ route("artist.profile.save") }}', collectLayoutPayload());
                    btn.innerText = 'Saved!';
                    setTimeout(() => window.location.reload(), 350);
                } catch (err) {
                    btn.innerText = 'Error';
                    alert('Save failed: ' + (err.message || 'Unknown error'));
                    btn.disabled = false;
                    btn.innerText = originalText;
                }
            });
        }

        const saveConfigBtn = document.getElementById('save-config-btn');
        const configNameInput = document.getElementById('config-name-input');
        if (saveConfigBtn && configNameInput) {
            saveConfigBtn.addEventListener('click', async () => {
                const name = configNameInput.value.trim();
                if (!name) {
                    alert('Give this config a name first.');
                    configNameInput.focus();
                    return;
                }

                try {
                    await postJson('{{ route("artist.profile.save") }}', collectLayoutPayload());
                    const result = await postJson('{{ route("artist.profile.configs.save") }}', { name });
                    const list = document.getElementById('saved-configs-list');
                    if (list && result.config) {
                        list.insertAdjacentHTML('afterbegin', renderSavedConfigRow(result.config));
                        configNameInput.value = '';
                        ensureEmptyConfigState();
                    }
                } catch (error) {
                    alert(error.message || 'Failed to save config.');
                }
            });
        }

        document.querySelectorAll('.default-config-btn').forEach(btn => {
            btn.addEventListener('click', () => loadConfig(
                { default_key: btn.dataset.defaultKey },
                'Load this default config? It will replace your current page layout and modules.'
            ));
        });

        document.addEventListener('click', async (e) => {
            const loadBtn = e.target.closest('.load-config-btn');
            const deleteBtn = e.target.closest('.delete-config-btn');

            if (loadBtn) {
                await loadConfig({ config_id: loadBtn.dataset.configId });
                return;
            }

            if (deleteBtn) {
                if (!confirm('Delete this saved config?')) return;
                try {
                    await fetch(`/profile/configs/${deleteBtn.dataset.configId}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });
                    deleteBtn.closest('.saved-config-row')?.remove();
                    ensureEmptyConfigState();
                } catch (error) {
                    alert('Failed to delete saved config.');
                }
            }
        });

        ensureEmptyConfigState();

        // MODAL MANAGEMENT
        let currentEditingId = null;
        let currentWrapper = null;
        let currentModuleType = null;

        const modal = document.getElementById('edit-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalBody = document.getElementById('modal-body');
        const defaultEditor = document.getElementById('editor-default');
        const defaultSettingsJson = document.getElementById('default-settings-json');
        const markdownEditor = document.getElementById('editor-markdown');
        const commSlotsEditor = document.getElementById('editor-comm_slots');
        const linksEditor = document.getElementById('editor-links');
        const tipJarEditor = document.getElementById('editor-tip_jar');
        const kanbanEditor = document.getElementById('editor-kanban_tracker');
        const galleryEditor = document.getElementById('editor-gallery_feed');
        const avatarEditor = document.getElementById('editor-avatar_info');
        const bannerEditor = document.getElementById('editor-banner');
        
        const textarea = document.getElementById('modal-textarea');
        const preview = document.getElementById('modal-preview');
        const modalCloseBtn = document.getElementById('close-modal');
        const cancelBtn = document.getElementById('cancel-edit');
        const saveBtn = document.getElementById('save-edit');
        const contextMenu = document.getElementById('markdown-context-menu');

        const baseTypesBuilder = document.getElementById('base-types-builder');
        const addonsBuilder = document.getElementById('addons-builder');
        const linksContainer = document.getElementById('links-container');

        // Markdown Engine
        function updatePreview() {
            if (!textarea || !preview) return;
            let html = textarea.value
                .replace(/^# (.+)/gm, '<h1 class="serif" style="font-size: 2.2rem; color: var(--accent-color); margin-bottom: 1rem;">$1</h1>')
                .replace(/^## (.+)/gm, '<h2 class="serif" style="font-size: 1.8rem; color: var(--accent-color); margin-bottom: 0.8rem;">$1</h2>')
                .replace(/^### (.+)/gm, '<h3 class="serif" style="font-size: 1.4rem; color: var(--accent-color); margin-bottom: 0.6rem;">$1</h3>')
                .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.+?)\*/g, '<em>$1</em>')
                .replace(/!\[(.*?)\]\((.+?)\)/g, '<img src="$2" alt="$1" style="max-width: 100%; border-radius: 8px; margin: 1rem 0; border: 1px solid var(--border-color);">')
                .replace(/\[(.+?)\]\((.+?)\)/g, '<a href="$2" target="_blank" style="color: var(--accent-color); text-decoration: underline;">$1</a>')
                .replace(/^> (.+)/gm, '<blockquote style="border-left: 4px solid var(--accent-color); padding: 0.5rem 1rem; color: var(--text-muted); background: var(--bg-panel); border-radius: 0 4px 4px 0; margin: 1rem 0;">$1</blockquote>')
                .replace(/`(.+?)`/g, '<code style="background: var(--bg-panel); padding: 0.2rem 0.4rem; border-radius: 4px; color: var(--accent-color); font-family: monospace;">$1</code>')
                .replace(/^- (.+)/gm, '<li style="margin-left: 1.5rem; color: var(--text-main);">$1</li>')
                .replace(/\n/g, '<br>');
            preview.innerHTML = html || '<span style="color: var(--text-muted); font-style: italic;">Preview will appear here...</span>';
        }

        if (textarea) {
            textarea.addEventListener('input', updatePreview);
            textarea.addEventListener('contextmenu', (e) => { 
                e.preventDefault(); 
                if(contextMenu) { 
                    contextMenu.style.top = `${e.clientY}px`; 
                    contextMenu.style.left = `${e.clientX}px`; 
                    contextMenu.style.display = 'block'; 
                } 
            });
        }
        
        function insertMarkdown(type) {
            if (!textarea) return;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const selection = text.substring(start, end);
            let inserted = "";
            let focusOffset = 0;
            switch(type) {
                case 'h1': inserted = `# ${selection || "Heading 1"}`; focusOffset = 2; break;
                case 'h2': inserted = `## ${selection || "Heading 2"}`; focusOffset = 3; break;
                case 'h3': inserted = `### ${selection || "Heading 3"}`; focusOffset = 4; break;
                case 'bold': inserted = `**${selection || "bold text"}**`; focusOffset = 2; break;
                case 'italic': inserted = `*${selection || "italic text"}*`; focusOffset = 1; break;
                case 'link': inserted = `[${selection || "link text"}](https://)`; focusOffset = (selection ? selection.length + 3 : 1); break;
                case 'image': inserted = `![${selection || "Alt text"}](https://)`; focusOffset = (selection ? selection.length + 4 : 2); break;
                case 'list': inserted = `\n- ${selection || "list item"}`; focusOffset = 3; break;
                case 'quote': inserted = `\n> ${selection || "quoted text"}`; focusOffset = 3; break;
                case 'code': inserted = `\n\`\`\`\n${selection || "code here"}\n\`\`\`\n`; focusOffset = 5; break;
            }
            textarea.value = text.substring(0, start) + inserted + text.substring(end);
            textarea.focus();
            const nStart = start + focusOffset;
            const nEnd = nStart + (selection.length || (inserted.length - (focusOffset * 2)));
            textarea.setSelectionRange(nStart, nEnd);
            updatePreview();
        }

        document.querySelectorAll('.md-btn').forEach(btn => btn.addEventListener('click', (e) => { e.preventDefault(); insertMarkdown(btn.dataset.md); }));
        document.addEventListener('click', (e) => { if (contextMenu && !e.target.closest('#markdown-context-menu')) contextMenu.style.display = 'none'; });
        document.querySelectorAll('.ctx-item').forEach(item => item.addEventListener('click', () => { insertMarkdown(item.dataset.md); contextMenu.style.display = 'none'; }));

        // BUILDER LOGIC (Pricing / Links)
        function createBuilderRow(container, name = '', price = '', type = 'flat') {
            const row = document.createElement('div');
            row.classList.add('builder-row');
            row.innerHTML = `<input type="text" placeholder="Name" class="name-input" value="${name}" style="flex: 3;">
                <input type="text" placeholder="Value" class="price-input" value="${price}" style="flex: 1;">
                <select class="type-select" style="flex: 1;">
                    <option value="flat" ${type==='flat'?'selected':''}>$</option>
                    <option value="percent" ${type==='percent'?'selected':''}>%</option>
                    <option value="mult" ${type==='mult'?'selected':''}>x</option>
                </select>
                <button class="remove-row">✕</button>`;
            row.querySelector('.remove-row').addEventListener('click', () => row.remove());
            container.appendChild(row);
        }

        function createLinkRow(name = '', url = '') {
            const row = document.createElement('div');
            row.classList.add('builder-row');
            row.innerHTML = `<input type="text" placeholder="Site Name" class="link-name-input" value="${name}" style="flex: 1;">
                <input type="text" placeholder="URL" class="link-url-input" value="${url}" style="flex: 2;">
                <button class="remove-row" style="border-color: #ff4d4d; color: #ff4d4d;">✕</button>`;
            row.querySelector('.remove-row').addEventListener('click', () => row.remove());
            linksContainer.appendChild(row);
        }

        if(baseTypesBuilder) {
            const addBaseBtn = document.getElementById('add-base-type');
            if(addBaseBtn) addBaseBtn.addEventListener('click', () => createBuilderRow(baseTypesBuilder));
        }
        if(addonsBuilder) {
            const addAddonBtn = document.getElementById('add-addon');
            if(addAddonBtn) addAddonBtn.addEventListener('click', () => createBuilderRow(addonsBuilder));
        }
        if(linksContainer) {
            const addLinkBtn = document.getElementById('add-link-row');
            if(addLinkBtn) addLinkBtn.addEventListener('click', () => createLinkRow());
        }

        function closeModal() { if(modal) modal.style.display = 'none'; currentEditingId = null; currentWrapper = null; currentModuleType = null; }
        if(modalCloseBtn) modalCloseBtn.addEventListener('click', closeModal);
        if(cancelBtn) cancelBtn.addEventListener('click', closeModal);

        function collectCurrentSettings() {
            let settings = {};
            if (currentModuleType === 'bio' || currentModuleType === 'text_block') {
                settings = { text: textarea.value.trim() };
            }
            else if (currentModuleType === 'tip_jar') {
                settings = {
                    message: document.getElementById('tip-jar-message-input')?.value?.trim() || 'Drop a coffee in the jar to support my work.',
                    emoji: document.getElementById('tip-jar-emoji-input')?.value?.trim() || '☕'
                };
            }
            else if (currentModuleType === 'kanban_tracker') {
                settings = {
                    title: document.getElementById('kanban-title-input')?.value?.trim() || 'Commission Tracker'
                };
            }
            else if (currentModuleType === 'gallery_feed') {
                const currentSettings = JSON.parse(currentWrapper?.dataset?.settings || '{}');
                settings = {
                    layout: document.getElementById('gallery-layout-input')?.value || 'grid',
                    images: currentSettings.images || []
                };
            }
            else if (currentModuleType === 'avatar_info') {
                settings = {
                    show_follow: !!document.getElementById('avatar-show-follow-input')?.checked
                };
            }
            else if (currentModuleType === 'banner') {
                settings = {
                    color: document.getElementById('banner-color-input')?.value?.trim() || 'var(--bg-panel)',
                    image: document.getElementById('banner-image-input')?.value?.trim() || ''
                };
            }
            else if (currentModuleType === 'comm_slots') {
                const base_types = [];
                baseTypesBuilder.querySelectorAll('.builder-row').forEach(row => {
                    const nameInput = row.querySelector('.name-input');
                    const priceInput = row.querySelector('.price-input');
                    const typeSelect = row.querySelector('.type-select');
                    if (nameInput && nameInput.value.trim()) {
                        base_types.push({
                            name: nameInput.value.trim(),
                            price: priceInput ? priceInput.value.trim() : '',
                            type: typeSelect ? typeSelect.value : 'flat'
                        });
                    }
                });
                const addons = [];
                addonsBuilder.querySelectorAll('.builder-row').forEach(row => {
                    const nameInput = row.querySelector('.name-input');
                    const priceInput = row.querySelector('.price-input');
                    const typeSelect = row.querySelector('.type-select');
                    if (nameInput && nameInput.value.trim()) {
                        addons.push({
                            name: nameInput.value.trim(),
                            price: priceInput ? priceInput.value.trim() : '',
                            type: typeSelect ? typeSelect.value : 'flat'
                        });
                    }
                });
                settings = {
                    slots_open: document.getElementById('slots-open-input')?.value || 0,
                    next_open_date: document.getElementById('next-open-date-input')?.value || '',
                    base_types,
                    addons
                };
            }
            else if (currentModuleType === 'links') {
                const links = {};
                linksContainer.querySelectorAll('.builder-row').forEach(row => {
                    const nameInput = row.querySelector('.link-name-input');
                    const urlInput = row.querySelector('.link-url-input');
                    if (nameInput && nameInput.value.trim() && urlInput && urlInput.value.trim()) {
                        links[nameInput.value.trim()] = urlInput.value.trim();
                    }
                });
                settings = { links };
            }
            else {
                try {
                    settings = JSON.parse(defaultSettingsJson?.value || '{}');
                } catch(e) {
                    throw new Error('Invalid JSON settings.');
                }
            }
            return settings;
        }

        function renderMarkdownHtml(text = '') {
            return text
                .replace(/^# (.+)/gm, '<h1 class="serif" style="font-size: 2.2rem; color: var(--accent-color); margin-bottom: 1rem;">$1</h1>')
                .replace(/^## (.+)/gm, '<h2 class="serif" style="font-size: 1.8rem; color: var(--accent-color); margin-bottom: 0.8rem;">$1</h2>')
                .replace(/^### (.+)/gm, '<h3 class="serif" style="font-size: 1.4rem; color: var(--accent-color); margin-bottom: 0.6rem;">$1</h3>')
                .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.+?)\*/g, '<em>$1</em>')
                .replace(/!\[(.*?)\]\((.+?)\)/g, '<img src="$2" alt="$1" style="max-width: 100%; border-radius: 8px; margin: 1rem 0; border: 1px solid var(--border-color);">')
                .replace(/\[(.+?)\]\((.+?)\)/g, '<a href="$2" target="_blank" style="color: var(--accent-color); text-decoration: underline;">$1</a>')
                .replace(/^> (.+)/gm, '<blockquote style="border-left: 4px solid var(--accent-color); padding: 0.5rem 1rem; color: var(--text-muted); background: var(--bg-panel); border-radius: 0 4px 4px 0; margin: 1rem 0;">$1</blockquote>')
                .replace(/`(.+?)`/g, '<code style="background: var(--bg-panel); padding: 0.2rem 0.4rem; border-radius: 4px; color: var(--accent-color); font-family: monospace;">$1</code>')
                .replace(/^- (.+)/gm, '<li style="margin-left: 1.5rem; color: var(--text-main);">$1</li>')
                .replace(/\n/g, '<br>');
        }

        function applySettingsToModule(wrapper, type, settings) {
            if (!wrapper) return;
            wrapper.dataset.settings = JSON.stringify(settings || {});

            if (type === 'bio' || type === 'text_block') {
                const content = wrapper.querySelector('.module-content');
                if (content) content.innerHTML = renderMarkdownHtml(settings.text || '');
            }
            else if (type === 'tip_jar') {
                const message = wrapper.querySelector('[data-role="tip-jar-message"]');
                const emoji = wrapper.querySelector('[data-role="tip-jar-emoji"]');
                if (message) message.textContent = settings.message || 'Drop a coffee in the jar to support my work.';
                if (emoji) emoji.textContent = settings.emoji || '☕';
            }
            else if (type === 'kanban_tracker') {
                const title = wrapper.querySelector('[data-role="kanban-title"]');
                if (title) title.textContent = settings.title || 'Commission Tracker';
            }
            else if (type === 'gallery_feed') {
                const pill = wrapper.querySelector('[data-role="gallery-layout-pill"]');
                const previewEl = wrapper.querySelector('[data-role="gallery-preview"]');
                if (pill) pill.textContent = settings.layout || 'grid';
                if (previewEl) previewEl.className = `gallery-feed-preview gallery-feed-preview--${settings.layout || 'grid'}`;
            }
            else if (type === 'links') {
                const list = wrapper.querySelector('[data-role="links-list"]');
                if (list) {
                    const links = settings.links || {};
                    list.innerHTML = Object.entries(links).map(([name, url]) => `
                        <a href="${url}" target="_blank" class="profile-link-card"
                           style="padding: 0.9rem 1rem; text-decoration: none; border: 1px solid var(--border-color); border-radius: 10px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: space-between; gap: 1rem; background: color-mix(in srgb, var(--bg-color) 85%, transparent);">
                            <span style="display: inline-flex; align-items: center; gap: 0.65rem; min-width: 0;">
                                <span style="color: var(--accent-color); font-size: 0.6rem;">◆</span>
                                <span class="mono" style="font-size: 0.8rem; color: var(--text-main); text-transform: uppercase;">${escapeHtml(name)}</span>
                            </span>
                            <span class="mono" style="font-size: 0.68rem; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 50%;">${escapeHtml(String(url).replace(/^https?:\/\//, ''))}</span>
                        </a>
                    `).join('');
                }
            }
            else if (type === 'comm_slots') {
                const slots = parseInt(settings.slots_open || 0, 10);
                const label = wrapper.querySelector('[data-role="comm-status-label"]');
                const nextWrap = wrapper.querySelector('[data-role="comm-next-open"]');
                const nextText = wrapper.querySelector('[data-role="comm-next-open-text"]');
                if (label) label.textContent = slots > 0 ? `OPEN FOR ${slots} SLOTS` : 'COMMISSIONS CLOSED';
                if (nextWrap) nextWrap.style.display = settings.next_open_date ? 'flex' : 'none';
                if (nextText) nextText.textContent = settings.next_open_date || '';
            }
            else if (type === 'avatar_info') {
                const cta = wrapper.querySelector('[data-role="follow-cta-wrap"]');
                if (cta) cta.style.display = settings.show_follow ? '' : 'none';
            }
            else if (type === 'banner') {
                const bannerBox = wrapper.querySelector('.module-banner-box');
                const bannerImg = wrapper.querySelector('.module-banner-image');
                const placeholder = wrapper.querySelector('.module-banner-placeholder');
                if (bannerBox) bannerBox.style.background = settings.color || 'var(--bg-panel)';
                if (bannerImg) {
                    if (settings.image) {
                        bannerImg.src = settings.image;
                        bannerImg.style.display = 'block';
                    } else {
                        bannerImg.removeAttribute('src');
                        bannerImg.style.display = 'none';
                    }
                }
                if (placeholder) placeholder.style.display = settings.image ? 'none' : '';
            }
        }

        if(saveBtn) {
            saveBtn.addEventListener('click', async () => {
                if (!currentEditingId) return;

                let settings = {};
                try {
                    settings = collectCurrentSettings();
                } catch (error) {
                    alert(error.message || 'Invalid settings.');
                    return;
                }

                if (currentEditingId.startsWith('new-')) {
                    if (currentWrapper) applySettingsToModule(currentWrapper, currentModuleType, settings);
                    closeModal();
                    return;
                }

                try {
                    const res = await fetch(`/profile/module/${currentEditingId}/settings`, {
                        method: 'PATCH', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ settings })
                    });
                    if (res.ok) {
                        if (currentWrapper) applySettingsToModule(currentWrapper, currentModuleType, settings);
                        closeModal();
                    }
                    else { const errorData = await res.json(); alert("Failed to save: " + (errorData.message || "Unknown error")); }
                } catch (err) { alert("Failed to save due to network or server error."); }
            });
        }

        // Event Delegation for Edit/Delete Buttons
        document.addEventListener('click', async (e) => {
            const deleteBtn = e.target.closest('.mod-btn-delete');
            const editBtn = e.target.closest('.mod-btn-edit');
            const openPricingBtn = e.target.closest('.open-pricing-sheet');

            if (deleteBtn) {
                if(!confirm('Delete module?')) return;
                const wrapper = deleteBtn.closest('.module-wrapper');
                const id = wrapper.dataset.id;
                if (id.startsWith('new-')) { wrapper.remove(); return; }
                fetch(`/profile/module/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(() => wrapper.remove());
            }

            if (editBtn) {
                currentWrapper = editBtn.closest('.module-wrapper');
                currentEditingId = currentWrapper.dataset.id;
                currentModuleType = currentWrapper.dataset.type || 'text_block';

                if(markdownEditor) markdownEditor.style.display = 'none'; 
                if(commSlotsEditor) commSlotsEditor.style.display = 'none'; 
                if(linksEditor) linksEditor.style.display = 'none'; 
                if(tipJarEditor) tipJarEditor.style.display = 'none';
                if(kanbanEditor) kanbanEditor.style.display = 'none';
                if(galleryEditor) galleryEditor.style.display = 'none';
                if(avatarEditor) avatarEditor.style.display = 'none';
                if(bannerEditor) bannerEditor.style.display = 'none';
                if(defaultEditor) defaultEditor.style.display = 'none';
                
                const settings = currentWrapper.dataset.settings ? JSON.parse(currentWrapper.dataset.settings) : defaultSettingsForType(currentModuleType);
                
                if (currentModuleType === 'bio' || currentModuleType === 'text_block') {
                    if(modalTitle) modalTitle.innerText = "Edit Text Module"; 
                    if(markdownEditor) markdownEditor.style.display = 'flex'; 
                    if(textarea) textarea.value = settings.text || ''; 
                    updatePreview();
                } else if (currentModuleType === 'comm_slots') {
                    if(modalTitle) modalTitle.innerText = "Edit Commission Slots & Pricing"; 
                    if(commSlotsEditor) commSlotsEditor.style.display = 'flex';
                    const slotsInput = document.getElementById('slots-open-input');
                    const dateInput = document.getElementById('next-open-date-input');
                    if(slotsInput) slotsInput.value = settings.slots_open || 0;
                    if(dateInput) dateInput.value = settings.next_open_date || '';
                    if(baseTypesBuilder) {
                        baseTypesBuilder.innerHTML = ''; 
                        (settings.base_types || []).forEach(bt => createBuilderRow(baseTypesBuilder, bt.name, bt.price, bt.type));
                    }
                    if(addonsBuilder) {
                        addonsBuilder.innerHTML = ''; 
                        (settings.addons || []).forEach(ao => createBuilderRow(addonsBuilder, ao.name, ao.price, ao.type));
                    }
                } else if (currentModuleType === 'links') {
                    if(modalTitle) modalTitle.innerText = "Edit Social Links"; 
                    if(linksEditor) linksEditor.style.display = 'flex'; 
                    if(linksContainer) {
                        linksContainer.innerHTML = '';
                        for (const [name, url] of Object.entries(settings.links || {})) { createLinkRow(name, url); }
                    }
                } else if (currentModuleType === 'tip_jar') {
                    if(modalTitle) modalTitle.innerText = 'Edit Tip Jar';
                    if(tipJarEditor) tipJarEditor.style.display = 'flex';
                    const msgInput = document.getElementById('tip-jar-message-input');
                    const emojiInput = document.getElementById('tip-jar-emoji-input');
                    if (msgInput) msgInput.value = settings.message || 'Drop a coffee in the jar to support my work.';
                    if (emojiInput) emojiInput.value = settings.emoji || '☕';
                } else if (currentModuleType === 'kanban_tracker') {
                    if(modalTitle) modalTitle.innerText = 'Edit Tracker';
                    if(kanbanEditor) kanbanEditor.style.display = 'flex';
                    const titleInput = document.getElementById('kanban-title-input');
                    if (titleInput) titleInput.value = settings.title || 'Commission Tracker';
                } else if (currentModuleType === 'gallery_feed') {
                    if(modalTitle) modalTitle.innerText = 'Edit Gallery';
                    if(galleryEditor) galleryEditor.style.display = 'flex';
                    const galleryInput = document.getElementById('gallery-layout-input');
                    if (galleryInput) galleryInput.value = settings.layout || 'grid';
                } else if (currentModuleType === 'avatar_info') {
                    if(modalTitle) modalTitle.innerText = 'Edit Profile Identity';
                    if(avatarEditor) avatarEditor.style.display = 'flex';
                    const showFollow = document.getElementById('avatar-show-follow-input');
                    if (showFollow) showFollow.checked = settings.show_follow ?? true;
                } else if (currentModuleType === 'banner') {
                    if(modalTitle) modalTitle.innerText = 'Edit Banner';
                    if(bannerEditor) bannerEditor.style.display = 'flex';
                    const colorInput = document.getElementById('banner-color-input');
                    const imageInput = document.getElementById('banner-image-input');
                    if (colorInput) colorInput.value = settings.color || 'var(--bg-panel)';
                    if (imageInput) imageInput.value = settings.image || '';
                } else {
                    if(modalTitle) modalTitle.innerText = `Edit ${currentModuleType.replace('_', ' ')} Module`;
                    if(defaultEditor) defaultEditor.style.display = 'flex';
                    if(defaultSettingsJson) defaultSettingsJson.value = JSON.stringify(settings, null, 2);
                }
                if(modal) modal.style.display = 'flex';
            }

            // PRICING CALCULATOR LOGIC
            if (openPricingBtn) {
                console.log('Opening pricing sheet...');
                const wrapper = openPricingBtn.closest('.module-wrapper');
                const settings = JSON.parse(wrapper.dataset.settings || '{}');
                const calcModal = document.getElementById('pricing-sheet-modal');
                const form = document.getElementById('pricing-options-form');
                
                if(!calcModal || !form) {
                    console.error('Pricing modal or form not found');
                    return;
                }
                
                form.innerHTML = '';

                // Add Base Types
                if (settings.base_types?.length) {
                    const cat = document.createElement('div');
                    cat.innerHTML = '<span class="pricing-category-label">Base Type</span>';
                    const group = document.createElement('div');
                    group.style.display = 'flex'; group.style.flexDirection = 'column'; group.style.gap = '0.5rem';
                    settings.base_types.forEach((bt, idx) => {
                        const card = document.createElement('div');
                        card.classList.add('pricing-option-card');
                        if (idx === 0) card.classList.add('active');
                        card.dataset.type = 'base'; card.dataset.name = bt.name; card.dataset.value = bt.price; card.dataset.calc = bt.type || 'flat';
                        const suffix = card.dataset.calc === 'percent' ? '%' : (card.dataset.calc === 'mult' ? 'x' : '');
                        const prefix = card.dataset.calc === 'flat' ? '$' : '';
                        card.innerHTML = `<div class="pricing-option-info"><span class="pricing-option-name">${bt.name}</span></div>
                            <span class="pricing-option-price">${prefix}${bt.price}${suffix}</span>`;
                        card.onclick = () => { group.querySelectorAll('.pricing-option-card').forEach(c => c.classList.remove('active')); card.classList.add('active'); calculateTotal(); };
                        group.appendChild(card);
                    });
                    cat.appendChild(group);
                    form.appendChild(cat);
                }

                // Add Add-ons
                if (settings.addons?.length) {
                    const cat = document.createElement('div');
                    cat.innerHTML = '<span class="pricing-category-label">Add-ons & Modifiers</span>';
                    const group = document.createElement('div');
                    group.style.display = 'flex'; group.style.flexDirection = 'column'; group.style.gap = '0.5rem';
                    settings.addons.forEach(ao => {
                        const card = document.createElement('div');
                        card.classList.add('pricing-option-card');
                        card.dataset.type = 'addon'; card.dataset.name = ao.name; card.dataset.value = ao.price; card.dataset.calc = ao.type || 'flat';
                        const suffix = card.dataset.calc === 'percent' ? '%' : (card.dataset.calc === 'mult' ? 'x' : '');
                        const prefix = card.dataset.calc === 'flat' ? '$' : '';
                        card.innerHTML = `<div class="pricing-option-info"><span class="pricing-option-name">${ao.name}</span></div>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <span class="pricing-option-price">${prefix}${ao.price}${suffix}</span>
                                <input type="number" class="pricing-qty-input" value="0" min="0" onclick="event.stopPropagation()">
                            </div>`;
                        card.onclick = () => { card.classList.toggle('active'); if(card.classList.contains('active')) card.querySelector('input').value = 1; else card.querySelector('input').value = 0; calculateTotal(); };
                        card.querySelector('input').oninput = () => { if(parseInt(card.querySelector('input').value) > 0) card.classList.add('active'); else card.classList.remove('active'); calculateTotal(); };
                        group.appendChild(card);
                    });
                    cat.appendChild(group);
                    form.appendChild(cat);
                }

                function calculateTotal() {
                    const quoteItemsEl = document.getElementById('quote-items');
                    const quoteTotalEl = document.getElementById('quote-total');
                    if(!quoteItemsEl || !quoteTotalEl) return;

                    let itemsHtml = '';
                    let subtotal = 0; // Base + Flat Addons
                    let multipliers = [];
                    let percentages = [];

                    // 1. Process Base
                    const activeBase = form.querySelector('.pricing-option-card[data-type="base"].active');
                    if (activeBase) {
                        const val = parseFloat(activeBase.dataset.value) || 0;
                        const type = activeBase.dataset.calc;
                        if (type === 'flat') {
                            subtotal += val;
                            itemsHtml += `<div style="display: flex; justify-content: space-between; font-size: 0.9rem;"><span>${activeBase.dataset.name}</span><span>$${val}</span></div>`;
                        } else if (type === 'mult') multipliers.push({ name: activeBase.dataset.name, val });
                        else if (type === 'percent') percentages.push({ name: activeBase.dataset.name, val });
                    }

                    // 2. Process Addons (Flat first)
                    form.querySelectorAll('.pricing-option-card[data-type="addon"].active').forEach(card => {
                        const qty = parseInt(card.querySelector('input').value) || 0;
                        const val = parseFloat(card.dataset.value) || 0;
                        const type = card.dataset.calc;
                        
                        if (type === 'flat') {
                            const sub = qty * val;
                            subtotal += sub;
                            itemsHtml += `<div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-muted);"><span>${card.dataset.name} (x${qty})</span><span>$${sub}</span></div>`;
                        } else if (type === 'mult') {
                            // Multipliers usually act on the current subtotal
                            for(let i=0; i<qty; i++) multipliers.push({ name: card.dataset.name, val });
                        } else if (type === 'percent') {
                            // Percentages act on the final subtotal
                            for(let i=0; i<qty; i++) percentages.push({ name: card.dataset.name, val });
                        }
                    });

                    let finalTotal = subtotal;

                    // 3. Apply Multipliers
                    multipliers.forEach(m => {
                        const prev = finalTotal;
                        finalTotal *= m.val;
                        itemsHtml += `<div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--accent-color);"><span>${m.name} (x${m.val})</span><span>+$${(finalTotal - prev).toFixed(2)}</span></div>`;
                    });

                    // 4. Apply Percentages (based on the cost before percentages)
                    const costToPercent = finalTotal;
                    percentages.forEach(p => {
                        const sub = (p.val / 100) * costToPercent;
                        finalTotal += sub;
                        itemsHtml += `<div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--accent-color);"><span>${p.name} (+${p.val}%)</span><span>+$${sub.toFixed(2)}</span></div>`;
                    });

                    document.getElementById('quote-items').innerHTML = itemsHtml;
                    document.getElementById('quote-total').innerText = `$${finalTotal.toFixed(2)}`;
                }

                if(calcModal) calcModal.style.display = 'flex';
                calculateTotal();
            }
        });

        const closePricingBtn = document.getElementById('close-pricing-modal');
        if(closePricingBtn) {
            closePricingBtn.onclick = () => {
                const modal = document.getElementById('pricing-sheet-modal');
                if(modal) modal.style.display = 'none';
            };
        }
    });
</script>

<style>
    .edit-zone { min-height: 50px; outline: 2px dashed rgba(255,255,255,0.05); border-radius: 12px; transition: outline 0.3s ease, background 0.3s ease; padding: 14px; margin: -14px; }
    .edit-zone:empty { outline: 2px dashed var(--accent-color); opacity: 0.65; background: rgba(0,0,0,0.2); }
    .module-wrapper { position: relative; border-radius: 12px; }
    .module-wrapper.draggable { cursor: grab; transition: outline 0.2s ease, box-shadow 0.2s ease; }
    .module-wrapper.draggable:hover { outline: 2px solid var(--accent-color); border-radius: 12px; }
    .ghost-card { opacity: 0.4; background: var(--bg-color) !important; outline: 2px dashed var(--accent-color) !important; }
    .dragging-card { opacity: 1; box-shadow: 0 10px 30px rgba(0,0,0,0.5); cursor: grabbing !important; }
    .module-edit-overlay { display: none; position: absolute; top: 10px; right: 10px; z-index: 50; gap: 0.5rem; }
    .module-wrapper.draggable:hover .module-edit-overlay,
    .module-edit-overlay.module-edit-overlay-visible { display: flex; }
    .mod-btn { background: color-mix(in srgb, var(--bg-panel) 92%, black); border: 1px solid var(--border-color); color: var(--text-main); width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 8px 20px rgba(0,0,0,0.25); }
    .mod-btn:hover { border-color: var(--accent-color); color: var(--accent-color); }
    .drag-handle { cursor: grab; }
    .pending-module-card { background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01)); border: 1px dashed var(--accent-color); border-radius: 12px; padding: 1.1rem 1rem; min-height: 92px; display: flex; flex-direction: column; justify-content: center; gap: 0.45rem; }
    .pending-module-label { color: var(--text-main); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .pending-module-hint { color: var(--text-muted); font-size: 0.72rem; line-height: 1.5; }
    .builder-row { display: flex; gap: 0.5rem; }
    .builder-row input { background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.5rem; border-radius: 4px; font-size: 0.75rem; outline: none; flex: 1; }
    .remove-row { background: none; border: 1px solid #ff4d4d; color: #ff4d4d; border-radius: 4px; cursor: pointer; padding: 0 0.5rem; }

    @media (max-width: 980px) {
        #open-drawer-btn { left: 1rem !important; right: 1rem !important; bottom: 1rem !important; width: auto; justify-content: center; }
        #edit-drawer { width: min(92vw, 360px) !important; }
    }
</style>
