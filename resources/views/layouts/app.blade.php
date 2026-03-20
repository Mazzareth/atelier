<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Atelier — Your art. Your rules.</title>
    <script>
        // Load theme early to prevent flash
        (function() {
            try {
                var savedTheme = localStorage.getItem('atelier_theme');
                if (savedTheme && savedTheme !== 'default') {
                    document.documentElement.setAttribute('data-theme', savedTheme);
                }
            } catch(e) {}
        })();
    </script>
    <style>
        :root {
            --bg-color: #0f1210; --bg-panel: #161b18; --accent-color: #2bdc6c; --accent-dim: rgba(43, 220, 108, 0.15);
            --text-main: #f0f4f2; --text-muted: #8c9b92; --stripe-bg: #2bdc6c; --stripe-height: 4px;
            --particle-color: 43, 220, 108; --border-color: #232b26;
            transition: all 0.5s ease;
        }
        [data-theme="gay"] { --bg-color: #0f1012; --bg-panel: #16171b; --accent-color: #7329b3; --accent-dim: rgba(115, 41, 179, 0.15); --stripe-bg: linear-gradient(to bottom, #FF0018 16.6%, #FFA52C 16.6% 33.3%, #FFFF41 33.3% 50%, #008018 50% 66.6%, #0000F9 66.6% 83.3%, #86007D 83.3%); --stripe-height: 12px; --particle-color: 115, 41, 179; }
        [data-theme="trans"] { --bg-color: #0d1114; --bg-panel: #14191d; --accent-color: #55cdfc; --accent-dim: rgba(85, 205, 252, 0.15); --stripe-bg: linear-gradient(to bottom, #55cdfc 20%, #f7a8b8 20% 40%, #ffffff 40% 60%, #f7a8b8 60% 80%, #55cdfc 80%); --stripe-height: 12px; --particle-color: 85, 205, 252; }
        [data-theme="lesbian"] { --bg-color: #120e10; --bg-panel: #1b1518; --accent-color: #d62900; --accent-dim: rgba(214, 41, 0, 0.15); --stripe-bg: linear-gradient(to bottom, #d62900 20%, #ff9b55 20% 40%, #ffffff 40% 60%, #d461a6 60% 80%, #a40061 80%); --stripe-height: 12px; --particle-color: 214, 41, 0; }
        [data-theme="bi"] { --bg-color: #110e12; --bg-panel: #1a151b; --accent-color: #d60270; --accent-dim: rgba(214, 2, 112, 0.15); --stripe-bg: linear-gradient(to bottom, #d60270 40%, #9b4f96 40% 60%, #0038a8 60%); --stripe-height: 12px; --particle-color: 214, 2, 112; }
        [data-theme="nonbinary"] { --bg-color: #121210; --bg-panel: #1b1b15; --accent-color: #fcf434; --accent-dim: rgba(252, 244, 52, 0.15); --stripe-bg: linear-gradient(to bottom, #fcf434 25%, #ffffff 25% 50%, #9c59d1 50% 75%, #2c2c2c 75%); --stripe-height: 12px; --particle-color: 252, 244, 52; }
        [data-theme="pan"] { --bg-color: #120f11; --bg-panel: #1b1619; --accent-color: #ff218c; --accent-dim: rgba(255, 33, 140, 0.15); --stripe-bg: linear-gradient(to bottom, #ff218c 33.3%, #ffd800 33.3% 66.6%, #21b1ff 66.6%); --stripe-height: 12px; --particle-color: 255, 33, 140; }
        [data-theme="asexual"] { --bg-color: #111111; --bg-panel: #1a1a1a; --accent-color: #800080; --accent-dim: rgba(128, 0, 128, 0.15); --stripe-bg: linear-gradient(to bottom, #000000 25%, #a3a3a3 25% 50%, #ffffff 50% 75%, #800080 75%); --stripe-height: 12px; --particle-color: 128, 0, 128; }
        [data-theme="genderqueer"] { --bg-color: #101211; --bg-panel: #171b19; --accent-color: #b57edc; --accent-dim: rgba(181, 126, 220, 0.15); --stripe-bg: linear-gradient(to bottom, #b57edc 33.3%, #ffffff 33.3% 66.6%, #4a8123 66.6%); --stripe-height: 12px; --particle-color: 181, 126, 220; }
        [data-theme="intersex"] { --bg-color: #12110e; --bg-panel: #1b1a15; --accent-color: #ffd800; --accent-dim: rgba(255, 216, 0, 0.15); --stripe-bg: #ffd800; --stripe-height: 12px; --particle-color: 255, 216, 0; }
        
        [data-theme="dickgirl"] { --bg-color: #170d14; --bg-panel: #21131c; --accent-color: #e62e8a; --accent-dim: rgba(230, 46, 138, 0.15); --stripe-bg: linear-gradient(to right, #4a0033, #e62e8a, #4a0033); --stripe-height: 8px; --particle-color: 230, 46, 138; }
        [data-theme="femboy"] { --bg-color: #14171a; --bg-panel: #1b1e22; --accent-color: #f5a9b8; --accent-dim: rgba(245, 169, 184, 0.15); --stripe-bg: linear-gradient(to right, #5bcffa, #f5a9b8, #ffffff, #f5a9b8, #5bcffa); --stripe-height: 8px; --particle-color: 245, 169, 184; }
        [data-theme="dominant"] { --bg-color: #080808; --bg-panel: #111111; --accent-color: #cc0000; --accent-dim: rgba(204, 0, 0, 0.15); --stripe-bg: linear-gradient(to right, #111, #cc0000, #111); --stripe-height: 8px; --particle-color: 204, 0, 0; }
        [data-theme="submissive"] { --bg-color: #121216; --bg-panel: #1a1a20; --accent-color: #9d8ec7; --accent-dim: rgba(157, 142, 199, 0.15); --stripe-bg: linear-gradient(to right, #6a5acd, #9d8ec7, #e6e6fa); --stripe-height: 8px; --particle-color: 157, 142, 199; }
        [data-theme="musk"] { --bg-color: #10120a; --bg-panel: #171a0f; --accent-color: #b58d3d; --accent-dim: rgba(181, 141, 61, 0.15); --stripe-bg: linear-gradient(to right, #2c3618, #b58d3d, #4b3621); --stripe-height: 8px; --particle-color: 181, 141, 61; }
        [data-theme="pup"] { --bg-color: #0a0c12; --bg-panel: #12151c; --accent-color: #2b5cff; --accent-dim: rgba(43, 92, 255, 0.15); --stripe-bg: linear-gradient(to right, #000, #2b5cff, #fff, #2b5cff, #000); --stripe-height: 8px; --particle-color: 43, 92, 255; }

        /* Themed Extras! */
        .theme-vibe-container { position: fixed; inset: 0; pointer-events: none; z-index: -1; overflow: hidden; opacity: 0.6; }
        .theme-extra { display: none; position: absolute; font-size: 2.5rem; filter: drop-shadow(0 0 10px var(--accent-dim)); }
        
        @keyframes drift { 
            0% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(15px, -30px) rotate(5deg); }
            50% { transform: translate(-10px, -60px) rotate(-5deg); }
            75% { transform: translate(-20px, -30px) rotate(3deg); }
            100% { transform: translate(0, 0) rotate(0deg); }
        }
        
        /* Standardized Vibe Positioning */
        .vibe-1 { top: 15%; left: 8%; animation: drift 10s ease-in-out infinite; }
        .vibe-2 { top: 25%; right: 10%; animation: drift 12s ease-in-out infinite reverse; }
        .vibe-3 { bottom: 20%; left: 12%; animation: drift 14s ease-in-out infinite; }
        .vibe-4 { bottom: 15%; right: 8%; animation: drift 11s ease-in-out infinite alternate; }

        /* Pride Themes */
        [data-theme="gay"] .gay-rainbow { display: block; }
        [data-theme="trans"] .blahaj-shonk { display: block; }
        [data-theme="lesbian"] .lesbian-butterflies { display: block; }
        [data-theme="bi"] .bi-frog { display: block; }
        [data-theme="nonbinary"] .enby-bee { display: block; }
        [data-theme="pan"] .pan-pancake { display: block; }
        [data-theme="asexual"] .ace-cake { display: block; }
        [data-theme="genderqueer"] .gq-crystal { display: block; }
        [data-theme="intersex"] .intersex-flower { display: block; }

        /* Vibes Themes */
        [data-theme="dickgirl"] .kiss-mark { display: block; }
        [data-theme="dickgirl"] .lipstick { display: block; }
        [data-theme="femboy"] .femboy-bow { display: block; }
        [data-theme="femboy"] .femboy-flower { display: block; }
        [data-theme="dominant"] .dom-chain { display: block; }
        [data-theme="dominant"] .dom-crown { display: block; }
        [data-theme="submissive"] .sub-bell { display: block; }
        [data-theme="submissive"] .sub-plead { display: block; }
        [data-theme="musk"] .musk-swirl { display: block; }
        [data-theme="musk"] .musk-dash { display: block; }
        [data-theme="pup"] .pup-paw { display: block; }
        [data-theme="pup"] .pup-bone { display: block; }

        /* Wavy Default Background */
        body:not([data-theme]) .wavy-divider {
            display: block; width: 100%; height: 50px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23161b18' fill-opacity='1' d='M0,128L48,144C96,160,192,192,288,181.3C384,171,480,117,576,112C672,107,768,149,864,165.3C960,181,1056,171,1152,149.3C1248,128,1344,96,1392,80L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover; background-repeat: no-repeat; bottom: -1px; left: 0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; transition: background-color 0.5s ease, color 0.5s ease, border-color 0.5s ease, fill 0.5s ease; }
        body { background-color: var(--bg-color); color: var(--text-main); font-family: 'Inter', -apple-system, sans-serif; overflow-x: hidden; display: flex; flex-direction: column; min-height: 100vh; }
        body.chat-route { height: 100vh; overflow: hidden; }
        .serif { font-family: 'Georgia', serif; font-style: italic; }
        .mono { font-family: 'Courier New', monospace; letter-spacing: 0.05em; }

        #flag-stripe { width: 100%; height: var(--stripe-height); background: var(--stripe-bg); transition: height 0.5s ease, background 0.5s ease; position: relative; z-index: 100; }
        
        /* Themed Extras Animation Classes */
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-15px); } 100% { transform: translateY(0px); } }
        @keyframes pulse-slow { 0%, 100% { transform: scale(1); opacity: 0.8; } 50% { transform: scale(1.1); opacity: 1; } }
        @keyframes wobble { 0%, 100% { transform: rotate(-10deg); } 50% { transform: rotate(10deg); } }
        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-25px); } }

        /* Common CSS continued */
        nav { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1rem 2rem; background-color: var(--bg-panel); border-bottom: 1px solid var(--border-color); position: sticky; top: 0; z-index: 1000; }
        .logo { display: flex; align-items: center; gap: 0.5rem; font-size: 1.2rem; flex-shrink: 0; }
        .logo-circle { width: 24px; height: 24px; border-radius: 50%; background-color: var(--accent-color); color: var(--bg-color); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: bold; font-style: normal; }
        .app-nav-shell { display: grid; grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr); align-items: center; gap: 1rem; flex: 1; }
        .app-nav-shell--commissioner { grid-template-columns: minmax(0, 1fr) auto; }
        .app-nav-left { justify-content: flex-start; }
        .app-nav-left--commissioner { gap: 0.55rem; flex-wrap: wrap; }
        .app-nav-center { display: flex; justify-content: center; }
        .app-nav-right { display: flex; justify-content: flex-end; align-items: center; gap: 0.75rem; }
        .nav-links { display: flex; gap: 1.25rem; position: relative; align-items: center; }
        .nav-links a { color: var(--text-muted); text-decoration: none; font-size: 0.8rem; text-transform: uppercase; }
        .nav-links a:hover { color: var(--text-main); }
        .nav-quick-link { display: inline-flex; align-items: center; justify-content: center; min-height: 2.35rem; padding: 0.55rem 0.9rem; border-radius: 999px; border: 1px solid var(--border-color); background: color-mix(in srgb, var(--bg-color) 52%, var(--bg-panel)); font-size: 0.72rem !important; letter-spacing: 0.08em; }
        .nav-quick-link.is-active { color: var(--text-main); border-color: color-mix(in srgb, var(--accent-color) 48%, var(--border-color)); background: color-mix(in srgb, var(--accent-color) 13%, transparent); box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--accent-color) 18%, transparent); }
        .nav-menu-dropdown { position: relative; }
        .nav-menu-dropdown summary { list-style: none; }
        .nav-menu-dropdown summary::-webkit-details-marker { display: none; }
        .nav-menu-trigger { cursor: pointer; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; }
        .nav-menu-trigger:hover { color: var(--text-main); }
        .nav-menu-panel { position: absolute; top: calc(100% + 0.65rem); left: 0; min-width: 260px; display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; padding: 0.9rem; border-radius: 18px; border: 1px solid color-mix(in srgb, var(--accent-color) 16%, var(--border-color)); background: linear-gradient(180deg, color-mix(in srgb, var(--bg-panel) 96%, transparent), color-mix(in srgb, var(--bg-color) 18%, var(--bg-panel))); box-shadow: 0 20px 44px rgba(0,0,0,0.24); }
        .nav-menu-group { display: flex; flex-direction: column; gap: 0.45rem; min-width: 0; }
        .nav-menu-label { font-size: 0.62rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.09em; padding: 0 0.2rem 0.2rem; }
        .nav-menu-link { color: var(--text-main) !important; text-decoration: none; font-size: 0.72rem !important; text-transform: uppercase; padding: 0.7rem 0.8rem; border-radius: 12px; border: 1px solid var(--border-color); background: color-mix(in srgb, var(--bg-color) 34%, var(--bg-panel)); }
        .nav-menu-link:hover { border-color: color-mix(in srgb, var(--accent-color) 35%, var(--border-color)); }
        .free-badge { position: absolute; top: -10px; right: -10px; color: var(--accent-color); font-size: 0.6rem; transform: rotate(15deg); }
        .sign-in { background: transparent; border: 1px solid var(--border-color); color: var(--text-main); padding: 0.55rem 1rem; border-radius: 999px; cursor: pointer; font-size: 0.8rem; }
        .sign-in:hover { border-color: var(--accent-color); }
        .nav-chat-link { white-space: nowrap; }
        .nav-profile-menu { position: relative; }
        .nav-profile-menu summary { list-style: none; }
        .nav-profile-menu summary::-webkit-details-marker { display: none; }
        .nav-profile-trigger { display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.3rem; border-radius: 999px; border: 1px solid var(--border-color); background: color-mix(in srgb, var(--bg-color) 55%, var(--bg-panel)); }
        .nav-profile-badge { font-size: 0.78rem; font-weight: bold; color: var(--accent-color); background: var(--accent-dim); border: 1px solid color-mix(in srgb, var(--accent-color) 48%, var(--border-color)); padding: 0.45rem 0.8rem; border-radius: 999px; }
        .nav-profile-caret { color: var(--text-muted); padding-right: 0.25rem; }
        .nav-profile-dropdown { position: absolute; top: calc(100% + 0.55rem); right: 0; min-width: 240px; display: flex; flex-direction: column; gap: 0.55rem; padding: 0.85rem; border-radius: 18px; border: 1px solid color-mix(in srgb, var(--accent-color) 16%, var(--border-color)); background: linear-gradient(180deg, color-mix(in srgb, var(--bg-panel) 96%, transparent), color-mix(in srgb, var(--bg-color) 18%, var(--bg-panel))); box-shadow: 0 20px 44px rgba(0,0,0,0.24); }
        .nav-profile-item { color: var(--text-main); text-decoration: none; font-size: 0.72rem; text-transform: uppercase; padding: 0.65rem 0.75rem; border-radius: 12px; border: 1px solid var(--border-color); background: color-mix(in srgb, var(--bg-color) 34%, var(--bg-panel)); }
        .nav-profile-item:hover { border-color: color-mix(in srgb, var(--accent-color) 35%, var(--border-color)); }
        .nav-profile-item-button { width: 100%; text-align: left; }
        .nav-profile-label { font-size: 0.64rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.08em; padding: 0 0.2rem; }
        .nav-theme-select { width: 100%; font-size: 0.7rem; }

        .theme-select { background: var(--bg-panel); color: var(--text-main); border: 1px solid var(--border-color); padding: 0.4rem 0.8rem; border-radius: 12px; outline: none; cursor: pointer; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; appearance: none; padding-right: 2rem; }
        .theme-select:focus { border-color: var(--accent-color); }
        .theme-select optgroup { background: var(--bg-panel); color: var(--text-muted); font-weight: bold; }
        .theme-select option { background: var(--bg-panel); color: var(--text-main); }

        .hero { display: flex; min-height: 60vh; border-bottom: 1px solid var(--border-color); position: relative; }
        .hero-left { flex: 1; padding: 4rem; display: flex; flex-direction: column; justify-content: center; position: relative; }
        .pill { display: inline-flex; align-items: center; gap: 0.5rem; background: var(--accent-dim); color: var(--accent-color); padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.8rem; width: fit-content; margin-bottom: 2rem; border: 1px solid var(--accent-color); }
        .dot { width: 6px; height: 6px; background: var(--accent-color); border-radius: 50%; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.5); opacity: 0.5; } 100% { transform: scale(1); opacity: 1; } }
        h1 { font-size: 4rem; line-height: 1.1; margin-bottom: 1.5rem; font-weight: normal; z-index: 10; position: relative;}
        h1 span.light { color: var(--text-main); }
        h1 span.dim { color: var(--text-muted); }
        h1 span.highlight { color: var(--accent-color); }
        .hero-text { color: var(--text-muted); font-size: 1.2rem; max-width: 400px; margin-bottom: 2.5rem; line-height: 1.5; z-index: 10; position: relative;}
        .btn-group { display: flex; gap: 1rem; z-index: 10; position: relative;}
        .btn { padding: 0.8rem 1.5rem; border-radius: 4px; cursor: pointer; font-size: 0.9rem; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; }
        .btn-primary { background: var(--accent-color); color: #000; border: none; font-weight: bold; }
        .btn-primary:hover .arrow { transform: translateX(4px); }
        .arrow { transition: transform 0.2s; display: inline-block; }
        .btn-ghost { background: transparent; border: 1px solid var(--text-muted); color: var(--text-main); }
        .btn-ghost:hover { border-color: var(--text-main); }
        .hero-right { flex: 1; background-color: var(--bg-panel); position: relative; overflow: hidden; border-left: 1px solid var(--border-color); }
        .hero-right::before { content: ''; position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 20px 20px; z-index: 1; }
        .hero-right::after { content: ''; position: absolute; inset: 0; background: radial-gradient(circle, transparent 20%, var(--bg-panel) 90%); z-index: 2; }
        canvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; }
        .canvas-label { position: absolute; bottom: 2rem; left: 2rem; z-index: 10; }
        .canvas-label-title { font-size: 0.7rem; color: var(--accent-color); margin-bottom: 0.5rem; display: block; }
        .canvas-quote { color: var(--text-muted); font-size: 0.9rem; opacity: 0.7; }
        .ticker-wrap { width: 100%; overflow: hidden; background: var(--bg-color); border-bottom: 1px solid var(--border-color); padding: 0.8rem 0; white-space: nowrap; }
        .ticker-content { display: inline-block; animation: ticker 30s linear infinite; font-size: 0.7rem; color: var(--text-muted); }
        @keyframes ticker { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .ticker-item { display: inline-flex; align-items: center; margin-right: 2rem; }
        .ticker-diamond { color: var(--accent-color); margin-left: 2rem; font-size: 0.6rem; }
        .pillars { display: flex; background: var(--bg-color); border-bottom: 1px solid var(--border-color); }
        .pillar { flex: 1; padding: 3rem 2rem; border-right: 1px solid var(--border-color); position: relative; }
        .pillar:last-child { border-right: none; }
        .pillar:hover { background-color: var(--bg-panel); }
        .pillar-dot { width: 6px; height: 6px; background: var(--accent-color); border-radius: 50%; position: absolute; top: 1.5rem; left: 1.5rem; }
        .pillar-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; margin-top: 1rem; }
        .pillar-num { font-size: 0.8rem; color: var(--text-muted); }
        .pillar-title { font-size: 1.1rem; color: var(--text-main); }
        .pillar-text { color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; }
        
        /* Human Translation Styles */
        .human-translation { margin-top: 1.2rem; font-size: 0.65rem; color: var(--accent-color); line-height: 1.4; border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 0.8rem; text-transform: uppercase; }
        .human-translation span { color: var(--text-muted); font-family: 'Inter', -apple-system, sans-serif; letter-spacing: 0; text-transform: none; font-size: 0.8rem; display: block; margin-top: 0.3rem; }

        .manifesto { padding: 6rem 4rem; background: var(--bg-panel); position: relative; overflow: hidden; display: flex; gap: 4rem; }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 20vw; color: rgba(255,255,255,0.02); z-index: 0; pointer-events: none; font-family: 'Georgia', serif; }
        .manifesto-left { flex: 1; z-index: 1; }
        .manifesto-label { font-size: 0.7rem; color: var(--text-muted); margin-bottom: 2rem; display: block; }
        .manifesto-text { font-size: 1.5rem; line-height: 1.6; color: var(--text-muted); }
        .manifesto-text span { color: var(--accent-color); }
        .manifesto-right { flex: 1; z-index: 1; display: flex; flex-direction: column; gap: 2rem; justify-content: center; }
        .stat { padding-left: 1.5rem; border-left: 4px solid var(--accent-color); }
        .stat-num { font-size: 2rem; color: var(--text-main); margin-bottom: 0.2rem; }
        .stat-desc { color: var(--text-muted); font-size: 0.9rem; }
        
        /* The Switcher */
        .mode-switcher {
            display: flex;
            align-items: stretch;
            background: linear-gradient(180deg, color-mix(in srgb, var(--bg-color) 65%, var(--bg-panel)), var(--bg-panel));
            border: 1px solid color-mix(in srgb, var(--accent-color) 18%, var(--border-color));
            border-radius: 18px;
            padding: 4px;
            position: relative;
            overflow: hidden;
            width: max-content;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.04), 0 8px 18px rgba(0,0,0,0.16);
            backdrop-filter: blur(8px);
        }
        .mode-switcher::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, color-mix(in srgb, var(--accent-color) 8%, transparent), transparent 55%);
            pointer-events: none;
            opacity: 0.9;
        }
        .mode-slider {
            position: absolute;
            top: 4px; bottom: 4px; left: 4px;
            width: calc((100% - 8px) / var(--modes, 2));
            background: linear-gradient(180deg, color-mix(in srgb, var(--accent-color) 26%, #ffffff 6%), color-mix(in srgb, var(--accent-color) 12%, transparent));
            border: 1px solid color-mix(in srgb, var(--accent-color) 50%, var(--border-color));
            border-radius: 14px;
            box-shadow: 0 8px 18px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.16);
            transition: transform 0.52s cubic-bezier(0.16, 1, 0.3, 1), background 0.3s ease, box-shadow 0.3s ease, filter 0.3s ease;
            z-index: 1;
            pointer-events: none;
            transform-origin: center center;
        }
        .mode-switcher.is-switching .mode-slider {
            box-shadow: 0 12px 24px rgba(0,0,0,0.2), 0 0 0 1px color-mix(in srgb, var(--accent-color) 16%, transparent), inset 0 1px 0 rgba(255,255,255,0.2);
            filter: saturate(1.12) brightness(1.04);
            animation: mode-slider-pop 380ms cubic-bezier(0.16, 1, 0.3, 1);
        }
        .mode-btn {
            flex: 1;
            min-width: 92px;
            padding: 0.5rem 0.72rem;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 0.72rem;
            cursor: pointer;
            z-index: 2;
            position: relative;
            text-align: left;
            transition: color 0.25s ease, transform 0.22s ease, opacity 0.2s ease, filter 0.22s ease;
            display: flex;
            align-items: center;
            gap: 0.55rem;
            border-radius: 14px;
            overflow: hidden;
            isolation: isolate;
        }
        .mode-btn::after {
            content: '';
            position: absolute;
            inset: -20%;
            background: linear-gradient(115deg, transparent 28%, rgba(255,255,255,0.22) 46%, transparent 64%);
            transform: translateX(-140%) skewX(-12deg);
            opacity: 0;
            pointer-events: none;
            z-index: -1;
        }
        .mode-btn:hover:not(.active) {
            color: var(--text-main);
            transform: translateY(-1px) scale(1.01);
            filter: brightness(1.03);
        }
        .mode-btn:active {
            transform: translateY(1px) scale(0.97);
        }
        .mode-btn.is-clicked::after {
            opacity: 1;
            animation: mode-btn-sheen 420ms cubic-bezier(0.22, 1, 0.36, 1);
        }
        .mode-btn.is-clicked .mode-btn-icon {
            animation: mode-icon-flip 380ms cubic-bezier(0.2, 0.9, 0.2, 1);
        }
        .mode-btn.active {
            color: var(--text-main);
            font-weight: bold;
        }
        .mode-btn-icon {
            width: 1.45rem;
            height: 1.45rem;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: color-mix(in srgb, var(--bg-panel) 85%, transparent);
            border: 1px solid color-mix(in srgb, var(--accent-color) 20%, var(--border-color));
            color: var(--accent-color);
            font-size: 0.75rem;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.05);
            flex-shrink: 0;
        }
        .mode-btn.active .mode-btn-icon {
            background: color-mix(in srgb, var(--accent-color) 18%, rgba(255,255,255,0.04));
            border-color: color-mix(in srgb, var(--accent-color) 48%, var(--border-color));
            color: var(--text-main);
        }
        @keyframes mode-slider-pop {
            0% { transform: var(--mode-slider-transform, translateX(0)) scaleX(0.98) scaleY(0.94); }
            45% { transform: var(--mode-slider-transform, translateX(0)) scaleX(1.035) scaleY(1.02); }
            100% { transform: var(--mode-slider-transform, translateX(0)) scaleX(1) scaleY(1); }
        }
        @keyframes mode-btn-sheen {
            0% { transform: translateX(-140%) skewX(-12deg); opacity: 0; }
            18% { opacity: 1; }
            100% { transform: translateX(135%) skewX(-12deg); opacity: 0; }
        }
        @keyframes mode-icon-flip {
            0% { transform: rotateY(0deg) scale(1); }
            50% { transform: rotateY(180deg) scale(1.14); }
            100% { transform: rotateY(360deg) scale(1); }
        }
        .mode-btn-text-wrap {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }
        .mode-btn-label {
            font-size: 0.7rem;
            line-height: 1;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .mode-form { margin: 0; flex: 1; display: flex; align-items: stretch; justify-content: center; }

        @media (max-width: 1100px) {
            .mode-btn {
                min-width: 82px;
                padding: 0.46rem 0.62rem;
            }
        }

        @media (max-width: 780px) {
            .mode-switcher {
                width: 100%;
            }

            .mode-btn {
                min-width: 0;
                justify-content: center;
                text-align: center;
                padding: 0.72rem 0.7rem;
            }

            .mode-btn-text-wrap {
                align-items: center;
            }
        }

        /* Toast Notifications */
        .toast-notification {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: var(--bg-panel);
            border-left: 4px solid var(--accent-color);
            color: var(--text-main);
            padding: 1rem 1.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
            z-index: 9999;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            transform: translateY(150%);
            opacity: 0;
            transition: transform 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55), opacity 0.5s ease;
        }
        .toast-notification.show {
            transform: translateY(0);
            opacity: 1;
        }
        .toast-notification span.diamond {
            color: var(--accent-color);
            margin-right: 0.5rem;
        }

        footer { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; background: var(--bg-color); border-top: 1px solid var(--border-color); font-size: 0.7rem; color: var(--text-muted); }
        .footer-links { display: flex; gap: 1.5rem; }
        .footer-links a { color: var(--text-muted); text-decoration: none; }
        .footer-links a:hover { color: var(--accent-color); }
        @media (max-width: 1100px) {
            .app-nav-shell { grid-template-columns: 1fr; }
            .app-nav-left, .app-nav-center, .app-nav-right { justify-content: flex-start; }
            nav { align-items: flex-start; }
        }
        @media (max-width: 900px) { .hero, .pillars, .manifesto { flex-direction: column; } .hero-right { min-height: 300px; border-left: none; border-top: 1px solid var(--border-color); } .pillar { border-right: none; border-bottom: 1px solid var(--border-color); } }
        @media (max-width: 720px) {
            nav { padding: 0.9rem 1rem; }
            .app-nav-right { flex-wrap: wrap; }
            .nav-profile-dropdown { left: 0; right: auto; min-width: min(280px, calc(100vw - 2rem)); }
            .nav-menu-panel { grid-template-columns: 1fr; min-width: min(280px, calc(100vw - 2rem)); }
        }
    </style>
</head>
<body class="{{ request()->routeIs('conversations.*') ? 'chat-route' : '' }}">
    @include('partials.theme-vibes')
    <div id="flag-stripe"></div>
    @include('partials.nav')
    
    @yield('content')

    @unless(request()->routeIs('conversations.*'))
    <footer>
        <div class="mono">© 2025 atelier — made by artists</div>
        <div class="footer-links mono">
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <a href="#">Contact</a>
        </div>
    </footer>
    @endunless

    @stack('scripts')
    
    @if(session('status'))
    <div id="toast-notification" class="toast-notification mono">
        <span class="diamond">◆</span>{{ session('status') }}
    </div>
    @endif
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeSelector = document.getElementById('theme-selector');
            if (themeSelector) {
                const currentTheme = localStorage.getItem('atelier_theme') || 'default';
                themeSelector.value = currentTheme;
                if(currentTheme !== 'default') {
                    document.documentElement.setAttribute('data-theme', currentTheme);
                }

                themeSelector.addEventListener('change', (e) => {
                    const theme = e.target.value;
                    localStorage.setItem('atelier_theme', theme);
                    if(theme === 'default') {
                        document.documentElement.removeAttribute('data-theme');
                    } else {
                        document.documentElement.setAttribute('data-theme', theme);
                    }
                });
            }

            // Toast Logic
            const toast = document.getElementById('toast-notification');
            if (toast) {
                setTimeout(() => {
                    toast.classList.add('show');
                }, 100);
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 4000);
            }

            const drawer = document.getElementById('requests-drawer');
            const drawerList = document.getElementById('requests-drawer-list');
            const drawerToggle = document.getElementById('requests-drawer-toggle');
            const drawerClose = document.getElementById('requests-drawer-close');
            const drawerBackdrop = document.getElementById('requests-drawer-backdrop');
            const unreadBadge = document.getElementById('requests-unread-badge');
            const toastStack = document.getElementById('toast-stack');
            const modeSwitcher = document.querySelector('.mode-switcher');
            let lastUnreadCount = 0;
            let lastTopSignature = null;

            function openRequestsDrawer() {
                if (!drawer || !drawerBackdrop) return;
                drawer.style.right = '0';
                drawerBackdrop.style.opacity = '1';
                drawerBackdrop.style.pointerEvents = 'auto';
            }

            function closeRequestsDrawer() {
                if (!drawer || !drawerBackdrop) return;
                drawer.style.right = '-420px';
                drawerBackdrop.style.opacity = '0';
                drawerBackdrop.style.pointerEvents = 'none';
            }

            if (modeSwitcher) {
                const modeSlider = modeSwitcher.querySelector('.mode-slider');
                const modeButtons = Array.from(modeSwitcher.querySelectorAll('.mode-btn'));
                const modeMap = {
                    commissioner: '0%',
                    artist: '100%',
                    admin: '200%'
                };

                modeButtons.forEach((button) => {
                    button.addEventListener('click', (event) => {
                        if (button.classList.contains('active')) return;
                        event.preventDefault();

                        const form = button.closest('form');
                        const target = button.dataset.modeTarget;
                        const transformValue = modeMap[target] || '0%';

                        modeButtons.forEach((btn) => btn.classList.remove('is-clicked'));
                        button.classList.add('is-clicked');
                        modeSwitcher.classList.add('is-switching');

                        if (modeSlider) {
                            const transform = `translateX(${transformValue})`;
                            modeSlider.style.transform = transform;
                            modeSlider.style.setProperty('--mode-slider-transform', transform);
                        }

                        window.setTimeout(() => {
                            form?.submit();
                        }, 180);
                    });
                });
            }

            function pushRequestToast(title, body) {
                if (!toastStack) return;
                const el = document.createElement('div');
                el.className = 'requests-toast';
                el.innerHTML = `<div class="mono" style="font-size:0.68rem; color:var(--accent-color); text-transform:uppercase; margin-bottom:0.35rem;">New request activity</div><div style="font-weight:600; margin-bottom:0.25rem;">${title}</div><div style="font-size:0.9rem; color:var(--text-muted); line-height:1.5;">${body}</div>`;
                toastStack.appendChild(el);
                setTimeout(() => el.remove(), 4500);
            }

            function renderRequestDrawerItems(items) {
                if (!drawerList) return;
                if (!items.length) {
                    drawerList.innerHTML = '<div class="mono" style="color:var(--text-muted); font-size:0.78rem;">No request threads yet.</div>';
                    return;
                }

                drawerList.innerHTML = items.map(item => `
                    <a href="${item.url}" style="text-decoration:none; color:inherit; background: var(--bg-color); border:1px solid var(--border-color); border-radius:14px; padding:0.95rem; display:flex; gap:0.85rem; justify-content:space-between; align-items:flex-start;">
                        <div style="min-width:0; flex:1;">
                            <div class="mono" style="font-size:0.65rem; color:var(--text-muted); text-transform:uppercase; margin-bottom:0.3rem;">${item.otherParty.username ? '/' + item.otherParty.username : 'request thread'} • ${String(item.status).replace('_', ' ')}</div>
                            <div style="font-weight:600; margin-bottom:0.25rem;">${item.title}</div>
                            <div style="font-size:0.85rem; color:var(--text-muted); line-height:1.5; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">${item.latestMessage ? item.latestMessage.message : 'No messages yet.'}</div>
                        </div>
                        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:0.4rem;">
                            ${item.unread > 0 ? `<span class="mono" style="min-width:1.25rem; height:1.25rem; padding:0 0.35rem; border-radius:999px; background:var(--accent-color); color:#000; font-size:0.65rem; font-weight:bold; line-height:1.25rem; text-align:center;">${item.unread}</span>` : ''}
                            <span class="mono" style="font-size:0.65rem; color:var(--text-muted); white-space:nowrap;">${item.updatedHuman}</span>
                        </div>
                    </a>
                `).join('');
            }

            async function refreshCommissionSummary() {
                if (!drawerList || !unreadBadge) return;
                try {
                    const res = await fetch('{{ route('conversations.notifications.summary') }}', { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const data = await res.json();
                    renderRequestDrawerItems(data.items || []);

                    const totalUnread = Number(data.totalUnread || 0);
                    unreadBadge.style.display = totalUnread > 0 ? 'inline-block' : 'none';
                    unreadBadge.textContent = totalUnread > 99 ? '99+' : String(totalUnread);

                    const top = (data.items || []).find(item => Number(item.unread) > 0) || data.items?.[0] || null;
                    const topSignature = top ? `${top.id}:${top.latestMessage?.createdHuman || ''}:${top.unread}` : null;
                    if (top && totalUnread > lastUnreadCount && topSignature !== lastTopSignature) {
                        pushRequestToast(top.title, top.latestMessage?.message || 'New activity in a commission thread.');
                    }

                    lastUnreadCount = totalUnread;
                    lastTopSignature = topSignature;
                } catch (e) {
                    console.error(e);
                }
            }

            if (drawerToggle) drawerToggle.addEventListener('click', async () => { openRequestsDrawer(); await refreshCommissionSummary(); });
            if (drawerClose) drawerClose.addEventListener('click', closeRequestsDrawer);
            if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeRequestsDrawer);
            if (drawerList && unreadBadge) {
                refreshCommissionSummary();
                setInterval(refreshCommissionSummary, 15000);
            }
        });
    </script>
</body>
</html>
