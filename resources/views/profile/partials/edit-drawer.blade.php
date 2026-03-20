<!-- Edit Drawer (Toolbox) -->
<div id="edit-drawer" style="position: fixed; top: 0; right: -380px; width: 380px; height: 100vh; background: var(--bg-panel); border-left: 1px solid var(--border-color); z-index: 9999; display: flex; flex-direction: column; transition: right 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); box-shadow: -10px 0 30px rgba(0,0,0,0.5);">
    <div style="padding: 1.5rem 1.5rem 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
        <div>
            <div class="pill mono" style="margin: 0 0 0.75rem; border-color: var(--accent-color); color: var(--accent-color); background: var(--accent-dim);">
                <div class="dot" style="background: var(--accent-color);"></div>
                ● edit artist page
            </div>
            <div class="mono" style="color: var(--text-muted); font-size: 0.75rem; line-height: 1.6;">Build, rescue, save, and reload your page without losing your mind.</div>
        </div>
        <button id="toggle-drawer" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.5rem;">✕</button>
    </div>

    <div style="padding: 1.5rem; flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 1.5rem;">
        <section style="border: 1px solid var(--border-color); border-radius: 14px; padding: 1rem; background: color-mix(in srgb, var(--bg-color) 65%, transparent);">
            <h4 class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.75rem;">Quick rescue</h4>
            <div class="mono" style="color: var(--text-muted); font-size: 0.72rem; line-height: 1.6; margin-bottom: 1rem;">If the page gets cursed, load a built-in default and keep moving.</div>
            <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                @foreach(($defaultConfigs ?? []) as $config)
                    <button
                        type="button"
                        class="config-action-btn default-config-btn"
                        data-default-key="{{ $config['key'] }}"
                        style="text-align: left; width: 100%; background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 12px; padding: 0.9rem; cursor: pointer;"
                    >
                        <div class="mono" style="font-size: 0.78rem; text-transform: uppercase; color: var(--text-main); margin-bottom: 0.35rem;">{{ $config['name'] }}</div>
                        <div class="mono" style="font-size: 0.68rem; color: var(--text-muted); line-height: 1.5;">{{ $config['description'] }}</div>
                    </button>
                @endforeach
            </div>
        </section>

        <section style="border: 1px solid var(--border-color); border-radius: 14px; padding: 1rem; background: color-mix(in srgb, var(--bg-color) 65%, transparent);">
            <h4 class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.75rem;">Save / load configs</h4>
            <div class="mono" style="color: var(--text-muted); font-size: 0.72rem; line-height: 1.6; margin-bottom: 1rem;">Save the current state, then reload it later. Good for experiments, seasonal pages, and undoing bad ideas.</div>
            <div style="display: flex; gap: 0.6rem; margin-bottom: 0.85rem;">
                <input id="config-name-input" type="text" placeholder="Config name (ex: Summer Comms)" style="flex: 1; background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.8rem; border-radius: 10px; outline: none; font-size: 0.8rem;">
                <button id="save-config-btn" type="button" class="btn btn-secondary" style="white-space: nowrap;">Save Current</button>
            </div>
            <div id="saved-configs-list" style="display: flex; flex-direction: column; gap: 0.65rem;">
                @forelse(($savedConfigs ?? []) as $config)
                    <div class="saved-config-row" data-config-id="{{ $config->id }}" style="display: flex; gap: 0.45rem; align-items: stretch; background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 12px; padding: 0.75rem;">
                        <button type="button" class="load-config-btn" data-config-id="{{ $config->id }}" style="flex: 1; text-align: left; background: transparent; border: none; color: var(--text-main); cursor: pointer;">
                            <div class="mono" style="font-size: 0.78rem; text-transform: uppercase; margin-bottom: 0.2rem;">{{ $config->name }}</div>
                            <div class="mono" style="font-size: 0.66rem; color: var(--text-muted);">{{ ucfirst(str_replace('_', ' ', $config->page_layout)) }} layout</div>
                        </button>
                        <button type="button" class="delete-config-btn" data-config-id="{{ $config->id }}" style="background: transparent; border: 1px solid #ff4d4d; color: #ff6b6b; border-radius: 10px; padding: 0 0.8rem; cursor: pointer;">✕</button>
                    </div>
                @empty
                    <div id="empty-config-state" class="mono" style="font-size: 0.72rem; color: var(--text-muted); border: 1px dashed var(--border-color); border-radius: 12px; padding: 0.85rem;">No saved configs yet. Save one before you start getting reckless.</div>
                @endforelse
            </div>
        </section>

        <section style="border: 1px solid var(--border-color); border-radius: 14px; padding: 1rem; background: color-mix(in srgb, var(--bg-color) 65%, transparent);">
            <h4 class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1rem;">Page layout</h4>
            <select id="layout-selector" style="width: 100%; background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.8rem; border-radius: 10px; margin-bottom: 0.85rem; outline: none; font-size: 0.8rem;">
                <option value="classic" {{ ($artist->page_layout ?? 'classic') === 'classic' ? 'selected' : '' }}>Classic — content + sidebar</option>
                <option value="fixed_left" {{ ($artist->page_layout ?? 'classic') === 'fixed_left' ? 'selected' : '' }}>Sticky Left Rail</option>
                <option value="editorial" {{ ($artist->page_layout ?? 'classic') === 'editorial' ? 'selected' : '' }}>Editorial — wide reading layout</option>
                <option value="magazine" {{ ($artist->page_layout ?? 'classic') === 'magazine' ? 'selected' : '' }}>Magazine — roomy right column</option>
                <option value="stacked" {{ ($artist->page_layout ?? 'classic') === 'stacked' ? 'selected' : '' }}>Stacked — single column</option>
            </select>
            <p class="mono" style="color: var(--text-muted); font-size: 0.72rem; line-height: 1.6; margin-bottom: 0;">Drag modules between zones, click them to edit, then save layout when it looks right.</p>
        </section>

        <section style="border: 1px solid var(--border-color); border-radius: 14px; padding: 1rem; background: color-mix(in srgb, var(--bg-color) 65%, transparent);">
            <h4 class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1rem;">Theme preview</h4>
            <div id="theme-swatches" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.6rem;">
                <button type="button" class="theme-swatch-btn mono" data-theme="default">Default</button>
                <button type="button" class="theme-swatch-btn mono" data-theme="gay">Gay</button>
                <button type="button" class="theme-swatch-btn mono" data-theme="trans">Trans</button>
                <button type="button" class="theme-swatch-btn mono" data-theme="lesbian">Lesbian</button>
                <button type="button" class="theme-swatch-btn mono" data-theme="bi">Bi</button>
                <button type="button" class="theme-swatch-btn mono" data-theme="pan">Pan</button>
                <button type="button" class="theme-swatch-btn mono" data-theme="femboy">Femboy</button>
                <button type="button" class="theme-swatch-btn mono" data-theme="dominant">Dom</button>
                <button type="button" class="theme-swatch-btn mono" data-theme="musk">Musk</button>
            </div>
        </section>

        <section style="border: 1px solid var(--border-color); border-radius: 14px; padding: 1rem; background: color-mix(in srgb, var(--bg-color) 65%, transparent);">
            <h4 class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 1rem;">Available modules</h4>
            <div id="toolbox-zone" style="display: flex; flex-direction: column; gap: 0.75rem; min-height: 100px;">
                <div class="module-card mono toolbox-item" data-id="new-banner" data-type="banner" style="background: var(--bg-color); border: 1px dashed var(--accent-color); padding: 1rem; border-radius: 10px; cursor: grab; font-size: 0.8rem; color: var(--text-main);">☰ [ Cover Banner ]</div>
                <div class="module-card mono toolbox-item" data-id="new-avatar_info" data-type="avatar_info" style="background: var(--bg-color); border: 1px dashed var(--accent-color); padding: 1rem; border-radius: 10px; cursor: grab; font-size: 0.8rem; color: var(--text-main);">☰ [ Profile Identity ]</div>
                <div class="module-card mono toolbox-item" data-id="new-bio" data-type="bio" style="background: var(--bg-color); border: 1px dashed var(--accent-color); padding: 1rem; border-radius: 10px; cursor: grab; font-size: 0.8rem; color: var(--text-main);">☰ [ Artist Bio ]</div>
                <div class="module-card mono toolbox-item" data-id="new-text_block" data-type="text_block" style="background: var(--bg-color); border: 1px dashed var(--accent-color); padding: 1rem; border-radius: 10px; cursor: grab; font-size: 0.8rem; color: var(--text-main);">☰ [ Custom Text Block ]</div>
                <div class="module-card mono toolbox-item" data-id="new-gallery_feed" data-type="gallery_feed" style="background: var(--bg-color); border: 1px dashed var(--accent-color); padding: 1rem; border-radius: 10px; cursor: grab; font-size: 0.8rem; color: var(--text-main);">☰ [ Art Gallery Feed ]</div>
                <div class="module-card mono toolbox-item" data-id="new-comm_slots" data-type="comm_slots" style="background: var(--bg-color); border: 1px dashed var(--accent-color); padding: 1rem; border-radius: 10px; cursor: grab; font-size: 0.8rem; color: var(--text-main);">☰ [ Comm Status & Pricing ]</div>
                <div class="module-card mono toolbox-item" data-id="new-kanban_tracker" data-type="kanban_tracker" style="background: var(--bg-color); border: 1px dashed var(--accent-color); padding: 1rem; border-radius: 10px; cursor: grab; font-size: 0.8rem; color: var(--text-main);">☰ [ Commission Queue ]</div>
                <div class="module-card mono toolbox-item" data-id="new-links" data-type="links" style="background: var(--bg-color); border: 1px dashed var(--accent-color); padding: 1rem; border-radius: 10px; cursor: grab; font-size: 0.8rem; color: var(--text-main);">☰ [ Social Links ]</div>
                <div class="module-card mono toolbox-item" data-id="new-tip_jar" data-type="tip_jar" style="background: var(--bg-color); border: 1px dashed var(--accent-color); padding: 1rem; border-radius: 10px; cursor: grab; font-size: 0.8rem; color: var(--text-main);">☰ [ Tip Jar / Support ]</div>
            </div>
        </section>
    </div>

    <div style="padding: 1.5rem; border-top: 1px solid var(--border-color); display: flex; gap: 0.75rem;">
        <button id="save-layout-btn" class="btn btn-primary" style="flex: 1; justify-content: center;">Save Layout</button>
        <button id="load-starter-btn" type="button" class="btn btn-ghost default-config-btn" data-default-key="starter_classic" style="justify-content: center;">Reset Safe</button>
    </div>
</div>

<!-- Floating Edit Button -->
<button id="open-drawer-btn" class="btn btn-primary" style="position: fixed; bottom: 3rem; left: 3rem; z-index: 9998; border-radius: 99px; box-shadow: 0 10px 40px rgba(0,0,0,0.6); padding: 1.2rem 2.4rem; border: 2px solid rgba(255,255,255,0.1);">
    <span class="mono" style="font-weight: bold; text-transform: uppercase; font-size: 0.9rem;">⚙ Edit Artist Page</span>
</button>
