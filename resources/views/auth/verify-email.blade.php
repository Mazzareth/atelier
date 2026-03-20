@extends('layouts.app')

@section('content')
<div style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
    <div style="background: var(--bg-panel); border: 1px solid var(--border-color); padding: 3rem; border-radius: 8px; width: 100%; max-width: 500px; text-align: center; position: relative;">
        <div class="theme-extra" style="display: block; position: absolute; top: -30px; left: -30px; font-size: 4rem; opacity: 0.1; pointer-events: none; z-index: 0;">✦</div>

        <div style="position: relative; z-index: 1;">
            <div class="pill mono" style="margin-bottom: 1.5rem; display: inline-flex;">
                <div class="dot"></div>
                ● verify
            </div>

            <h1 class="serif" style="font-size: 2rem; margin-bottom: 1rem;">
                <span class="light">Check your</span> <span class="highlight">inbox.</span>
            </h1>

            <p class="hero-text serif" style="font-size: 1rem; margin-bottom: 2rem; color: var(--text-muted);">
                We've sent a verification link to <strong style="color: var(--text-main);">{{ auth()->user()->email }}</strong>.
                Click the link to activate your account.
            </p>

            @if(session('status'))
                <div style="background: rgba(43, 220, 108, 0.15); border-left: 4px solid #2bdc6c; padding: 1rem; margin-bottom: 1.5rem; color: #f0f4f2; font-size: 0.85rem;">
                    {{ session('status') }}
                </div>
            @endif

            <p class="mono" style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                Didn't receive the email? Check your spam folder or
            </p>

            <form method="POST" action="{{ route('verification.send') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-ghost">Resend verification email</button>
            </form>

            <div style="margin-top: 2rem;">
                <a href="{{ route('dashboard') }}" class="mono" style="color: var(--text-muted); font-size: 0.78rem;">
                    Skip for now and go to dashboard →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
