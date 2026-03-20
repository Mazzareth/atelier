<!-- Pricing Sheet Modal (Calculator) -->
<div id="pricing-sheet-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 10002; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
    <div style="background: var(--bg-panel); border: 1px solid var(--border-color); border-radius: 12px; width: 95%; max-width: 900px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 20px 60px rgba(0,0,0,1);">
        
        <div style="padding: 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 class="serif" style="margin: 0; color: var(--accent-color); font-size: 2rem;">Commission Pricing</h2>
                <p class="mono" style="margin: 0; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Calculate your total</p>
            </div>
            <button id="close-pricing-modal" style="background: none; border: none; font-size: 2.5rem; color: var(--text-muted); cursor: pointer; line-height: 1;">×</button>
        </div>
        
        <div style="padding: 2.5rem; flex: 1; overflow-y: auto; display: grid; grid-template-columns: 1.5fr 1fr; gap: 3rem;">
            
            <!-- Left: Options Selection -->
            <div id="pricing-options-form" style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- Dynamically filled by JS -->
            </div>

            <!-- Right: Quote Summary -->
            <div style="background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 8px; padding: 2rem; display: flex; flex-direction: column; height: fit-content; position: sticky; top: 0;">
                <h4 class="mono" style="margin-bottom: 1.5rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Estimated Quote</h4>
                
                <div id="quote-items" style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 2rem;">
                    <!-- Dynamically filled -->
                </div>

                <div style="margin-top: auto; border-top: 2px solid var(--border-color); padding-top: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <span class="mono" style="font-size: 0.9rem; color: var(--text-muted);">TOTAL</span>
                        <span id="quote-total" class="serif" style="font-size: 2.5rem; color: var(--accent-color);">$0</span>
                    </div>
                </div>

                <button class="btn btn-primary" style="margin-top: 2rem; width: 100%; justify-content: center; padding: 1rem;">
                    Request Commission
                </button>
                <p class="mono" style="font-size: 0.6rem; color: var(--text-muted); text-align: center; margin-top: 1rem;">Final price subject to artist approval</p>
            </div>
        </div>
    </div>
</div>

<style>
    .pricing-category-label { font-size: 0.7rem; color: var(--accent-color); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 1rem; display: block; }
    .pricing-option-card { background: var(--bg-color); border: 1px solid var(--border-color); padding: 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s ease; display: flex; justify-content: space-between; align-items: center; }
    .pricing-option-card:hover { border-color: var(--accent-color); background: var(--accent-dim); }
    .pricing-option-card.active { border-color: var(--accent-color); background: var(--accent-dim); box-shadow: inset 0 0 10px rgba(var(--particle-color), 0.1); }
    .pricing-option-info { display: flex; flex-direction: column; }
    .pricing-option-name { color: var(--text-main); font-size: 1rem; font-weight: bold; }
    .pricing-option-price { color: var(--text-muted); font-size: 0.8rem; }
    
    /* Number Input Styling */
    .pricing-qty-input { background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main); padding: 0.5rem; border-radius: 4px; width: 60px; text-align: center; outline: none; }
    .pricing-qty-input:focus { border-color: var(--accent-color); }
</style>
