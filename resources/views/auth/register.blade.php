@extends('layouts.app')

@section('content')
<div style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
    <div style="background: var(--bg-panel); border: 1px solid var(--border-color); padding: 3rem; border-radius: 8px; width: 100%; max-width: 450px; position: relative;">
        <div class="theme-extra" style="display: block; position: absolute; top: -30px; left: -30px; font-size: 4rem; opacity: 0.1; pointer-events: none; z-index: 0;">✦</div>

        <div style="position: relative; z-index: 1;">
            <div class="pill mono" style="margin-bottom: 1.5rem;">
                <div class="dot"></div>
                ● join
            </div>
            <h1 class="serif" style="font-size: 2.5rem; margin-bottom: 0.5rem;"><span class="light">Join as a</span> <span class="highlight">Commissioner.</span></h1>
            <p class="hero-text serif" style="font-size: 1rem; margin-bottom: 2rem;">Create a basic account with just a username and password. This signup path does not create an artist profile.</p>

            @if($errors->any())
                <div style="background: rgba(214, 41, 0, 0.15); border-left: 4px solid #d62900; padding: 1rem; margin-bottom: 1.5rem; color: #f0f4f2; font-size: 0.85rem;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" style="display: flex; flex-direction: column; gap: 1.5rem;">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="username" class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                        style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main); padding: 0.8rem 1rem; border-radius: 4px; outline: none; font-size: 0.9rem;">
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="password" class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Password</label>
                    <input type="password" id="password" name="password" required
                        style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main); padding: 0.8rem 1rem; border-radius: 4px; outline: none; font-size: 0.9rem;">
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <label for="password_confirmation" class="mono" style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        style="background: var(--bg-color); border: 1px solid var(--border-color); color: var(--text-main); padding: 0.8rem 1rem; border-radius: 4px; outline: none; font-size: 0.9rem;">
                </div>

                <button type="submit" class="btn btn-primary" style="justify-content: center; margin-top: 1rem; width: 100%;">
                    Create account <span class="arrow">→</span>
                </button>
            </form>

            <div class="mono" style="margin-top: 1.5rem; font-size: 0.78rem; color: var(--text-muted); text-align: center;">
                Already have an account?
                <a href="{{ route('login') }}" style="color: var(--accent-color); text-decoration: none;">Log in</a>
            </div>
        </div>
    </div>
</div>
@endsection
