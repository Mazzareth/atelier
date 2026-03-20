@extends('layouts.app')

@section('content')
<div style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
    <div style="width: 100%; max-width: 720px; position: relative;">
        <div class="theme-extra" style="display: block; position: absolute; top: -60px; left: -40px; font-size: 6rem; opacity: 0.06; pointer-events: none; z-index: 0;">✦</div>

        <div style="position: relative; z-index: 1; text-align: center; margin-bottom: 3rem;">
            <div class="pill mono" style="margin-bottom: 1.5rem; display: inline-flex;">
                <div class="dot"></div>
                ● start here
            </div>
            <h1 class="serif" style="font-size: clamp(2rem, 5vw, 3rem); margin-bottom: 1rem;">
                <span class="light">What brings you</span> <span class="highlight">here?</span>
            </h1>
            <p class="hero-text serif" style="font-size: 1.1rem; max-width: 480px; margin: 0 auto;">
                Choose your path. You can always add capabilities later.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            {{-- Artist Card --}}
            <a href="{{ route('register', ['type' => 'artist']) }}" style="text-decoration: none; display: block;">
                <div style="background: var(--bg-panel); border: 1px solid var(--border-color); padding: 2.5rem 2rem; border-radius: 16px; transition: all 0.25s ease; position: relative; overflow: hidden; height: 100%;">
                    <div style="position: absolute; top: 0; right: 0; width: 120px; height: 120px; background: radial-gradient(circle at top right, color-mix(in srgb, var(--accent-color) 15%, transparent), transparent 70%); pointer-events: none;"></div>

                    <div style="position: relative; z-index: 1;">
                        <div class="pill mono" style="margin-bottom: 1.25rem; display: inline-flex; background: color-mix(in srgb, var(--accent-color) 12%, transparent); border-color: color-mix(in srgb, var(--accent-color) 40%, var(--border-color));">
                            <div class="dot" style="background: var(--accent-color);"></div>
                            ● artist
                        </div>

                        <h2 class="serif" style="font-size: 1.75rem; margin-bottom: 0.75rem; color: var(--text-main);">
                            I want to <span class="highlight">sell my work</span>
                        </h2>

                        <p style="color: var(--text-muted); line-height: 1.6; font-size: 0.9rem; margin-bottom: 1.5rem;">
                            Set up an artist profile, receive commission requests, manage your workspace, and get paid for your creative work.
                        </p>

                        <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                            <div style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-muted); font-size: 0.82rem;">
                                <span style="color: var(--accent-color);">+</span> Customizable artist page
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-muted); font-size: 0.82rem;">
                                <span style="color: var(--accent-color);">+</span> Commission request inbox
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-muted); font-size: 0.82rem;">
                                <span style="color: var(--accent-color);">+</span> Workspace & file sharing
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-muted); font-size: 0.82rem;">
                                <span style="color: var(--accent-color);">+</span> Revenue tracking
                            </div>
                        </div>

                        <div class="mono" style="margin-top: 2rem; font-size: 0.78rem; color: var(--accent-color);">
                            Create artist account →
                        </div>
                    </div>
                </div>
            </a>

            {{-- Client Card --}}
            <a href="{{ route('register', ['type' => 'client']) }}" style="text-decoration: none; display: block;">
                <div style="background: var(--bg-panel); border: 1px solid var(--border-color); padding: 2.5rem 2rem; border-radius: 16px; transition: all 0.25s ease; position: relative; overflow: hidden; height: 100%;">
                    <div style="position: absolute; top: 0; right: 0; width: 120px; height: 120px; background: radial-gradient(circle at top right, color-mix(in srgb, var(--accent-color) 15%, transparent), transparent 70%); pointer-events: none;"></div>

                    <div style="position: relative; z-index: 1;">
                        <div class="pill mono" style="margin-bottom: 1.25rem; display: inline-flex; background: color-mix(in srgb, var(--accent-color) 12%, transparent); border-color: color-mix(in srgb, var(--accent-color) 40%, var(--border-color));">
                            <div class="dot" style="background: var(--accent-color);"></div>
                            ● client
                        </div>

                        <h2 class="serif" style="font-size: 1.75rem; margin-bottom: 0.75rem; color: var(--text-main);">
                            I want to <span class="highlight">hire an artist</span>
                        </h2>

                        <p style="color: var(--text-muted); line-height: 1.6; font-size: 0.9rem; margin-bottom: 1.5rem;">
                            Browse artists, follow your favorites, send commission requests, and manage your projects in one place.
                        </p>

                        <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                            <div style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-muted); font-size: 0.82rem;">
                                <span style="color: var(--accent-color);">+</span> Browse artist profiles
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-muted); font-size: 0.82rem;">
                                <span style="color: var(--accent-color);">+</span> Follow favorite artists
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-muted); font-size: 0.82rem;">
                                <span style="color: var(--accent-color);">+</span> Commission requests
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-muted); font-size: 0.82rem;">
                                <span style="color: var(--accent-color);">+</span> Message artists
                            </div>
                        </div>

                        <div class="mono" style="margin-top: 2rem; font-size: 0.78rem; color: var(--accent-color);">
                            Create client account →
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="mono" style="margin-top: 3rem; font-size: 0.82rem; color: var(--text-muted); text-align: center;">
            Already have an account?
            <a href="{{ route('login') }}" style="color: var(--accent-color); text-decoration: none; margin-left: 0.5rem;">Log in here</a>
        </div>
    </div>
</div>

<style>
    a:hover > div {
        border-color: color-mix(in srgb, var(--accent-color) 50%, var(--border-color));
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
</style>
@endsection
