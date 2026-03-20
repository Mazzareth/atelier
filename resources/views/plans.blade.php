@extends('layouts.app')

@section('content')
    <div style="padding: 6rem 4rem; max-width: 1200px; margin: 0 auto; text-align: center;">
        <span class="pill"><span class="dot"></span> My Subscription</span>
        <h1 class="serif" style="font-size: 4rem; margin-top: 1rem;">Manage <span class="highlight">Plans</span></h1>
        <p class="mono" style="color: var(--text-muted); margin-bottom: 5rem;">You are currently on the <span class="highlight">Free</span> plan. Upgrade to unlock more features.</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
            <!-- Free -->
            <div style="background: var(--bg-panel); border: 1px solid var(--accent-color); border-radius: 12px; padding: 3rem 2rem; display: flex; flex-direction: column; position: relative;">
                <div class="mono" style="position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--accent-color); color: var(--bg-color); padding: 0.2rem 1rem; border-radius: 99px; font-size: 0.7rem; font-weight: bold;">CURRENT</div>
                <h3 class="serif" style="font-size: 2rem; margin-bottom: 0.5rem;">Free</h3>
                <div class="mono highlight" style="font-size: 1.5rem; margin-bottom: 2rem;">$0<span style="font-size: 0.8rem; opacity: 0.6;">/mo</span></div>
                <div class="mono" style="text-align: left; color: var(--text-muted); font-size: 0.85rem; flex: 1; margin-bottom: 2rem;">
                    <p style="margin-bottom: 1rem;">1% added fee for free plan (just for cost <3)</p>
                </div>
                <button class="btn btn-ghost" disabled style="justify-content: center; cursor: default; opacity: 0.5;">Active</button>
            </div>

            <!-- Tier 1 -->
            <div style="background: var(--bg-panel); border: 1px solid var(--border-color); border-radius: 12px; padding: 3rem 2rem; display: flex; flex-direction: column;">
                <h3 class="serif" style="font-size: 2rem; margin-bottom: 0.5rem;">Tier 1</h3>
                <div class="mono highlight" style="font-size: 1.5rem; margin-bottom: 2rem;">$10<span style="font-size: 0.8rem; opacity: 0.6;">/mo</span></div>
                <div style="flex: 1;"></div>
                <button class="btn btn-primary" style="justify-content: center;">Upgrade</button>
            </div>

            <!-- Tier 2 -->
            <div style="background: var(--bg-panel); border: 1px solid var(--border-color); border-radius: 12px; padding: 3rem 2rem; display: flex; flex-direction: column;">
                <h3 class="serif" style="font-size: 2rem; margin-bottom: 0.5rem;">Tier 2</h3>
                <div class="mono highlight" style="font-size: 1.5rem; margin-bottom: 2rem;">$25<span style="font-size: 0.8rem; opacity: 0.6;">/mo</span></div>
                <div style="flex: 1;"></div>
                <button class="btn btn-primary" style="justify-content: center;">Upgrade</button>
            </div>

            <!-- Tier 3 -->
            <div style="background: var(--bg-panel); border: 1px solid var(--border-color); border-radius: 12px; padding: 3rem 2rem; display: flex; flex-direction: column;">
                <h3 class="serif" style="font-size: 2rem; margin-bottom: 0.5rem;">Tier 3</h3>
                <div class="mono highlight" style="font-size: 1.5rem; margin-bottom: 2rem;">$50<span style="font-size: 0.8rem; opacity: 0.6;">/mo</span></div>
                <div style="flex: 1;"></div>
                <button class="btn btn-primary" style="justify-content: center;">Upgrade</button>
            </div>
        </div>
    </div>
@endsection
