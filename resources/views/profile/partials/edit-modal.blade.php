<!-- Edit Modal -->
<x-modal id="edit-modal" size="lg" closeable class="edit-modal-wrapper" style="display: none;">
    <div class="edit-modal-content">
        <div class="edit-modal-header">
            <h3 id="modal-title" class="font-mono text-accent">Edit Module</h3>
            <button id="close-modal" class="edit-modal-close">&times;</button>
        </div>
        
        <div id="modal-body" class="edit-modal-body">
            <!-- Default / Catch-all -->
            <div id="editor-default" class="editor-panel hidden-panel text-center py-12">
                <p class="font-mono text-sm text-muted mb-4">This module uses a simplified configuration.</p>
                <div class="flex flex-col items-center gap-2">
                    <label class="font-mono text-xs text-accent">Raw Settings (JSON)</label>
                    <x-textarea id="default-settings-json" class="w-full max-w-md font-mono text-sm" rows="6"></x-textarea>
                </div>
            </div>

            <!-- Markdown Editor (Bio/Text) -->
            <div id="editor-markdown" class="editor-panel hidden-panel">
                <div id="markdown-toolbar" class="md-toolbar">
                    <button class="md-btn font-mono" data-md="h1" title="Heading 1">H1</button>
                    <button class="md-btn font-mono" data-md="h2" title="Heading 2">H2</button>
                    <button class="md-btn font-mono" data-md="h3" title="Heading 3">H3</button>
                    <div class="md-divider"></div>
                    <button class="md-btn font-mono" data-md="bold" title="Bold"><strong>B</strong></button>
                    <button class="md-btn font-mono" data-md="italic" title="Italic"><em>I</em></button>
                    <button class="md-btn font-mono" data-md="link" title="Link">Link</button>
                    <button class="md-btn font-mono" data-md="image" title="Image">Img</button>
                    <div class="md-divider"></div>
                    <button class="md-btn font-mono" data-md="list" title="Bulleted List">&bull; List</button>
                    <button class="md-btn font-mono" data-md="quote" title="Quote">Quote</button>
                    <button class="md-btn font-mono" data-md="code" title="Code Block">Code</button>
                </div>

                <x-textarea id="modal-textarea" rows="10" class="w-full"></x-textarea>
                
                <div id="modal-preview-header" class="font-mono text-xs text-accent uppercase mt-4 mb-2">Live Preview</div>
                <div id="modal-preview" class="md-preview-box"></div>
            </div>

            <!-- Commission Slots Editor -->
            <div id="editor-comm_slots" class="editor-panel hidden-panel gap-8">
                <div class="grid grid-cols-2 gap-6">
                    <div class="flex flex-col gap-2">
                        <label class="font-mono text-xs text-muted uppercase">Total Slots Open</label>
                        <x-input type="number" id="slots-open-input" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-mono text-xs text-muted uppercase">Next Opening Date (Optional)</label>
                        <x-input type="text" id="next-open-date-input" placeholder="e.g. Next Monday" />
                    </div>
                </div>

                <!-- PRICING SHEET BUILDER -->
                <div class="pt-8 border-t border-border">
                    <h4 class="font-mono text-sm text-accent uppercase mb-2">Pricing Sheet & Calculator</h4>
                    <p class="font-mono text-xs text-muted mb-8 leading-relaxed">
                        <strong>Type Key:</strong> <span class="text-text">$ (Flat)</span>, <span class="text-text">% (Percent of cost)</span>, <span class="text-text">x (Multiplier)</span>.<br>
                        Percentage and Multipliers apply to the sum of Base + Flat add-ons.
                    </p>
                    
                    <div class="flex flex-col gap-12">
                        <!-- Base Types Section -->
                        <div>
                            <p class="pricing-category-label">Base Types (Mutually Exclusive)</p>
                            <div id="base-types-builder" class="flex flex-col gap-3 mb-4">
                                <!-- Dynamic Rows -->
                            </div>
                            <x-button id="add-base-type" variant="ghost" class="w-full border-dashed font-mono text-xs">
                                + Add Base Offering
                            </x-button>
                        </div>

                        <!-- Add-ons Section -->
                        <div>
                            <p class="pricing-category-label">Add-ons & Modifiers (Stackable)</p>
                            <div id="addons-builder" class="flex flex-col gap-3 mb-4">
                                <!-- Dynamic Rows -->
                            </div>
                            <x-button id="add-addon" variant="ghost" class="w-full border-dashed font-mono text-xs">
                                + Add Modifier
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Links Editor -->
            <div id="editor-links" class="editor-panel hidden-panel">
                <p class="font-mono text-xs text-muted uppercase mb-2">Social Links</p>
                <div id="links-container" class="flex flex-col gap-4 mb-4"></div>
                <x-button id="add-link-row" variant="ghost" class="text-xs text-accent border-accent">
                    + Add Link
                </x-button>
            </div>

            <div id="editor-tip_jar" class="editor-panel hidden-panel">
                <label class="font-mono text-xs text-muted uppercase mb-2 block">Support Message</label>
                <x-textarea id="tip-jar-message-input" rows="6" class="w-full mb-4"></x-textarea>
                
                <label class="font-mono text-xs text-muted uppercase mb-2 block">Emoji</label>
                <x-input id="tip-jar-emoji-input" type="text" maxlength="4" class="w-32" />
            </div>

            <div id="editor-kanban_tracker" class="editor-panel hidden-panel">
                <label class="font-mono text-xs text-muted uppercase mb-2 block">Tracker Title</label>
                <x-input id="kanban-title-input" type="text" class="w-full" />
            </div>

            <div id="editor-gallery_feed" class="editor-panel hidden-panel">
                <label class="font-mono text-xs text-muted uppercase mb-2 block">Gallery Style</label>
                <x-select id="gallery-layout-input" :options="['grid' => 'Grid', 'masonry' => 'Masonry', 'featured' => 'Featured']" class="max-w-xs" />
            </div>

            <div id="editor-avatar_info" class="editor-panel hidden-panel">
                <label class="font-mono text-xs text-muted uppercase flex items-center gap-3">
                    <input id="avatar-show-follow-input" type="checkbox">
                    Show follow button on profile
                </label>
            </div>

            <div id="editor-banner" class="editor-panel hidden-panel">
                <label class="font-mono text-xs text-muted uppercase mb-2 block">Banner Background</label>
                <x-input id="banner-color-input" type="text" placeholder="var(--bg-panel) or #1a1a1a" class="w-full mb-4" />
                
                <label class="font-mono text-xs text-muted uppercase mb-2 block">Image URL (optional)</label>
                <x-input id="banner-image-input" type="text" placeholder="https://..." class="w-full" />
            </div>

        </div>
        
        <div class="edit-modal-footer">
            <x-button id="cancel-edit" variant="ghost">Cancel</x-button>
            <x-button id="save-edit" variant="primary">Save Changes</x-button>
        </div>
    </div>
</x-modal>

<!-- Custom Context Menu -->
<div id="markdown-context-menu" class="md-context-menu" style="display: none;">
    <div class="ctx-header font-mono text-muted uppercase">Formatting</div>
    <div class="ctx-item font-mono" data-md="bold"><strong>Bold</strong></div>
    <div class="ctx-item font-mono" data-md="italic"><em>Italic</em></div>
    <div class="ctx-item font-mono" data-md="link">Insert Link</div>
    <div class="ctx-divider"></div>
    <div class="ctx-header font-mono text-muted uppercase">Structure</div>
    <div class="ctx-item font-mono" data-md="h2">Heading</div>
    <div class="ctx-item font-mono" data-md="list">Bulleted List</div>
    <div class="ctx-item font-mono" data-md="quote">Blockquote</div>
    <div class="ctx-item font-mono" data-md="code">Code Block</div>
</div>
