@extends('layouts.app')

@section('content')
@php
    $featuredUsers = $users->filter()->take(4)->values();
@endphp

<div class="landing-page">
    <section class="landing-hero container">
        <div class="landing-hero-copy">
            <div class="landing-kicker">
                <span class="landing-kicker-dot"></span>
                Artist-run commission platform
            </div>

            <h1 class="landing-title">
                Commission work without shaping your art around someone else's rules.
            </h1>

            <p class="landing-subtitle">
                Atelier gives independent artists a calmer home for profiles, queues, messages, and custom work. Built for niche communities, private commissions, and people who are done begging algorithms for permission.
            </p>

            <div class="landing-actions">
                <x-themed-button href="{{ route('onboard') }}" variant="primary" size="lg">
                    Start your studio
                </x-themed-button>

                @auth
                    <x-themed-button href="{{ route('browse') }}" variant="ghost" size="lg">
                        Browse artists
                    </x-themed-button>
                @else
                    <x-themed-button href="{{ route('pricing') }}" variant="ghost" size="lg">
                        See plans
                    </x-themed-button>
                @endauth
            </div>

            <div class="landing-proof">
                <div class="landing-proof-item">
                    <span class="landing-proof-value">0%</span>
                    <span class="landing-proof-label">platform cut on commissions</span>
                </div>
                <div class="landing-proof-item">
                    <span class="landing-proof-value">Direct</span>
                    <span class="landing-proof-label">chat between artist and client</span>
                </div>
                <div class="landing-proof-item">
                    <span class="landing-proof-value">Flexible</span>
                    <span class="landing-proof-label">for furry, NSFW, and niche work</span>
                </div>
            </div>
        </div>

        <div class="landing-hero-art" aria-hidden="true">
            <div class="landing-showcase">
                <div class="landing-showcase-panel landing-showcase-panel-main">
                    <div class="landing-panel-header">
                        <span class="landing-panel-pill">Studio status</span>
                        <span class="landing-panel-meta">Open for commissions</span>
                    </div>
                    <div class="landing-panel-body">
                        <div class="landing-price-card">
                            <span class="landing-price-label">Queue</span>
                            <strong>4 active slots</strong>
                            <span>Deposits held cleanly. No awkward chasing.</span>
                        </div>
                        <div class="landing-message-card">
                            <span class="landing-message-name">Client thread</span>
                            <p>Sketch approved. Move to flats and send the next update on Friday.</p>
                        </div>
                    </div>
                </div>

                <div class="landing-showcase-panel landing-showcase-panel-side">
                    <div class="landing-mini-stat">
                        <span class="landing-mini-value">Profile</span>
                        <span class="landing-mini-label">Custom modules, galleries, pricing</span>
                    </div>
                    <div class="landing-mini-stat">
                        <span class="landing-mini-value">Inbox</span>
                        <span class="landing-mini-label">Keep request details in one place</span>
                    </div>
                </div>

                @if($featuredUsers->isNotEmpty())
                    <div class="landing-handle-strip">
                        @foreach($featuredUsers as $username)
                            <span>/{{ $username }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="landing-band">
        <div class="container landing-band-grid">
            @foreach([
                [
                    'eyebrow' => '01',
                    'title' => 'Profiles that feel like your own space',
                    'desc' => 'Show your work, rates, slots, and style without flattening everything into a social feed.',
                ],
                [
                    'eyebrow' => '02',
                    'title' => 'Commission flow that stays readable',
                    'desc' => 'Requests, messages, and work tracking live together so artists and clients stop losing context.',
                ],
                [
                    'eyebrow' => '03',
                    'title' => 'Built for communities other platforms sideline',
                    'desc' => 'Niche, adult, furry, and experimental work are treated like part of the ecosystem, not a moderation accident.',
                ],
            ] as $feature)
                <article class="landing-feature-card">
                    <div class="landing-feature-eyebrow">{{ $feature['eyebrow'] }}</div>
                    <h2>{{ $feature['title'] }}</h2>
                    <p>{{ $feature['desc'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="container landing-story">
        <div class="landing-story-copy">
            <div class="landing-section-label">Why it feels different</div>
            <h2>Less feed, more working studio.</h2>
            <p>
                The landing page should signal the product honestly: Atelier is not trying to be a mass-market creator app. It is a place for artists who need structure, privacy, and room to work on their own terms.
            </p>
            <p>
                That means clearer pricing, quieter surfaces, stronger typography, and a visual language that feels deliberate instead of placeholder-heavy.
            </p>
        </div>

        <div class="landing-story-card">
            <div class="landing-section-label">At a glance</div>
            <ul class="landing-checklist">
                <li>Artist-first onboarding and profile setup</li>
                <li>Private commission threads instead of comment chaos</li>
                <li>Theme-aware UI that still reads cleanly by default</li>
                <li>Guest-safe calls to action that do not dump users into auth walls</li>
            </ul>
        </div>
    </section>

    <section class="container landing-cta">
        <div class="landing-cta-card">
            <div class="landing-section-label">Make the page earn the click</div>
            <h2>Set up a page that looks like a studio, not an apology.</h2>
            <p>
                Whether you are opening commissions or browsing for an artist, the front door should feel sharp, trustworthy, and specific.
            </p>
            <div class="landing-actions landing-actions-center">
                <x-themed-button href="{{ route('onboard') }}" variant="primary" size="lg">
                    Create your page
                </x-themed-button>

                @auth
                    <x-themed-button href="{{ route('browse') }}" variant="ghost" size="lg">
                        Explore artists
                    </x-themed-button>
                @else
                    <x-themed-button href="{{ route('login') }}" variant="ghost" size="lg">
                        Sign in
                    </x-themed-button>
                @endauth
            </div>
        </div>
    </section>
</div>
@endsection
