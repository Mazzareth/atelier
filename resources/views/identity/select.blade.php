@extends('layouts.app')

@section('content')
<style>
    .identity-page {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        text-align: center;
        position: relative;
    }

    .identity-page::before {
        content: '';
        position: fixed;
        inset: 0;
        background: radial-gradient(ellipse at 50% 30%, rgba(var(--accent-color-rgb), 0.08) 0%, transparent 60%);
        pointer-events: none;
    }

    .identity-content {
        max-width: 560px;
        width: 100%;
        position: relative;
        z-index: 1;
    }

    .identity-eyebrow {
        font-family: var(--font-mono);
        font-size: 0.7rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--accent-color);
        margin-bottom: 1rem;
        opacity: 0.8;
    }

    .identity-title {
        font-family: var(--font-display);
        font-size: 3rem;
        font-weight: var(--font-weight-heading);
        letter-spacing: var(--letter-spacing);
        color: var(--text-main);
        margin-bottom: 0.75rem;
        line-height: 1.1;
    }

    .identity-subtitle {
        font-size: 1.05rem;
        color: var(--text-muted);
        margin-bottom: 3rem;
        line-height: 1.5;
    }

    .identity-options {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .identity-option {
        position: relative;
    }

    .identity-option input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .identity-option-label {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1.25rem 1.75rem;
        border-radius: var(--radius-card);
        border: var(--border-width) var(--border-style) var(--border-color);
        background: var(--panel-bg);
        cursor: pointer;
        transition: all calc(0.3s * var(--anim-speed)) ease;
        box-shadow: var(--shadow-card);
        text-align: left;
        width: 100%;
    }

    .identity-option-label:hover {
        border-color: color-mix(in srgb, var(--accent-color) 40%, var(--border-color));
        transform: translateY(-2px);
        box-shadow: var(--shadow-card), 0 0 20px rgba(var(--accent-color-rgb), 0.1);
    }

    .identity-option input:checked + .identity-option-label {
        border-color: var(--accent-color);
        background: color-mix(in srgb, var(--accent-color) 8%, var(--panel-bg));
        box-shadow: var(--shadow-card), 0 0 30px rgba(var(--accent-color-rgb), 0.15);
    }

    .identity-option-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-base);
        background: var(--accent-dim);
        border: var(--border-width) var(--border-style) color-mix(in srgb, var(--accent-color) 30%, var(--border-color));
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.5rem;
        color: var(--accent-color);
    }

    .identity-option-text {
        flex: 1;
    }

    .identity-option-name {
        font-family: var(--font-display);
        font-weight: var(--font-weight-heading);
        font-size: 1.2rem;
        color: var(--text-main);
        letter-spacing: var(--letter-spacing);
        margin-bottom: 0.2rem;
    }

    .identity-option-desc {
        font-size: 0.85rem;
        color: var(--text-muted);
        line-height: 1.4;
    }

    .identity-option-check {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: var(--border-width) var(--border-style) var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: transparent;
        font-size: 0.75rem;
        transition: all calc(0.2s * var(--anim-speed)) ease;
    }

    .identity-option input:checked + .identity-option-label .identity-option-check {
        background: var(--accent-color);
        border-color: var(--accent-color);
        color: #000;
    }

    /* Other preference field */
    .identity-other-field {
        display: none;
        margin-bottom: 1.5rem;
        animation: fadeSlideIn calc(0.3s * var(--anim-speed)) ease;
    }

    .identity-other-field.visible {
        display: block;
    }

    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .identity-other-field label {
        display: block;
        font-size: 0.8rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.5rem;
        font-family: var(--font-mono);
    }

    .identity-other-field input {
        width: 100%;
        padding: 0.9rem 1.1rem;
        border-radius: var(--radius-card);
        border: var(--border-width) var(--border-style) var(--border-color);
        background: var(--panel-bg);
        color: var(--text-main);
        font-size: 0.95rem;
        box-shadow: var(--shadow-card);
        outline: none;
        transition: border-color calc(0.2s * var(--anim-speed)) ease;
    }

    .identity-other-field input:focus {
        border-color: var(--accent-color);
    }

    /* Submit button */
    .identity-submit {
        width: 100%;
        padding: 1.1rem;
        border-radius: var(--radius-btn);
        border: none;
        background: var(--accent-color);
        color: #000;
        font-family: var(--font-display);
        font-weight: var(--font-weight-heading);
        font-size: 1.1rem;
        letter-spacing: var(--letter-spacing);
        cursor: pointer;
        box-shadow: var(--shadow-glow);
        transition: all calc(0.25s * var(--anim-speed)) ease;
        text-transform: uppercase;
    }

    .identity-submit:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-glow), 0 0 40px rgba(var(--accent-color-rgb), 0.3);
    }

    .identity-submit:active {
        transform: translateY(0);
    }

    .identity-note {
        margin-top: 1.5rem;
        font-size: 0.78rem;
        color: var(--text-muted);
        opacity: 0.7;
        font-family: var(--font-mono);
        letter-spacing: 0.04em;
    }

    .identity-divider {
        width: 40px;
        height: 2px;
        background: var(--accent-color);
        margin: 2rem auto;
        opacity: 0.4;
        border-radius: 2px;
    }

    .identity-skip {
        display: inline-block;
        font-size: 0.8rem;
        color: var(--text-muted);
        text-decoration: none;
        font-family: var(--font-mono);
        letter-spacing: 0.06em;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-base);
        transition: color calc(0.2s * var(--anim-speed)) ease;
    }

    .identity-skip:hover {
        color: var(--text-main);
    }

    /* Dom-specific styles */
    [data-theme="dickgirl-dom"] .identity-title {
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }

    [data-theme="dickgirl-dom"] .identity-option-label {
        border-radius: 0;
    }

    [data-theme="dickgirl-dom"] .identity-submit {
        border-radius: 0;
        letter-spacing: 0.15em;
    }

    /* Mommy-specific styles */
    [data-theme="dickgirl-mommy"] .identity-option-label {
        border-radius: 24px;
    }

    [data-theme="dickgirl-mommy"] .identity-submit {
        border-radius: 999px;
    }

    [data-theme="dickgirl-mommy"] .identity-option-icon {
        border-radius: 50%;
    }
</style>

<div class="identity-page">
    <div class="identity-content">
        <div class="identity-eyebrow mono">Who are you?</div>

        <h1 class="identity-title">
            @if($theme === 'dickgirl-dom')
                Tell me who you are.
            @else
                Hello, sweetie.
            @endif
        </h1>

        <p class="identity-subtitle">
            @if($theme === 'dickgirl-dom')
                I need to know how to handle you. Choose honestly.
            @else
                I want to know how to take care of you properly. This shapes your whole experience here.
            @endif
        </p>

        <form method="POST" action="{{ route('identity.store') }}">
            @csrf

            <div class="identity-options">
                <div class="identity-option">
                    <input type="radio" name="identity" id="identity_male" value="male" required>
                    <label for="identity_male" class="identity-option-label">
                        <div class="identity-option-icon">
                            @if($theme === 'dickgirl-dom')
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="14" r="5"/><line x1="19" y1="5" x2="5" y2="19"/><polyline points="15 5 19 5 19 9"/></svg>
                            @else
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            @endif
                        </div>
                        <div class="identity-option-text">
                            <div class="identity-option-name">Male</div>
                            <div class="identity-option-desc">
                                @if($theme === 'dickgirl-dom')
                                    You'll be teased. In the best way.
                                @else
                                    I'll take very good care of you.
                                @endif
                            </div>
                        </div>
                        <div class="identity-option-check">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                    </label>
                </div>

                <div class="identity-option">
                    <input type="radio" name="identity" id="identity_female" value="female" required>
                    <label for="identity_female" class="identity-option-label">
                        <div class="identity-option-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div class="identity-option-text">
                            <div class="identity-option-name">Female</div>
                            <div class="identity-option-desc">
                                @if($theme === 'dickgirl-dom')
                                    You'll be treated like you deserve. Properly.
                                @else
                                    You're going to be cherished here, sweet girl.
                                @endif
                            </div>
                        </div>
                        <div class="identity-option-check">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                    </label>
                </div>

                <div class="identity-option">
                    <input type="radio" name="identity" id="identity_dickgirl" value="dickgirl" required>
                    <label for="identity_dickgirl" class="identity-option-label">
                        <div class="identity-option-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        </div>
                        <div class="identity-option-text">
                            <div class="identity-option-name">Dickgirl</div>
                            <div class="identity-option-desc">
                                @if($theme === 'dickgirl-dom')
                                    One of us. You'll be shown respect.
                                @else
                                    Welcome, dear. You belong here.
                                @endif
                            </div>
                        </div>
                        <div class="identity-option-check">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                    </label>
                </div>

                <div class="identity-option">
                    <input type="radio" name="identity" id="identity_other" value="other" required>
                    <label for="identity_other" class="identity-option-label">
                        <div class="identity-option-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <div class="identity-option-text">
                            <div class="identity-option-name">Other</div>
                            <div class="identity-option-desc">
                                @if($theme === 'dickgirl-dom')
                                    Tell me your preference.
                                @else
                                    Let me know what you're comfortable with.
                                @endif
                            </div>
                        </div>
                        <div class="identity-option-check">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                    </label>
                </div>
            </div>

            <div class="identity-other-field" id="other_field">
                <label for="other_preference">How would you like to be treated?</label>
                <input
                    type="text"
                    name="other_preference"
                    id="other_preference"
                    placeholder="e.g. soft and gentle, firm but fair, whatever feels right..."
                    maxlength="200"
                >
            </div>

            <button type="submit" class="identity-submit">
                @if($theme === 'dickgirl-dom')
                    That's Who You Are
                @else
                    Take Care of Me
                @endif
            </button>
        </form>

        <div class="identity-divider"></div>

        <a href="{{ route('identity.clear') }}" class="identity-skip">
            Skip for now
        </a>

        <p class="identity-note">
            This shapes how artists and the platform talk to you. You can change it anytime.
        </p>
    </div>
</div>

<script>
    // Show/hide other preference field
    document.querySelectorAll('input[name="identity"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var otherField = document.getElementById('other_field');
            if (this.value === 'other') {
                otherField.classList.add('visible');
                document.getElementById('other_preference').focus();
            } else {
                otherField.classList.remove('visible');
            }
        });
    });
</script>
@endsection
