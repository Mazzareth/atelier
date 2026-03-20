<!-- Edit Modal -->
<div id="edit-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: var(--bg-panel); border: 1px solid var(--border-color); border-radius: 8px; width: 90%; max-width: 950px; max-height: 90vh; display: flex; flex-direction: column;">
        
        <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modal-title" class="mono" style="margin: 0; color: var(--accent-color);">Edit Module</h3>
            <button id="close-modal" style="background: none; border: none; font-size: 1.8rem; color: var(--text-muted); cursor: pointer;">×</button>
        </div>
        
        <div id="modal-body" style="padding: 2rem; flex: 1; overflow: auto; display: flex; flex-direction: column; gap: 1rem;">
            
            <!-- Default / Catch-all (for things like Banner/Gallery that have no specific UI yet) -->
            <div id="editor-default" style="display: none; flex-direction: column; gap: 1rem; text-align: center; padding: 4rem 2rem;">
                <p class="mono" style="color: var(--text-muted); font-size: 0.9rem;">This module uses a simplified configuration.</p>
                <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: center;">
                    <label class="mono" style="font-size: 0.75rem; color: var(--accent-color);">Raw Settings (JSON)</label>
                    <textarea id="default-settings-json" style="width: 100%; max-width: 400px; min-height: 100px; background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 1rem; border-radius: 4px; font-family: monospace; font-size: 0.8rem;"></textarea>
                </div>
            </div>

            <!-- Markdown Editor (Bio/Text) -->
            <div id="editor-markdown" style="display: none; flex-direction: column; gap: 1rem;">
                <div id="markdown-toolbar" style="display: flex; gap: 0.4rem; background: var(--bg-color); padding: 0.4rem; border: 1px solid var(--border-color); border-radius: 4px; flex-wrap: wrap;">
                    <button class="md-btn mono" data-md="h1" title="Heading 1">H1</button>
                    <button class="md-btn mono" data-md="h2" title="Heading 2">H2</button>
                    <button class="md-btn mono" data-md="h3" title="Heading 3">H3</button>
                    <div style="width: 1px; background: var(--border-color); margin: 0 2px;"></div>
                    <button class="md-btn mono" data-md="bold" title="Bold"><strong>B</strong></button>
                    <button class="md-btn mono" data-md="italic" title="Italic"><em>I</em></button>
                    <button class="md-btn mono" data-md="link" title="Link">Link</button>
                    <button class="md-btn mono" data-md="image" title="Image">Img</button>
                    <div style="width: 1px; background: var(--border-color); margin: 0 2px;"></div>
                    <button class="md-btn mono" data-md="list" title="Bulleted List">• List</button>
                    <button class="md-btn mono" data-md="quote" title="Quote">Quote</button>
                    <button class="md-btn mono" data-md="code" title="Code Block">Code</button>
                </div>

                <textarea id="modal-textarea" style="width: 100%; min-height: 250px; background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 1rem; font-size: 1rem; resize: vertical; border-radius: 4px; font-family: 'Inter', sans-serif;"></textarea>
                <div id="modal-preview-header" class="mono" style="font-size: 0.7rem; color: var(--accent-color); text-transform: uppercase; margin-top: 1rem;">Live Preview</div>
                <div id="modal-preview" style="background: var(--bg-color); border: 1px solid var(--border-color); padding: 2rem; border-radius: 4px; min-height: 150px; overflow: auto; line-height: 1.6;"></div>
            </div>

            <!-- Commission Slots Editor -->
            <div id="editor-comm_slots" style="display: none; flex-direction: column; gap: 2.5rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Total Slots Open</label>
                        <input type="number" id="slots-open-input" style="background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.8rem; border-radius: 4px; outline: none;">
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Next Opening Date (Optional)</label>
                        <input type="text" id="next-open-date-input" placeholder="e.g. Next Monday" style="background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.8rem; border-radius: 4px; outline: none;">
                    </div>
                </div>

                <!-- PRICING SHEET BUILDER -->
                <div style="border-top: 1px solid var(--border-color); padding-top: 2rem;">
                    <h4 class="mono" style="font-size: 0.9rem; color: var(--accent-color); text-transform: uppercase; margin-bottom: 0.5rem;">Pricing Sheet & Calculator</h4>
                    <p class="mono" style="font-size: 0.65rem; color: var(--text-muted); margin-bottom: 2rem; line-height: 1.6;">
                        <strong>Type Key:</strong> <span style="color: var(--text-main);">$ (Flat)</span>, <span style="color: var(--text-main);">% (Percent of cost)</span>, <span style="color: var(--text-main);">x (Multiplier)</span>.<br>
                        Percentage and Multipliers apply to the sum of Base + Flat add-ons.
                    </p>
                    
                    <div style="display: flex; flex-direction: column; gap: 3rem;">
                        <!-- Base Types Section -->
                        <div>
                            <p class="pricing-category-label">Base Types (Mutually Exclusive)</p>
                            <div id="base-types-builder" style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1rem;">
                                <!-- Dynamic Rows -->
                            </div>
                            <button id="add-base-type" class="btn btn-ghost mono" style="font-size: 0.7rem; width: 100%; border-style: dashed;">+ Add Base Offering</button>
                        </div>

                        <!-- Add-ons Section -->
                        <div>
                            <p class="pricing-category-label">Add-ons & Modifiers (Stackable)</p>
                            <div id="addons-builder" style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1rem;">
                                <!-- Dynamic Rows -->
                            </div>
                            <button id="add-addon" class="btn btn-ghost mono" style="font-size: 0.7rem; width: 100%; border-style: dashed;">+ Add Modifier</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Links Editor -->
            <div id="editor-links" style="display: none; flex-direction: column; gap: 1.5rem;">
                <p class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Social Links</p>
                <div id="links-container" style="display: flex; flex-direction: column; gap: 1rem;"></div>
                <button id="add-link-row" class="btn btn-ghost" style="font-size: 0.7rem; padding: 0.5rem 1rem; border-color: var(--accent-color); color: var(--accent-color);">+ Add Link</button>
            </div>

            <div id="editor-tip_jar" style="display: none; flex-direction: column; gap: 1rem;">
                <label class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Support Message</label>
                <textarea id="tip-jar-message-input" style="width: 100%; min-height: 140px; background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 1rem; font-size: 0.95rem; resize: vertical; border-radius: 4px;"></textarea>
                <label class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Emoji</label>
                <input id="tip-jar-emoji-input" type="text" maxlength="4" style="background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.8rem; border-radius: 4px; outline: none; width: 120px;">
            </div>

            <div id="editor-kanban_tracker" style="display: none; flex-direction: column; gap: 1rem;">
                <label class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Tracker Title</label>
                <input id="kanban-title-input" type="text" style="background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.8rem; border-radius: 4px; outline: none;">
            </div>

            <div id="editor-gallery_feed" style="display: none; flex-direction: column; gap: 1rem;">
                <label class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Gallery Style</label>
                <select id="gallery-layout-input" style="background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.8rem; border-radius: 4px; outline: none; max-width: 220px;">
                    <option value="grid">Grid</option>
                    <option value="masonry">Masonry</option>
                    <option value="featured">Featured</option>
                </select>
            </div>

            <div id="editor-avatar_info" style="display: none; flex-direction: column; gap: 1rem;">
                <label class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; display: flex; align-items: center; gap: 0.75rem;">
                    <input id="avatar-show-follow-input" type="checkbox">
                    Show follow button on profile
                </label>
            </div>

            <div id="editor-banner" style="display: none; flex-direction: column; gap: 1rem;">
                <label class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Banner Background</label>
                <input id="banner-color-input" type="text" placeholder="var(--bg-panel) or #1a1a1a" style="background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.8rem; border-radius: 4px; outline: none;">
                <label class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Image URL (optional)</label>
                <input id="banner-image-input" type="text" placeholder="https://..." style="background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.8rem; border-radius: 4px; outline: none;">
            </div>

        </div>
        
        <div style="padding: 1.5rem 2rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 1rem;">
            <button id="cancel-edit" class="btn btn-ghost">Cancel</button>
            <button id="save-edit" class="btn btn-primary">Save Changes</button>
        </div>
    </div>
</div>

<!-- Custom Context Menu -->
<div id="markdown-context-menu" style="display: none; position: fixed; background: var(--bg-panel); border: 1px solid var(--border-color); border-radius: 4px; z-index: 10001; box-shadow: 0 10px 40px rgba(0,0,0,0.8); min-width: 180px; padding: 0.5rem 0;">
    <div class="ctx-header mono" style="padding: 0.3rem 1rem; font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase;">Formatting</div>
    <div class="ctx-item mono" data-md="bold"><strong>Bold</strong></div>
    <div class="ctx-item mono" data-md="italic"><em>Italic</em></div>
    <div class="ctx-item mono" data-md="link">Insert Link</div>
    <div style="height: 1px; background: var(--border-color); margin: 0.3rem 0;"></div>
    <div class="ctx-header mono" style="padding: 0.3rem 1rem; font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase;">Structure</div>
    <div class="ctx-item mono" data-md="h2">Heading</div>
    <div class="ctx-item mono" data-md="list">Bulleted List</div>
    <div class="ctx-item mono" data-md="quote">Blockquote</div>
    <div class="ctx-item mono" data-md="code">Code Block</div>
</div>

<style>
    .md-btn { background: transparent; border: 1px solid var(--border-color); color: var(--text-main); padding: 0.3rem 0.7rem; border-radius: 3px; cursor: pointer; font-size: 0.75rem; transition: all 0.2s ease; }
    .md-btn:hover { border-color: var(--accent-color); color: var(--accent-color); background: var(--accent-dim); }
    
    .ctx-item { padding: 0.6rem 1rem; color: var(--text-main); cursor: pointer; font-size: 0.8rem; display: flex; align-items: center; gap: 0.5rem; }
    .ctx-item:hover { background: var(--accent-dim); color: var(--accent-color); }
    
    .builder-row { display: flex; gap: 0.5rem; }
    .builder-row input, .builder-row select { background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.5rem; border-radius: 4px; font-size: 0.75rem; outline: none; }
    .builder-row input:focus, .builder-row select:focus { border-color: var(--accent-color); }
    .remove-row { background: none; border: 1px solid #ff4d4d; color: #ff4d4d; border-radius: 4px; cursor: pointer; padding: 0 0.5rem; font-size: 0.6rem; }
</style>
