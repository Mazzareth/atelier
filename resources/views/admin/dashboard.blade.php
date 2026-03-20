@extends('layouts.app')

@section('content')
<div style="min-height: 80vh; padding: 4rem;">
    <div style="max-width: 1200px; margin: 0 auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem; border-bottom: 1px solid var(--border-color); padding-bottom: 2rem;">
            <div>
                <div class="pill mono" style="margin-bottom: 1rem; border-color: var(--accent-color); color: var(--accent-color); background: var(--accent-dim);">
                    <div class="dot" style="background: var(--accent-color);"></div>
                    ● platform control
                </div>
                <h1 class="serif" style="font-size: 3.5rem; margin-bottom: 0;">
                    <span class="light">Admin</span> <span class="highlight" style="color: var(--accent-color);">panel.</span>
                </h1>
                <p class="hero-text serif" style="margin-top: 1rem; margin-bottom: 0; font-size: 1.1rem;">
                    Server metrics and user management.
                </p>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <!-- removed switcher -->
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 3rem;">
            <div style="background: var(--bg-panel); border: 1px solid var(--border-color); border-left: 4px solid var(--accent-color); padding: 1.5rem; border-radius: 8px;">
                <h4 class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Total Users</h4>
                <div class="serif" style="font-size: 2rem; color: var(--text-main);">1,245</div>
            </div>
            <div style="background: var(--bg-panel); border: 1px solid var(--border-color); border-left: 4px solid var(--accent-color); padding: 1.5rem; border-radius: 8px; opacity: 0.9;">
                <h4 class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Active Artists</h4>
                <div class="serif" style="font-size: 2rem; color: var(--text-main);">389</div>
            </div>
            <div style="background: var(--bg-panel); border: 1px solid var(--border-color); border-left: 4px solid var(--accent-color); padding: 1.5rem; border-radius: 8px; opacity: 0.8;">
                <h4 class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Escrow Volume</h4>
                <div class="serif" style="font-size: 2rem; color: var(--text-main);">$14.2k</div>
            </div>
            <div style="background: var(--bg-panel); border: 1px solid var(--border-color); border-left: 4px solid var(--accent-color); padding: 1.5rem; border-radius: 8px;">
                <h4 class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Reports</h4>
                <div class="serif" style="font-size: 2rem; color: var(--text-main);">0</div>
            </div>
        </div>

    </div>
</div>
@endsection
