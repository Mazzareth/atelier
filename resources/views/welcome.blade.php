@extends('layouts.app')

@section('content')
<header class="hero">
    <div class="hero-left">
        <div class="theme-extra wavy-divider"></div>
        <div class="pill mono">
            <div class="dot"></div>
            ● open to all
        </div>
        <h1 class="serif">
            <span class="light">Your art.</span><br>
            <span class="dim">Your rules.</span><br>
            <span class="highlight">Your community.</span>
        </h1>
        <p class="hero-text serif">
            A commission platform built by artists, for artists — furry, NSFW, niche, weird, wonderful. All of it. No shadowbanning. Just a calm, artist-first space where you can actually connect.
        </p>
        <div class="btn-group">
            <a href="{{ route('onboard') }}" class="btn btn-primary">Open your atelier <span class="arrow">→</span></a>
            <a href="{{ route('browse') }}" class="btn btn-ghost">Find an artist</a>
        </div>
        <div class="hero-social-proof mono" style="margin-top: 1rem; font-size: 0.85rem; opacity: 0.8;">
            <span style="color: var(--highlight-color, #2bdc6c);">★</span> Join 2,400+ artists who stopped worrying about the algorithm.
        </div>
    </div>
    <div class="hero-right">
        <canvas id="particle-canvas"></canvas>
        <div class="canvas-label">
            <span class="canvas-label-title mono">THE STUDIO WALL</span>
            <span class="canvas-quote serif">"Draw your OC without wondering if today's the day you get banned."</span>
        </div>
    </div>
</header>

<div class="ticker-wrap">
    <div class="ticker-content mono" id="ticker-content"></div>
</div>

<section class="pillars">
    <div class="pillar">
        <div class="pillar-dot"></div>
        <div class="pillar-header"><span class="pillar-num mono">01</span>🦊</div>
        <h3 class="pillar-title">Furry-friendly by design</h3>
        <p class="pillar-text">Anthro art is celebrated here, not tolerated. Draw your OC without wondering if today's the day you get banned.</p>
        <div class="human-translation mono">↳ Human translation: <span>We actually like furries and NSFW. Draw what you want without fear.</span></div>
    </div>
    <div class="pillar">
        <div class="pillar-dot"></div>
        <div class="pillar-header"><span class="pillar-num mono">02</span>🔒</div>
        <h3 class="pillar-title">Private commissions, zero drama</h3>
        <p class="pillar-text">Commissioners see your work, chat directly, and pay through clean escrow. No middlemen, no algorithms deciding who gets exposure.</p>
        <div class="human-translation mono">↳ Human translation: <span>Clients pay upfront into a safe hold. You draw, you get paid. No scams.</span></div>
    </div>
    <div class="pillar">
        <div class="pillar-dot"></div>
        <div class="pillar-header"><span class="pillar-num mono">03</span>💛</div>
        <h3 class="pillar-title">0% platform cuts on your art</h3>
        <p class="pillar-text">You keep exactly what you earn. Your rates, your queue, your schedule. We handle the dirty payment work so you can just focus on drawing.</p>
        <div class="human-translation mono">↳ Human translation: <span>We aren't greedy middlemen taking a 20% cut. It's your money.</span></div>
    </div>
</section>

<section class="manifesto">
    <div class="watermark">atelier</div>
    <div class="manifesto-left">
        <span class="manifesto-label mono">OUR PROMISE</span>
        <p class="manifesto-text serif">
            We built this because <span>artists deserve better</span> than platforms that treat their work as liability. Every niche. Every style. Every community. <span>No exceptions.</span> This is a space that was built to last — by artists, owned by no one with a VC cheque.
        </p>
        <div class="human-translation mono" style="margin-top: 2rem; border-top: none;">↳ Human translation: <span>Corporate platforms hate us. We built our own so the suits can't touch us.</span></div>
    </div>
    <div class="manifesto-right">
        <div class="stat"><div class="stat-num serif">2,400+</div><div class="stat-desc">artists with open queues</div></div>
        <div class="stat"><div class="stat-num serif">Zero</div><div class="stat-desc">bans for drawing furries</div></div>
        <div class="stat"><div class="stat-num serif">100%</div><div class="stat-desc">artist run, no VC overlords</div></div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Ticker population
        const appUsers = {!! isset($users) && count($users) > 0 ? json_encode($users) : '[]' !!};
        const tickerItems = appUsers.length > 0 
            ? appUsers.map(u => '@' + u)
            : [
                "artist-first platform", "zero bans for furries", "no algorithms",
                "100% artist run", "no VC overlords", "your art your rules",
                "escrow payments", "open commissions", "weird & wonderful"
            ];
        
        const tickerContent = document.getElementById('ticker-content');
        if (tickerContent) {
            let tickerHTML = '';
            for(let i=0; i<4; i++) {
                tickerItems.forEach(item => {
                    tickerHTML += `<span class="ticker-item">${item} <span class="ticker-diamond">◆</span></span>`;
                });
            }
            tickerContent.innerHTML = tickerHTML;
        }

        // Canvas Particles
        const canvas = document.getElementById('particle-canvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            let particles = [];
            let w, h;

            function resize() {
                w = canvas.width = canvas.parentElement.clientWidth;
                h = canvas.height = canvas.parentElement.clientHeight;
            }
            window.addEventListener('resize', resize);
            resize();

            class Particle {
                constructor() { this.reset(); this.y = Math.random() * h; }
                reset() {
                    this.x = Math.random() * w; this.y = -10;
                    this.size = Math.random() * 2 + 1;
                    this.speedY = Math.random() * 0.5 + 0.2;
                    this.opacity = Math.random() * 0.5 + 0.1;
                    this.isSquare = Math.random() > 0.7;
                }
                update() {
                    this.y += this.speedY;
                    if(this.y > h + 10) this.reset();
                }
                draw() {
                    const colorStr = getComputedStyle(document.body).getPropertyValue('--particle-color').trim() || '43, 220, 108';
                    ctx.fillStyle = `rgba(${colorStr}, ${this.opacity})`;
                    ctx.beginPath();
                    if(this.isSquare) {
                        ctx.rect(this.x, this.y, this.size*1.5, this.size*1.5);
                    } else {
                        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    }
                    ctx.fill();
                }
            }

            for(let i = 0; i < 100; i++) particles.push(new Particle());
            function animate() {
                ctx.clearRect(0, 0, w, h);
                particles.forEach(p => { p.update(); p.draw(); });
                requestAnimationFrame(animate);
            }
            animate();
        }
    });
</script>
@endpush
