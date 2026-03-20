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
            --bg-color: #0f1210; --bg-panel: #161b18; --accent-color: #2bdc6c; --accent-color-rgb: 43, 220, 108; --accent-dim: rgba(43, 220, 108, 0.15);
            --text-main: #f0f4f2; --text-muted: #8c9b92; --stripe-bg: #2bdc6c; --stripe-height: 4px; --stripe-anim: none;
            --particle-color: 43, 220, 108; --border-color: #232b26;
            --radius-base: 8px; --radius-card: 12px; --radius-btn: 999px;
            --font-body: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; --font-display: inherit; --font-mono: "Courier New", monospace; --font-weight-heading: 600; --letter-spacing: 0;
            --shadow-card: 0 4px 12px rgba(0,0,0,0.1); --shadow-glow: 0 0 20px rgba(var(--accent-color-rgb), 0.3);
            --border-width: 1px; --border-style: solid;
            --noise-opacity: 0; --texture-url: none;
            --anim-speed: 1; --anim-float-style: float;
            --panel-bg: var(--bg-panel); --glass-opacity: 0;
            transition: all calc(0.5s * var(--anim-speed)) ease;
        }
        [data-theme="gay"] { --bg-color: #130f18; --bg-panel: #1c1523; --accent-color: #7d41d9; --accent-color-rgb: 125, 65, 217; --accent-dim: rgba(125, 65, 217, 0.18); --text-main: #f7f3ff; --text-muted: #bfb0d8; --border-color: #34264a; --stripe-bg: linear-gradient(90deg, #ff0018 0 16.6%, #ffa52c 16.6% 33.3%, #ffff41 33.3% 50%, #008018 50% 66.6%, #0000f9 66.6% 83.3%, #86007d 83.3%); --stripe-height: 14px; --stripe-anim: shift; --particle-color: 125, 65, 217; --radius-base: 10px; --radius-card: 18px; --radius-btn: 999px; --font-display: "Trebuchet MS", "Avenir Next", sans-serif; --font-weight-heading: 700; --letter-spacing: 0.08em; --shadow-card: 0 18px 42px rgba(20, 6, 40, 0.4); --shadow-glow: 0 0 28px rgba(125, 65, 217, 0.35); --noise-opacity: 0.08; --texture-url: linear-gradient(135deg, rgba(255,255,255,0.06) 0 10%, transparent 10% 50%, rgba(255,255,255,0.04) 50% 60%, transparent 60% 100%); --anim-speed: 0.95; --anim-float-style: bounce; --panel-bg: linear-gradient(180deg, rgba(36,22,50,0.96), rgba(23,17,34,0.96)); --glass-opacity: 0.06; }
        [data-theme="trans"] { --bg-color: #0f1418; --bg-panel: #162028; --accent-color: #55cdfc; --accent-color-rgb: 85, 205, 252; --accent-dim: rgba(85, 205, 252, 0.16); --text-main: #eef8ff; --text-muted: #a5c2d2; --border-color: #28414e; --stripe-bg: linear-gradient(90deg, #55cdfc 0 20%, #f7a8b8 20% 40%, #ffffff 40% 60%, #f7a8b8 60% 80%, #55cdfc 80%); --stripe-height: 12px; --stripe-anim: wave; --particle-color: 85, 205, 252; --radius-base: 16px; --radius-card: 24px; --radius-btn: 999px; --font-display: "Avenir Next", "Segoe UI", sans-serif; --font-weight-heading: 600; --letter-spacing: 0.04em; --shadow-card: 0 16px 40px rgba(4, 18, 30, 0.32); --shadow-glow: 0 0 26px rgba(85, 205, 252, 0.28); --noise-opacity: 0.04; --texture-url: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.08), transparent 25%), linear-gradient(180deg, rgba(255,255,255,0.03), transparent 40%); --anim-speed: 1.05; --anim-float-style: float; --panel-bg: linear-gradient(180deg, rgba(22,32,40,0.92), rgba(20,27,34,0.92)); --glass-opacity: 0.08; }
        [data-theme="lesbian"] { --bg-color: #170e11; --bg-panel: #24171c; --accent-color: #ff8f50; --accent-color-rgb: 255, 143, 80; --accent-dim: rgba(255, 143, 80, 0.18); --text-main: #fff2ee; --text-muted: #d9b0a4; --border-color: #4d2b2c; --stripe-bg: linear-gradient(90deg, #d62900 0 20%, #ff9b55 20% 40%, #ffffff 40% 60%, #d461a6 60% 80%, #a40061 80%); --stripe-height: 13px; --stripe-anim: pulse; --particle-color: 255, 143, 80; --radius-base: 14px; --radius-card: 20px; --radius-btn: 999px; --font-display: "Georgia", "Times New Roman", serif; --font-weight-heading: 700; --letter-spacing: 0.03em; --shadow-card: 0 18px 44px rgba(44, 10, 16, 0.42); --shadow-glow: 0 0 24px rgba(255, 143, 80, 0.3); --noise-opacity: 0.07; --texture-url: radial-gradient(circle at 15% 25%, rgba(255,255,255,0.05), transparent 20%), linear-gradient(45deg, transparent 0 46%, rgba(255,255,255,0.04) 46% 54%, transparent 54% 100%); --anim-speed: 0.9; --anim-float-style: float; --panel-bg: linear-gradient(180deg, rgba(36,22,28,0.96), rgba(27,17,22,0.94)); --glass-opacity: 0.05; }
        [data-theme="bi"] { --bg-color: #100d16; --bg-panel: #1c1522; --accent-color: #d60270; --accent-color-rgb: 214, 2, 112; --accent-dim: rgba(214, 2, 112, 0.16); --text-main: #f8f1ff; --text-muted: #bca7cb; --border-color: #362447; --stripe-bg: linear-gradient(90deg, #d60270 0 40%, #9b4f96 40% 60%, #0038a8 60%); --stripe-height: 11px; --stripe-anim: shift; --particle-color: 214, 2, 112; --radius-base: 10px; --radius-card: 16px; --radius-btn: 999px; --font-display: "Gill Sans", "Trebuchet MS", sans-serif; --font-weight-heading: 800; --letter-spacing: 0.12em; --shadow-card: 0 20px 48px rgba(18, 5, 27, 0.46); --shadow-glow: 0 0 30px rgba(214, 2, 112, 0.34); --noise-opacity: 0.06; --texture-url: linear-gradient(135deg, rgba(255,255,255,0.05) 0 8%, transparent 8% 50%, rgba(255,255,255,0.03) 50% 58%, transparent 58% 100%); --anim-speed: 0.88; --anim-float-style: bounce; --panel-bg: linear-gradient(180deg, rgba(28,21,34,0.95), rgba(16,13,22,0.92)); --glass-opacity: 0.04; }
        [data-theme="nonbinary"] { --bg-color: #130f18; --bg-panel: #1d1724; --accent-color: #8f49d6; --accent-color-rgb: 143, 73, 214; --accent-dim: rgba(143, 73, 214, 0.18); --text-main: #fffbed; --text-muted: #ccbfa2; --border-color: #4c3d5c; --stripe-bg: linear-gradient(90deg, #fcf434 0 20%, #ffffff 20% 40%, #8f49d6 40% 60%, #9c59d1 60% 80%, #2c2c2c 80%); --stripe-height: 12px; --stripe-anim: stripe-wave; --particle-color: 143, 73, 214; --radius-base: 8px; --radius-card: 14px; --radius-btn: 999px; --font-display: "Arial Black", "Segoe UI", sans-serif; --font-weight-heading: 800; --letter-spacing: 0.14em; --shadow-card: 0 18px 40px rgba(14, 8, 24, 0.46); --shadow-glow: 0 0 24px rgba(143, 73, 214, 0.3); --noise-opacity: 0.08; --texture-url: linear-gradient(90deg, rgba(255,255,255,0.035) 1px, transparent 1px), linear-gradient(rgba(255,255,255,0.035) 1px, transparent 1px); --anim-speed: 0.92; --anim-float-style: bounce; --panel-bg: linear-gradient(180deg, rgba(29,23,36,0.96), rgba(21,18,27,0.93)); --glass-opacity: 0.03; }
        [data-theme="pan"] { --bg-color: #180f16; --bg-panel: #241720; --accent-color: #ff218c; --accent-color-rgb: 255, 33, 140; --accent-dim: rgba(255, 33, 140, 0.18); --text-main: #fff4fa; --text-muted: #d8a7bf; --border-color: #4b2942; --stripe-bg: linear-gradient(90deg, #ff218c 0 33.3%, #ffd800 33.3% 66.6%, #21b1ff 66.6%); --stripe-height: 13px; --stripe-anim: wave; --particle-color: 255, 33, 140; --radius-base: 18px; --radius-card: 26px; --radius-btn: 999px; --font-display: "Verdana", "Avenir Next", sans-serif; --font-weight-heading: 700; --letter-spacing: 0.06em; --shadow-card: 0 18px 44px rgba(31, 9, 23, 0.4); --shadow-glow: 0 0 28px rgba(255, 33, 140, 0.34); --noise-opacity: 0.05; --texture-url: radial-gradient(circle at 80% 15%, rgba(255,255,255,0.08), transparent 22%), radial-gradient(circle at 15% 80%, rgba(255,216,0,0.08), transparent 20%); --anim-speed: 0.86; --anim-float-style: bounce; --panel-bg: linear-gradient(180deg, rgba(36,23,32,0.94), rgba(28,18,27,0.94)); --glass-opacity: 0.07; }
        [data-theme="asexual"] { --bg-color: #111112; --bg-panel: #1a1a1d; --accent-color: #9b5de5; --accent-color-rgb: 155, 93, 229; --accent-dim: rgba(155, 93, 229, 0.16); --text-main: #f5f3f8; --text-muted: #b2a8bb; --border-color: #343139; --stripe-bg: linear-gradient(90deg, #000000 0 25%, #a3a3a3 25% 50%, #ffffff 50% 75%, #800080 75%); --stripe-height: 10px; --stripe-anim: none; --particle-color: 155, 93, 229; --radius-base: 4px; --radius-card: 8px; --radius-btn: 999px; --font-display: "Courier New", monospace; --font-weight-heading: 600; --letter-spacing: 0.1em; --shadow-card: 0 14px 30px rgba(0, 0, 0, 0.42); --shadow-glow: 0 0 18px rgba(155, 93, 229, 0.22); --noise-opacity: 0.03; --texture-url: linear-gradient(180deg, rgba(255,255,255,0.025), transparent 35%); --anim-speed: 1.12; --anim-float-style: sink; --panel-bg: linear-gradient(180deg, rgba(26,26,29,0.96), rgba(20,20,22,0.95)); --glass-opacity: 0.02; }
        [data-theme="genderqueer"] { --bg-color: #101411; --bg-panel: #182019; --accent-color: #b57edc; --accent-color-rgb: 181, 126, 220; --accent-dim: rgba(181, 126, 220, 0.16); --text-main: #f4fff2; --text-muted: #aac3a3; --border-color: #304132; --stripe-bg: linear-gradient(90deg, #b57edc 0 33.3%, #ffffff 33.3% 66.6%, #4a8123 66.6%); --stripe-height: 12px; --stripe-anim: shift; --particle-color: 181, 126, 220; --radius-base: 14px; --radius-card: 20px; --radius-btn: 999px; --font-display: "Palatino", "Book Antiqua", serif; --font-weight-heading: 700; --letter-spacing: 0.08em; --shadow-card: 0 18px 38px rgba(12, 22, 13, 0.38); --shadow-glow: 0 0 24px rgba(181, 126, 220, 0.3); --noise-opacity: 0.07; --texture-url: linear-gradient(135deg, rgba(255,255,255,0.04) 0 14%, transparent 14% 50%, rgba(255,255,255,0.04) 50% 64%, transparent 64% 100%); --anim-speed: 0.95; --anim-float-style: spiral; --panel-bg: linear-gradient(180deg, rgba(24,32,25,0.94), rgba(17,24,18,0.94)); --glass-opacity: 0.05; }
        [data-theme="intersex"] { --bg-color: #1a1608; --bg-panel: #251f0d; --accent-color: #ffd800; --accent-color-rgb: 255, 216, 0; --accent-dim: rgba(255, 216, 0, 0.16); --text-main: #fff8d9; --text-muted: #d2c27b; --border-color: #57471a; --stripe-bg: radial-gradient(circle at 50% 50%, transparent 22%, #7a00ac 23% 34%, transparent 35%), linear-gradient(90deg, #ffd800 0 100%); --stripe-height: 14px; --stripe-anim: stripe-pulse; --particle-color: 255, 216, 0; --radius-base: 999px; --radius-card: 28px; --radius-btn: 999px; --font-display: "Gill Sans", "Segoe UI", sans-serif; --font-weight-heading: 700; --letter-spacing: 0.12em; --shadow-card: 0 20px 40px rgba(37, 25, 4, 0.45); --shadow-glow: 0 0 26px rgba(255, 216, 0, 0.28); --noise-opacity: 0.04; --texture-url: radial-gradient(circle at 50% 50%, rgba(122,0,172,0.08), transparent 24%); --anim-speed: 0.98; --anim-float-style: float; --panel-bg: linear-gradient(180deg, rgba(37,31,13,0.95), rgba(29,24,10,0.92)); --glass-opacity: 0.03; }
        [data-theme="yami_kawaii"] { --bg-color: #120d14; --bg-panel: #1c141f; --accent-color: #ff9cd2; --accent-color-rgb: 255, 156, 210; --accent-dim: rgba(255, 156, 210, 0.18); --text-main: #fff2f8; --text-muted: #d5abc1; --border-color: #4a3144; --stripe-bg: repeating-linear-gradient(90deg, #ff9cd2 0 16px, #120d14 16px 32px, #ffd5eb 32px 42px, #120d14 42px 58px); --stripe-height: 12px; --stripe-anim: stripe-wave; --particle-color: 255, 156, 210; --radius-base: 24px; --radius-card: 32px; --radius-btn: 999px; --font-display: "Arial Rounded MT Bold", "Trebuchet MS", sans-serif; --font-weight-heading: 800; --letter-spacing: 0.08em; --shadow-card: 0 18px 44px rgba(15, 6, 15, 0.44); --shadow-glow: 0 0 30px rgba(255, 156, 210, 0.34); --noise-opacity: 0.12; --texture-url: radial-gradient(circle at 18% 22%, rgba(255,255,255,0.07), transparent 18%), radial-gradient(circle at 76% 26%, rgba(255,156,210,0.1), transparent 16%), linear-gradient(135deg, rgba(255,255,255,0.04) 0 10%, transparent 10% 50%, rgba(0,0,0,0.18) 50% 56%, transparent 56% 100%); --anim-speed: 0.84; --anim-float-style: bounce; --panel-bg: linear-gradient(180deg, rgba(28,20,31,0.95), rgba(19,14,23,0.95)); --glass-opacity: 0.08; }
        [data-theme="pastel_goth"] { --bg-color: #0f0d14; --bg-panel: #171320; --accent-color: #c8b3ff; --accent-color-rgb: 200, 179, 255; --accent-dim: rgba(200, 179, 255, 0.16); --text-main: #eef6f2; --text-muted: #aab8b3; --border-color: #322b40; --stripe-bg: linear-gradient(90deg, #c8b3ff 0 24%, #7ee2c6 24% 42%, #101018 42% 58%, #b08ad9 58% 76%, #d7f5ea 76%); --stripe-height: 11px; --stripe-anim: stripe-shift; --particle-color: 200, 179, 255; --radius-base: 16px; --radius-card: 22px; --radius-btn: 999px; --font-display: "Palatino", "Book Antiqua", serif; --font-weight-heading: 700; --letter-spacing: 0.09em; --shadow-card: 0 20px 48px rgba(5, 5, 9, 0.52); --shadow-glow: 0 0 22px rgba(200, 179, 255, 0.22); --noise-opacity: 0.07; --texture-url: radial-gradient(circle at 12% 20%, rgba(255,255,255,0.06), transparent 14%), repeating-linear-gradient(135deg, rgba(255,255,255,0.028) 0 6px, transparent 6px 12px), linear-gradient(90deg, rgba(126,226,198,0.04), transparent 44%, rgba(200,179,255,0.05) 100%); --anim-speed: 0.96; --anim-float-style: float; --panel-bg: linear-gradient(180deg, rgba(23,19,32,0.96), rgba(15,13,23,0.94)); --glass-opacity: 0.06; }
        [data-theme="deep_sea"] { --bg-color: #06111d; --bg-panel: #0c1a2a; --accent-color: #49d6ff; --accent-color-rgb: 73, 214, 255; --accent-dim: rgba(73, 214, 255, 0.16); --text-main: #e5f8ff; --text-muted: #86adc0; --border-color: #173247; --stripe-bg: linear-gradient(90deg, #031523 0 18%, #0a3550 18% 42%, #49d6ff 42% 58%, #0b3d55 58% 82%, #031523 82%); --stripe-height: 10px; --stripe-anim: stripe-shift; --particle-color: 73, 214, 255; --radius-base: 18px; --radius-card: 26px; --radius-btn: 999px; --font-display: "Trebuchet MS", "Avenir Next", sans-serif; --font-weight-heading: 700; --letter-spacing: 0.07em; --shadow-card: 0 28px 60px rgba(2, 7, 15, 0.68); --shadow-glow: 0 0 24px rgba(73, 214, 255, 0.22); --noise-opacity: 0.09; --texture-url: radial-gradient(circle at 18% 24%, rgba(73,214,255,0.08), transparent 16%), radial-gradient(circle at 80% 10%, rgba(255,255,255,0.04), transparent 12%), repeating-radial-gradient(circle at 50% 10%, rgba(255,255,255,0.03) 0 2px, transparent 2px 14px); --anim-speed: 1.24; --anim-float-style: sink; --panel-bg: linear-gradient(180deg, rgba(12,26,42,0.96), rgba(7,18,29,0.96)); --glass-opacity: 0.05; }
        [data-theme="goat"] { --bg-color: #191517; --bg-panel: #221d22; --accent-color: #bfa6e8; --accent-color-rgb: 191, 166, 232; --accent-dim: rgba(191, 166, 232, 0.16); --text-main: #f4ede5; --text-muted: #beaead; --border-color: #4b4045; --stripe-bg: linear-gradient(120deg, #f4ede5 0 14%, #bfa6e8 14% 28%, #2b2429 28% 50%, #9d8570 50% 68%, #221d22 68% 100%); --stripe-height: 9px; --stripe-anim: stripe-shift; --particle-color: 191, 166, 232; --radius-base: 14px; --radius-card: 18px; --radius-btn: 999px; --font-display: "Georgia", "Times New Roman", serif; --font-weight-heading: 700; --letter-spacing: 0.08em; --shadow-card: 0 18px 40px rgba(14, 10, 13, 0.5); --shadow-glow: 0 0 20px rgba(191, 166, 232, 0.2); --noise-opacity: 0.08; --texture-url: linear-gradient(135deg, rgba(255,255,255,0.04) 0 12%, transparent 12% 50%, rgba(255,255,255,0.02) 50% 62%, transparent 62% 100%), radial-gradient(circle at 18% 80%, rgba(255,255,255,0.05), transparent 16%); --anim-speed: 0.94; --anim-float-style: float; --panel-bg: linear-gradient(180deg, rgba(34,29,34,0.95), rgba(25,21,24,0.95)); --glass-opacity: 0.04; }
        [data-theme="moth"] { --bg-color: #17131a; --bg-panel: #211b24; --accent-color: #c7afd6; --accent-color-rgb: 199, 175, 214; --accent-dim: rgba(199, 175, 214, 0.16); --text-main: #f7f0e4; --text-muted: #c0b3a7; --border-color: #4a404c; --stripe-bg: linear-gradient(90deg, #f7f0e4 0 20%, #c7afd6 20% 40%, #8b7ca4 40% 60%, #312739 60% 80%, #f2d9a9 80%); --stripe-height: 10px; --stripe-anim: stripe-wave; --particle-color: 199, 175, 214; --radius-base: 18px; --radius-card: 24px; --radius-btn: 999px; --font-display: "Palatino Linotype", "Book Antiqua", serif; --font-weight-heading: 700; --letter-spacing: 0.08em; --shadow-card: 0 20px 46px rgba(10, 8, 13, 0.48); --shadow-glow: 0 0 24px rgba(199, 175, 214, 0.22); --noise-opacity: 0.08; --texture-url: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.06), transparent 14%), radial-gradient(circle at 80% 70%, rgba(242,217,169,0.05), transparent 16%), repeating-linear-gradient(150deg, rgba(255,255,255,0.02) 0 5px, transparent 5px 10px); --anim-speed: 1.08; --anim-float-style: spiral; --panel-bg: linear-gradient(180deg, rgba(33,27,36,0.95), rgba(24,20,28,0.94)); --glass-opacity: 0.06; }
        [data-theme="bunny"] { --bg-color: #fdf7fb; --bg-panel: #fffdfd; --accent-color: #ffb5d6; --accent-color-rgb: 255, 181, 214; --accent-dim: rgba(255, 181, 214, 0.22); --text-main: #443642; --text-muted: #91758a; --border-color: #f0d7e4; --stripe-bg: linear-gradient(90deg, #ffffff 0 22%, #ffd6e8 22% 44%, #ffb5d6 44% 56%, #ffffff 56% 78%, #ffd6e8 78%); --stripe-height: 11px; --stripe-anim: stripe-wave; --particle-color: 255, 181, 214; --radius-base: 26px; --radius-card: 34px; --radius-btn: 999px; --font-display: "Arial Rounded MT Bold", "Trebuchet MS", sans-serif; --font-weight-heading: 700; --letter-spacing: 0.05em; --shadow-card: 0 18px 32px rgba(232, 190, 210, 0.28); --shadow-glow: 0 0 26px rgba(255, 181, 214, 0.28); --noise-opacity: 0.05; --texture-url: radial-gradient(circle at 24% 20%, rgba(255,255,255,0.8), transparent 18%), radial-gradient(circle at 72% 58%, rgba(255,215,234,0.55), transparent 20%), repeating-radial-gradient(circle at 50% 50%, rgba(255,255,255,0.5) 0 3px, transparent 3px 14px); --anim-speed: 0.88; --anim-float-style: bounce; --panel-bg: linear-gradient(180deg, rgba(255,255,255,0.92), rgba(255,247,251,0.95)); --glass-opacity: 0.12; }
        [data-theme="sea_bunny"] { --bg-color: #0b1f32; --bg-panel: #11304a; --accent-color: #ff8c42; --accent-color-rgb: 255, 140, 66; --accent-dim: rgba(255, 140, 66, 0.16); --text-main: #eff9ff; --text-muted: #9ec1d9; --border-color: #23506c; --stripe-bg: linear-gradient(90deg, #ffffff 0 18%, #ff8c42 18% 28%, #4ed6ff 28% 56%, #ffffff 56% 74%, #ff8c42 74% 84%, #0b1f32 84%); --stripe-height: 10px; --stripe-anim: stripe-wave; --particle-color: 255, 140, 66; --radius-base: 22px; --radius-card: 30px; --radius-btn: 999px; --font-display: "Avenir Next", "Trebuchet MS", sans-serif; --font-weight-heading: 700; --letter-spacing: 0.06em; --shadow-card: 0 22px 48px rgba(3, 12, 22, 0.54); --shadow-glow: 0 0 24px rgba(78, 214, 255, 0.24); --noise-opacity: 0.07; --texture-url: radial-gradient(circle at 16% 18%, rgba(255,255,255,0.08), transparent 14%), radial-gradient(circle at 82% 70%, rgba(255,140,66,0.08), transparent 12%), linear-gradient(180deg, rgba(255,255,255,0.03), transparent 42%); --anim-speed: 1.04; --anim-float-style: float; --panel-bg: linear-gradient(180deg, rgba(17,48,74,0.94), rgba(10,29,46,0.95)); --glass-opacity: 0.07; }
        [data-theme="hexcorp"] { --bg-color: #070707; --bg-panel: #111111; --accent-color: #c7a14d; --accent-color-rgb: 199, 161, 77; --accent-dim: rgba(199, 161, 77, 0.16); --text-main: #f4ecd9; --text-muted: #9f9277; --border-color: #3a3324; --stripe-bg: repeating-linear-gradient(120deg, #0d0d0d 0 18px, #151515 18px 30px, #c7a14d 30px 36px, #0d0d0d 36px 54px); --stripe-height: 8px; --stripe-anim: stripe-shift; --particle-color: 199, 161, 77; --radius-base: 4px; --radius-card: 8px; --radius-btn: 6px; --font-display: "Helvetica Neue", "Arial Narrow", sans-serif; --font-weight-heading: 800; --letter-spacing: 0.16em; --shadow-card: 0 20px 44px rgba(0, 0, 0, 0.7); --shadow-glow: 0 0 18px rgba(199, 161, 77, 0.18); --noise-opacity: 0.08; --texture-url: radial-gradient(circle at 18% 22%, rgba(255,255,255,0.03), transparent 14%), repeating-linear-gradient(60deg, rgba(199,161,77,0.06) 0 2px, transparent 2px 18px), repeating-linear-gradient(120deg, rgba(255,255,255,0.025) 0 1px, transparent 1px 16px); --anim-speed: 0.72; --anim-float-style: bounce; --panel-bg: linear-gradient(180deg, rgba(17,17,17,0.97), rgba(9,9,9,0.98)); --glass-opacity: 0.02; }
        [data-theme="dickgirl"] { --bg-color: #170d14; --bg-panel: #21131c; --accent-color: #e62e8a; --accent-color-rgb: 230, 46, 138; --accent-dim: rgba(230, 46, 138, 0.18); --text-main: #fff1f7; --text-muted: #d3a3bb; --border-color: #48203a; --stripe-bg: linear-gradient(90deg, #4a0033, #e62e8a, #4a0033); --stripe-height: 9px; --stripe-anim: shift; --particle-color: 230, 46, 138; --radius-base: 18px; --radius-card: 22px; --radius-btn: 999px; --font-display: "Georgia", serif; --font-weight-heading: 700; --letter-spacing: 0.05em; --shadow-card: 0 22px 50px rgba(33, 4, 21, 0.48); --shadow-glow: 0 0 28px rgba(230, 46, 138, 0.35); --noise-opacity: 0.06; --texture-url: linear-gradient(120deg, rgba(255,255,255,0.08), transparent 30%, rgba(255,255,255,0.03) 60%, transparent 85%); --anim-speed: 0.85; --anim-float-style: bounce; --panel-bg: linear-gradient(180deg, rgba(33,19,28,0.95), rgba(25,14,21,0.94)); --glass-opacity: 0.08; }
        [data-theme="femboy"] { --bg-color: #14171a; --bg-panel: #1b1e22; --accent-color: #f5a9b8; --accent-color-rgb: 245, 169, 184; --accent-dim: rgba(245, 169, 184, 0.18); --text-main: #fcf8fb; --text-muted: #b9b7c8; --border-color: #353a44; --stripe-bg: linear-gradient(90deg, #5bcffa, #f5a9b8, #ffffff, #f5a9b8, #5bcffa); --stripe-height: 10px; --stripe-anim: wave; --particle-color: 245, 169, 184; --radius-base: 20px; --radius-card: 28px; --radius-btn: 999px; --font-display: "Avenir Next", "Segoe UI", sans-serif; --font-weight-heading: 600; --letter-spacing: 0.05em; --shadow-card: 0 18px 36px rgba(14, 18, 24, 0.38); --shadow-glow: 0 0 24px rgba(245, 169, 184, 0.32); --noise-opacity: 0.05; --texture-url: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.07), transparent 18%), radial-gradient(circle at 80% 60%, rgba(91,207,250,0.06), transparent 20%); --anim-speed: 0.9; --anim-float-style: bounce; --panel-bg: linear-gradient(180deg, rgba(27,30,34,0.92), rgba(23,26,31,0.92)); --glass-opacity: 0.09; }
        [data-theme="dominant"] { --bg-color: #0b0a0a; --bg-panel: #141111; --accent-color: #cc0000; --accent-color-rgb: 204, 0, 0; --accent-dim: rgba(204, 0, 0, 0.18); --text-main: #f5efef; --text-muted: #b09999; --border-color: #3b1d1d; --stripe-bg: linear-gradient(90deg, #111, #cc0000, #111); --stripe-height: 8px; --stripe-anim: pulse; --particle-color: 204, 0, 0; --radius-base: 2px; --radius-card: 6px; --radius-btn: 4px; --font-display: "Arial Black", "Impact", sans-serif; --font-weight-heading: 800; --letter-spacing: 0.16em; --shadow-card: 0 24px 54px rgba(0, 0, 0, 0.62); --shadow-glow: 0 0 20px rgba(204, 0, 0, 0.24); --noise-opacity: 0.06; --texture-url: linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px), linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px); --anim-speed: 0.78; --anim-float-style: sink; --panel-bg: linear-gradient(180deg, rgba(20,17,17,0.97), rgba(12,10,10,0.96)); --glass-opacity: 0.01; }
        [data-theme="submissive"] { --bg-color: #121216; --bg-panel: #1a1a20; --accent-color: #9d8ec7; --accent-color-rgb: 157, 142, 199; --accent-dim: rgba(157, 142, 199, 0.16); --text-main: #f4f1fb; --text-muted: #b4aec8; --border-color: #363247; --stripe-bg: linear-gradient(90deg, #6a5acd, #9d8ec7, #e6e6fa); --stripe-height: 10px; --stripe-anim: wave; --particle-color: 157, 142, 199; --radius-base: 18px; --radius-card: 22px; --radius-btn: 999px; --font-display: "Palatino", serif; --font-weight-heading: 600; --letter-spacing: 0.04em; --shadow-card: 0 18px 42px rgba(11, 10, 19, 0.42); --shadow-glow: 0 0 22px rgba(157, 142, 199, 0.28); --noise-opacity: 0.04; --texture-url: linear-gradient(180deg, rgba(255,255,255,0.03), transparent 40%), radial-gradient(circle at 75% 15%, rgba(255,255,255,0.05), transparent 15%); --anim-speed: 1.1; --anim-float-style: float; --panel-bg: linear-gradient(180deg, rgba(26,26,32,0.93), rgba(20,20,24,0.93)); --glass-opacity: 0.08; }
        [data-theme="musk"] { --bg-color: #12130b; --bg-panel: #1b1d12; --accent-color: #b58d3d; --accent-color-rgb: 181, 141, 61; --accent-dim: rgba(181, 141, 61, 0.18); --text-main: #f5f1df; --text-muted: #b7a983; --border-color: #473b23; --stripe-bg: linear-gradient(90deg, #2c3618, #b58d3d, #4b3621); --stripe-height: 9px; --stripe-anim: shift; --particle-color: 181, 141, 61; --radius-base: 10px; --radius-card: 16px; --radius-btn: 999px; --font-display: "Trebuchet MS", sans-serif; --font-weight-heading: 700; --letter-spacing: 0.08em; --shadow-card: 0 18px 40px rgba(17, 15, 8, 0.48); --shadow-glow: 0 0 22px rgba(181, 141, 61, 0.26); --noise-opacity: 0.1; --texture-url: radial-gradient(circle at 25% 30%, rgba(255,255,255,0.03), transparent 18%), linear-gradient(135deg, rgba(0,0,0,0.08), transparent 40%); --anim-speed: 0.95; --anim-float-style: spiral; --panel-bg: linear-gradient(180deg, rgba(27,29,18,0.96), rgba(22,23,14,0.94)); --glass-opacity: 0.03; }
        [data-theme="pup"] { --bg-color: #0a0c12; --bg-panel: #12151c; --accent-color: #2b5cff; --accent-color-rgb: 43, 92, 255; --accent-dim: rgba(43, 92, 255, 0.18); --text-main: #eef2ff; --text-muted: #9ba8c8; --border-color: #263352; --stripe-bg: linear-gradient(90deg, #000, #2b5cff, #fff, #2b5cff, #000); --stripe-height: 10px; --stripe-anim: pulse; --particle-color: 43, 92, 255; --radius-base: 14px; --radius-card: 20px; --radius-btn: 999px; --font-display: "Verdana", sans-serif; --font-weight-heading: 700; --letter-spacing: 0.1em; --shadow-card: 0 18px 44px rgba(3, 7, 18, 0.48); --shadow-glow: 0 0 24px rgba(43, 92, 255, 0.32); --noise-opacity: 0.05; --texture-url: radial-gradient(circle at 10% 20%, rgba(255,255,255,0.06), transparent 12%), radial-gradient(circle at 65% 72%, rgba(255,255,255,0.05), transparent 11%); --anim-speed: 0.9; --anim-float-style: bounce; --panel-bg: linear-gradient(180deg, rgba(18,21,28,0.94), rgba(12,15,21,0.94)); --glass-opacity: 0.06; }
        [data-theme="rubber"] { --bg-color: #060606; --bg-panel: #111111; --accent-color: #f1f1f1; --accent-color-rgb: 241, 241, 241; --accent-dim: rgba(241, 241, 241, 0.14); --text-main: #f5f5f5; --text-muted: #9f9f9f; --border-color: #2e2e2e; --stripe-bg: linear-gradient(90deg, #111 0, #f8f8f8 25%, #1a1a1a 50%, #ffffff 75%, #111 100%); --stripe-height: 6px; --stripe-anim: shift; --particle-color: 241, 241, 241; --radius-base: 0; --radius-card: 0; --radius-btn: 0; --font-display: "Arial Black", "Impact", sans-serif; --font-weight-heading: 800; --letter-spacing: 0.18em; --shadow-card: 0 20px 0 rgba(0,0,0,0.55), 0 30px 60px rgba(0,0,0,0.65); --shadow-glow: 0 0 18px rgba(255,255,255,0.18); --border-width: 1px; --border-style: solid; --noise-opacity: 0.12; --texture-url: linear-gradient(160deg, rgba(255,255,255,0.22) 0 8%, rgba(255,255,255,0.02) 8% 32%, rgba(255,255,255,0.16) 32% 36%, transparent 36% 100%); --anim-speed: 0.72; --anim-float-style: bounce; --panel-bg: linear-gradient(180deg, rgba(18,18,18,0.98), rgba(8,8,8,0.98)); --glass-opacity: 0.02; }
        [data-theme="rope"] { --bg-color: #1a1510; --bg-panel: #241d15; --accent-color: #c8a56e; --accent-color-rgb: 200, 165, 110; --accent-dim: rgba(200, 165, 110, 0.16); --text-main: #f4ecde; --text-muted: #bda887; --border-color: #4c3b26; --stripe-bg: repeating-linear-gradient(90deg, #8b6b3f 0 10px, #b08a54 10px 20px, #d8ba83 20px 30px); --stripe-height: 8px; --stripe-anim: none; --particle-color: 200, 165, 110; --radius-base: 8px; --radius-card: 16px; --radius-btn: 999px; --font-display: "Georgia", serif; --font-weight-heading: 700; --letter-spacing: 0.06em; --shadow-card: 0 16px 34px rgba(14, 9, 4, 0.4); --shadow-glow: 0 0 20px rgba(200, 165, 110, 0.18); --noise-opacity: 0.1; --texture-url: linear-gradient(90deg, rgba(255,255,255,0.03) 0 8%, transparent 8% 14%, rgba(0,0,0,0.08) 14% 17%, transparent 17% 100%); --anim-speed: 1; --anim-float-style: float; --panel-bg: linear-gradient(180deg, rgba(36,29,21,0.96), rgba(27,22,16,0.95)); --glass-opacity: 0.01; }
        [data-theme="inflation"] { --bg-color: #201a2a; --bg-panel: #2b2238; --accent-color: #ffb7df; --accent-color-rgb: 255, 183, 223; --accent-dim: rgba(255, 183, 223, 0.18); --text-main: #fff7fd; --text-muted: #d4bfd7; --border-color: #5d4b73; --stripe-bg: linear-gradient(90deg, #ffb7df, #bde7ff, #fff5aa, #ffb7df); --stripe-height: 18px; --stripe-anim: pulse; --particle-color: 255, 183, 223; --radius-base: 20px; --radius-card: 32px; --radius-btn: 999px; --font-display: "Arial Rounded MT Bold", "Trebuchet MS", sans-serif; --font-weight-heading: 700; --letter-spacing: 0.05em; --shadow-card: 0 20px 36px rgba(26, 14, 38, 0.36); --shadow-glow: 0 0 32px rgba(255, 183, 223, 0.34); --noise-opacity: 0.04; --texture-url: radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1), transparent 18%), radial-gradient(circle at 75% 60%, rgba(255,255,255,0.06), transparent 20%); --anim-speed: 0.82; --anim-float-style: bounce; --panel-bg: linear-gradient(180deg, rgba(43,34,56,0.92), rgba(35,28,46,0.9)); --glass-opacity: 0.12; }
        [data-theme="vore"] { --bg-color: #0d170f; --bg-panel: #152218; --accent-color: #6bb86f; --accent-color-rgb: 107, 184, 111; --accent-dim: rgba(107, 184, 111, 0.16); --text-main: #edf7ea; --text-muted: #9db099; --border-color: #29422d; --stripe-bg: linear-gradient(90deg, #0d170f, #375a2e, #6bb86f, #254127); --stripe-height: 7px; --stripe-anim: wave; --particle-color: 107, 184, 111; --radius-base: 12px; --radius-card: 18px; --radius-btn: 999px; --font-display: "Palatino Linotype", serif; --font-weight-heading: 700; --letter-spacing: 0.04em; --shadow-card: 0 22px 52px rgba(3, 10, 4, 0.54); --shadow-glow: 0 0 16px rgba(107, 184, 111, 0.22); --noise-opacity: 0.12; --texture-url: radial-gradient(circle at 30% 20%, rgba(0,0,0,0.16), transparent 22%), radial-gradient(circle at 70% 70%, rgba(255,255,255,0.03), transparent 14%); --anim-speed: 1.4; --anim-float-style: sink; --panel-bg: linear-gradient(180deg, rgba(21,34,24,0.96), rgba(13,23,15,0.96)); --glass-opacity: 0.02; }
        [data-theme="werewolf"] { --bg-color: #16120f; --bg-panel: #221c18; --accent-color: #c6c8d2; --accent-color-rgb: 198, 200, 210; --accent-dim: rgba(198, 200, 210, 0.16); --text-main: #f2eee8; --text-muted: #b3a18e; --border-color: #4d3b2b; --stripe-bg: linear-gradient(90deg, #3c2b20, #8c6c4f, #c6c8d2, #544335); --stripe-height: 9px; --stripe-anim: shift; --particle-color: 198, 200, 210; --radius-base: 10px; --radius-card: 18px; --radius-btn: 999px; --font-display: "Georgia", serif; --font-weight-heading: 700; --letter-spacing: 0.08em; --shadow-card: 0 20px 44px rgba(9, 6, 4, 0.5); --shadow-glow: 0 0 22px rgba(198, 200, 210, 0.2); --noise-opacity: 0.1; --texture-url: linear-gradient(135deg, rgba(255,255,255,0.03) 0 8%, transparent 8% 18%, rgba(0,0,0,0.12) 18% 25%, transparent 25% 100%); --anim-speed: 1.06; --anim-float-style: spiral; --panel-bg: linear-gradient(180deg, rgba(34,28,24,0.96), rgba(22,18,15,0.96)); --glass-opacity: 0.03; }
        [data-theme="hypno"] { --bg-color: #0d1024; --bg-panel: #141938; --accent-color: #8a7dff; --accent-color-rgb: 138, 125, 255; --accent-dim: rgba(138, 125, 255, 0.18); --text-main: #f3f2ff; --text-muted: #aaa8d4; --border-color: #2b2f5d; --stripe-bg: repeating-radial-gradient(circle at center, #3d2fa4 0 10px, #8a7dff 10px 20px, #141938 20px 30px); --stripe-height: 16px; --stripe-anim: wave; --particle-color: 138, 125, 255; --radius-base: 14px; --radius-card: 22px; --radius-btn: 999px; --font-display: "Times New Roman", serif; --font-weight-heading: 600; --letter-spacing: 0.16em; --shadow-card: 0 22px 60px rgba(5, 8, 24, 0.56); --shadow-glow: 0 0 34px rgba(138, 125, 255, 0.42); --noise-opacity: 0.08; --texture-url: repeating-radial-gradient(circle at center, rgba(255,255,255,0.08) 0 2px, transparent 2px 12px); --anim-speed: 2; --anim-float-style: spiral; --panel-bg: linear-gradient(180deg, rgba(20,25,56,0.94), rgba(12,15,36,0.94)); --glass-opacity: 0.08; }
        [data-theme="daddy"] { --bg-color: #1a130d; --bg-panel: #271d15; --accent-color: #d59b45; --accent-color-rgb: 213, 155, 69; --accent-dim: rgba(213, 155, 69, 0.16); --text-main: #f8efe1; --text-muted: #c5ab84; --border-color: #6a4d2b; --stripe-bg: linear-gradient(90deg, #7b4c16, #d59b45, #a06b2f); --stripe-height: 10px; --stripe-anim: pulse; --particle-color: 213, 155, 69; --radius-base: 16px; --radius-card: 24px; --radius-btn: 999px; --font-display: "Georgia", serif; --font-weight-heading: 800; --letter-spacing: 0.07em; --shadow-card: 0 18px 40px rgba(19, 11, 4, 0.44); --shadow-glow: 0 0 26px rgba(213, 155, 69, 0.28); --border-width: 2px; --border-style: solid; --noise-opacity: 0.07; --texture-url: linear-gradient(90deg, rgba(255,255,255,0.025) 0 10%, transparent 10% 20%, rgba(0,0,0,0.08) 20% 25%, transparent 25% 100%); --anim-speed: 0.95; --anim-float-style: float; --panel-bg: linear-gradient(180deg, rgba(39,29,21,0.95), rgba(29,21,15,0.94)); --glass-opacity: 0.05; }
        [data-theme="genderfluid"] { --bg-color: #11131b; --bg-panel: #191c28; --accent-color: #ff6ad5; --accent-color-rgb: 255, 106, 213; --accent-dim: rgba(255, 106, 213, 0.16); --text-main: #f4f7ff; --text-muted: #b7bfd7; --border-color: #303751; --stripe-bg: linear-gradient(90deg, #ff76c8, #ffffff, #b57edc, #2f3cbe, #1eb6ff, #ff76c8); --stripe-height: 15px; --stripe-anim: shift; --particle-color: 255, 106, 213; --radius-base: 14px; --radius-card: 22px; --radius-btn: 999px; --font-display: "Avenir Next", "Segoe UI", sans-serif; --font-weight-heading: 700; --letter-spacing: 0.08em; --shadow-card: 0 18px 42px rgba(9, 11, 21, 0.4); --shadow-glow: 0 0 28px rgba(255, 106, 213, 0.28); --noise-opacity: 0.05; --texture-url: linear-gradient(135deg, rgba(255,255,255,0.04), transparent 30%, rgba(30,182,255,0.04) 65%, transparent 100%); --anim-speed: 0.92; --anim-float-style: spiral; --panel-bg: linear-gradient(180deg, rgba(25,28,40,0.93), rgba(19,22,31,0.93)); --glass-opacity: 0.08; }
        [data-theme="guro"] { --bg-color: #efebe6; --bg-panel: #f8f4ef; --accent-color: #8d0f22; --accent-color-rgb: 141, 15, 34; --accent-dim: rgba(141, 15, 34, 0.12); --text-main: #241819; --text-muted: #7c6768; --border-color: #cfbeb8; --stripe-bg: linear-gradient(90deg, #f8f4ef, #d9c6c0, #8d0f22, #f8f4ef); --stripe-height: 6px; --stripe-anim: pulse; --particle-color: 141, 15, 34; --radius-base: 0; --radius-card: 0; --radius-btn: 0; --font-display: "Helvetica Neue", Arial, sans-serif; --font-weight-heading: 700; --letter-spacing: 0.14em; --shadow-card: 0 18px 30px rgba(71, 43, 43, 0.14); --shadow-glow: 0 0 18px rgba(141, 15, 34, 0.12); --border-width: 2px; --border-style: solid; --noise-opacity: 0.06; --texture-url: linear-gradient(90deg, rgba(0,0,0,0.03) 1px, transparent 1px), linear-gradient(rgba(141,15,34,0.03) 1px, transparent 1px); --anim-speed: 1.08; --anim-float-style: sink; --panel-bg: linear-gradient(180deg, rgba(248,244,239,0.96), rgba(239,235,230,0.96)); --glass-opacity: 0; }
        [data-theme="living_toilet"] { --bg-color: #1a140e; --bg-panel: #251c13; --accent-color: #b49343; --accent-color-rgb: 180, 147, 67; --accent-dim: rgba(180, 147, 67, 0.14); --text-main: #efe0c2; --text-muted: #a59064; --border-color: #4d3c21; --stripe-bg: linear-gradient(90deg, #4f3a18, #6d5b22, #b49343, #5e481d); --stripe-height: 8px; --stripe-anim: wave; --particle-color: 180, 147, 67; --radius-base: 10px; --radius-card: 16px; --radius-btn: 999px; --font-display: "Trebuchet MS", sans-serif; --font-weight-heading: 700; --letter-spacing: 0.08em; --shadow-card: 0 18px 44px rgba(14, 9, 4, 0.48); --shadow-glow: 0 0 16px rgba(180, 147, 67, 0.16); --noise-opacity: 0.13; --texture-url: radial-gradient(circle at 20% 20%, rgba(0,0,0,0.14), transparent 16%), linear-gradient(135deg, rgba(92,69,24,0.1), transparent 45%); --anim-speed: 1.15; --anim-float-style: sink; --panel-bg: linear-gradient(180deg, rgba(37,28,19,0.96), rgba(28,21,14,0.95)); --glass-opacity: 0.01; }
        [data-theme="parasites"] { --bg-color: #170911; --bg-panel: #220e18; --accent-color: #ff5ca8; --accent-color-rgb: 255, 92, 168; --accent-dim: rgba(255, 92, 168, 0.18); --text-main: #fff0f7; --text-muted: #c89caf; --border-color: #4d2236; --stripe-bg: repeating-linear-gradient(115deg, #220e18 0 12px, #ff7ab9 12px 18px, #c72f7f 18px 28px, #34101f 28px 42px, #ff5ca8 42px 48px); --stripe-height: 8px; --stripe-anim: stripe-shift; --particle-color: 255, 92, 168; --radius-base: 8px; --radius-card: 14px; --radius-btn: 999px; --font-display: "Arial", sans-serif; --font-weight-heading: 700; --letter-spacing: 0.09em; --shadow-card: 0 24px 52px rgba(12, 3, 8, 0.6); --shadow-glow: 0 0 22px rgba(255, 92, 168, 0.2); --noise-opacity: 0.17; --texture-url: radial-gradient(circle at 22% 28%, rgba(0,0,0,0.2), transparent 15%), radial-gradient(circle at 66% 62%, rgba(255,92,168,0.1), transparent 14%), repeating-radial-gradient(circle at 50% 50%, rgba(255,255,255,0.02) 0 2px, transparent 2px 14px); --anim-speed: 1.38; --anim-float-style: sink; --panel-bg: linear-gradient(180deg, rgba(34,14,24,0.97), rgba(18,8,14,0.96)); --glass-opacity: 0.01; }

        /* Themed Extras! */
        .theme-vibe-container { position: fixed; inset: 0; pointer-events: none; z-index: -1; overflow: hidden; opacity: 0.6; }
        .theme-extra {
            --theme-extra-duration: 12s;
            --theme-extra-delay: 0s;
            --theme-extra-direction: normal;
            display: none;
            position: absolute;
            width: min(12vw, 120px);
            color: var(--accent-color);
            filter: drop-shadow(0 0 10px rgba(var(--accent-color-rgb), 0.28));
            animation: var(--anim-float-style) calc(12s * var(--anim-speed)) ease-in-out infinite;
            animation-duration: calc(var(--theme-extra-duration) * var(--anim-speed));
            animation-delay: var(--theme-extra-delay);
            animation-direction: var(--theme-extra-direction);
            transform-origin: center;
        }
        .theme-extra svg { display: block; width: 100%; height: auto; }

        @keyframes float {
            0% { transform: translate3d(0, 0, 0) rotate(0deg) scale(1); }
            50% { transform: translate3d(0, -18px, 0) rotate(4deg) scale(1.03); }
            100% { transform: translate3d(0, 0, 0) rotate(0deg) scale(1); }
        }
        @keyframes bounce {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
            50% { transform: translate3d(0, -28px, 0) scale(1.08); }
        }
        @keyframes spiral {
            0% { transform: translate3d(0, 0, 0) rotate(0deg) scale(0.96); }
            30% { transform: translate3d(10px, -12px, 0) rotate(110deg) scale(1.02); }
            60% { transform: translate3d(-8px, -20px, 0) rotate(250deg) scale(1.05); }
            100% { transform: translate3d(0, 0, 0) rotate(360deg) scale(0.96); }
        }
        @keyframes sink {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); opacity: 0.78; }
            50% { transform: translate3d(0, 18px, 0) scale(0.94); opacity: 0.5; }
        }

        /* Standardized Vibe Positioning */
        .vibe-1 { top: 12%; left: 7%; --theme-extra-duration: 10s; }
        .vibe-2 { top: 20%; right: 8%; --theme-extra-duration: 12s; --theme-extra-delay: -2s; --theme-extra-direction: reverse; }
        .vibe-3 { bottom: 18%; left: 10%; --theme-extra-duration: 14s; --theme-extra-delay: -1s; }
        .vibe-4 { bottom: 12%; right: 7%; --theme-extra-duration: 11s; --theme-extra-delay: -3s; --theme-extra-direction: alternate; }

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
        [data-theme="intersex"] .intersex-orchid { display: block; }
        [data-theme="yami_kawaii"] .yami-heart { display: block; }
        [data-theme="yami_kawaii"] .yami-skull { display: block; }
        [data-theme="pastel_goth"] .pastel-goth-lace { display: block; }
        [data-theme="pastel_goth"] .pastel-goth-moon { display: block; }
        [data-theme="deep_sea"] .deep-sea-jelly { display: block; }
        [data-theme="deep_sea"] .deep-sea-bloom { display: block; }
        [data-theme="goat"] .goat-horns { display: block; }
        [data-theme="goat"] .goat-bell { display: block; }
        [data-theme="moth"] .moth-luna { display: block; }
        [data-theme="moth"] .moth-dust { display: block; }
        [data-theme="bunny"] .bunny-ears { display: block; }
        [data-theme="bunny"] .bunny-star { display: block; }
        [data-theme="sea_bunny"] .sea-bunny-slug { display: block; }
        [data-theme="sea_bunny"] .sea-bunny-coral { display: block; }

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
        [data-theme="rubber"] .rubber-chain { display: block; }
        [data-theme="rubber"] .rubber-shine { display: block; }
        [data-theme="rope"] .rope-knot { display: block; }
        [data-theme="rope"] .rope-weave { display: block; }
        [data-theme="inflation"] .inflation-orb { display: block; }
        [data-theme="inflation"] .inflation-puff { display: block; }
        [data-theme="vore"] .vore-vine { display: block; }
        [data-theme="vore"] .vore-maw { display: block; }
        [data-theme="werewolf"] .werewolf-moon { display: block; }
        [data-theme="werewolf"] .werewolf-claw { display: block; }
        [data-theme="hypno"] .hypno-spiral { display: block; }
        [data-theme="hypno"] .hypno-eye { display: block; }
        [data-theme="daddy"] .daddy-oak { display: block; }
        [data-theme="daddy"] .daddy-lantern { display: block; }
        [data-theme="hexcorp"] .hexcorp-grid { display: block; }
        [data-theme="hexcorp"] .hexcorp-sigil { display: block; }
        [data-theme="genderfluid"] .genderfluid-stream { display: block; }
        [data-theme="genderfluid"] .genderfluid-drop { display: block; }
        [data-theme="guro"] .guro-crosshair { display: block; }
        [data-theme="guro"] .guro-shard { display: block; }
        [data-theme="living_toilet"] .living-toilet-drip { display: block; }
        [data-theme="living_toilet"] .living-toilet-crown { display: block; }
        [data-theme="parasites"] .parasites-larva { display: block; }
        [data-theme="parasites"] .parasites-worm { display: block; }

        /* Wavy Default Background */
        body:not([data-theme]) .wavy-divider {
            display: block; width: 100%; height: 50px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23161b18' fill-opacity='1' d='M0,128L48,144C96,160,192,192,288,181.3C384,171,480,117,576,112C672,107,768,149,864,165.3C960,181,1056,171,1152,149.3C1248,128,1344,96,1392,80L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover; background-repeat: no-repeat; bottom: -1px; left: 0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; transition: background-color calc(0.5s * var(--anim-speed)) ease, color calc(0.5s * var(--anim-speed)) ease, border-color calc(0.5s * var(--anim-speed)) ease, fill calc(0.5s * var(--anim-speed)) ease, box-shadow calc(0.5s * var(--anim-speed)) ease, border-radius calc(0.45s * var(--anim-speed)) ease; }
        ::selection, *::selection { background: rgba(var(--accent-color-rgb), 0.3); color: var(--text-main); }
        :focus-visible, *:focus-visible { outline: 2px solid var(--accent-color); outline-offset: 3px; }
        html { scrollbar-color: color-mix(in srgb, var(--accent-color) 72%, var(--border-color)) color-mix(in srgb, var(--bg-color) 72%, var(--panel-bg)); }
        ::-webkit-scrollbar { width: 12px; height: 12px; }
        ::-webkit-scrollbar-track { background: color-mix(in srgb, var(--bg-color) 72%, var(--panel-bg)); }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, color-mix(in srgb, var(--accent-color) 78%, var(--panel-bg)), color-mix(in srgb, var(--accent-color) 48%, var(--border-color))); border: 2px solid color-mix(in srgb, var(--bg-color) 72%, var(--panel-bg)); border-radius: var(--radius-btn); }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, var(--accent-color), color-mix(in srgb, var(--accent-color) 58%, var(--border-color))); }
        body { background-color: var(--bg-color); color: var(--text-main); font-family: var(--font-body); overflow-x: hidden; display: flex; flex-direction: column; min-height: 100vh; position: relative; }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: -2;
            background-image: var(--texture-url);
            background-size: 180px 180px;
            opacity: var(--noise-opacity);
            mix-blend-mode: soft-light;
        }
        body.chat-route { height: 100vh; overflow: hidden; }
        .serif { font-family: var(--font-display); font-style: italic; }
        .mono { font-family: var(--font-mono); letter-spacing: max(var(--letter-spacing), 0.05em); }

        #flag-stripe { width: 100%; height: var(--stripe-height); background: var(--stripe-bg); background-size: 200% 200%; animation: var(--stripe-anim) calc(4s * var(--anim-speed)) linear infinite; transition: height calc(0.5s * var(--anim-speed)) ease, background calc(0.5s * var(--anim-speed)) ease; position: relative; z-index: 100; box-shadow: 0 0 0 1px rgba(var(--accent-color-rgb), 0.08), 0 0 18px rgba(var(--accent-color-rgb), 0.16); }
        @keyframes stripe-pulse { 0%, 100% { filter: saturate(1) brightness(1); } 50% { filter: saturate(1.15) brightness(1.12); } }
        @keyframes stripe-shift { 0% { background-position: 0% 50%; } 100% { background-position: 100% 50%; } }
        @keyframes stripe-wave { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
        @keyframes pulse { 0%, 100% { filter: saturate(1) brightness(1); } 50% { filter: saturate(1.15) brightness(1.12); } }
        @keyframes shift { 0% { background-position: 0% 50%; } 100% { background-position: 100% 50%; } }
        @keyframes wave { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
        @keyframes pulse-slow { 0%, 100% { transform: scale(1); opacity: 0.8; } 50% { transform: scale(1.1); opacity: 1; } }
        @keyframes wobble { 0%, 100% { transform: rotate(-10deg); } 50% { transform: rotate(10deg); } }

        /* Common CSS continued */
        nav { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1rem 2rem; font-family: var(--font-body); background: linear-gradient(180deg, rgba(var(--accent-color-rgb), calc(var(--glass-opacity) * 0.28)), transparent 48%), var(--panel-bg); border-top: var(--border-width) var(--border-style) color-mix(in srgb, var(--accent-color) 55%, var(--border-color)); border-bottom: var(--border-width) var(--border-style) var(--border-color); position: sticky; top: 0; z-index: 1000; box-shadow: var(--shadow-card); backdrop-filter: blur(calc(4px + 16px * var(--glass-opacity))); }
        nav::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: max(2px, var(--border-width)); background: linear-gradient(90deg, transparent, var(--accent-color), transparent); opacity: 0.9; pointer-events: none; }
        .logo { display: flex; align-items: center; gap: 0.5rem; font-size: 1.2rem; flex-shrink: 0; }
        .logo-link { text-decoration: none; color: inherit; }
        .logo-circle { width: 24px; height: 24px; border-radius: calc(var(--radius-base) + 8px); background-color: var(--accent-color); color: var(--bg-color); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: bold; font-style: normal; box-shadow: var(--shadow-glow); }
        .app-nav-shell { display: grid; grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr); align-items: center; gap: 1rem; flex: 1; }
        .app-nav-shell--commissioner { grid-template-columns: minmax(0, 1fr) auto; }
        .app-nav-left { justify-content: flex-start; }
        .app-nav-left--commissioner { gap: 0.55rem; flex-wrap: wrap; }
        .app-nav-center { display: flex; justify-content: center; }
        .app-nav-right { display: flex; justify-content: flex-end; align-items: center; gap: 0.75rem; }
        .nav-links { display: flex; gap: 1.25rem; position: relative; align-items: center; }
        .nav-links a { color: var(--text-muted); text-decoration: none; font-size: 0.8rem; text-transform: uppercase; letter-spacing: max(var(--letter-spacing), 0.08em); font-family: var(--font-body); }
        .nav-links a:hover { color: var(--text-main); }
        .nav-quick-link { display: inline-flex; align-items: center; justify-content: center; min-height: 2.35rem; padding: 0.55rem 0.9rem; border-radius: var(--radius-btn); border: var(--border-width) var(--border-style) var(--border-color); background: linear-gradient(180deg, rgba(var(--accent-color-rgb), calc(var(--glass-opacity) * 0.28)), transparent 70%), color-mix(in srgb, var(--bg-color) 52%, var(--panel-bg)); font-size: 0.72rem !important; letter-spacing: max(var(--letter-spacing), 0.08em); box-shadow: var(--shadow-card); }
        .nav-quick-link.is-active { color: var(--text-main); border-color: color-mix(in srgb, var(--accent-color) 48%, var(--border-color)); background: color-mix(in srgb, var(--accent-color) 13%, var(--panel-bg)); box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--accent-color) 18%, transparent), var(--shadow-glow); }
        .nav-menu-dropdown { position: relative; }
        .nav-menu-dropdown summary { list-style: none; }
        .nav-menu-dropdown summary::-webkit-details-marker { display: none; }
        .nav-menu-trigger { cursor: pointer; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: max(var(--letter-spacing), 0.08em); font-family: var(--font-body); }
        .nav-menu-trigger:hover { color: var(--text-main); }
        .nav-menu-panel { position: absolute; top: calc(100% + 0.65rem); left: 0; min-width: 260px; display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; padding: 0.9rem; border-radius: var(--radius-card); border: var(--border-width) var(--border-style) color-mix(in srgb, var(--accent-color) 16%, var(--border-color)); background: linear-gradient(180deg, rgba(var(--accent-color-rgb), calc(var(--glass-opacity) * 0.4)), transparent 35%), var(--panel-bg); box-shadow: var(--shadow-card), var(--shadow-glow); backdrop-filter: blur(calc(4px + 14px * var(--glass-opacity))); }
        .nav-menu-group { display: flex; flex-direction: column; gap: 0.45rem; min-width: 0; }
        .nav-menu-label { font-size: 0.62rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: max(var(--letter-spacing), 0.09em); padding: 0 0.2rem 0.2rem; }
        .nav-menu-link { color: var(--text-main) !important; text-decoration: none; font-size: 0.72rem !important; text-transform: uppercase; padding: 0.7rem 0.8rem; border-radius: calc(var(--radius-base) + 4px); border: var(--border-width) var(--border-style) var(--border-color); background: color-mix(in srgb, var(--bg-color) 34%, var(--panel-bg)); box-shadow: var(--shadow-card); }
        .nav-menu-link:hover { border-color: color-mix(in srgb, var(--accent-color) 35%, var(--border-color)); }
        .free-badge { position: absolute; top: -10px; right: -10px; color: var(--accent-color); font-size: 0.6rem; transform: rotate(15deg); }
        .sign-in { background: linear-gradient(180deg, rgba(var(--accent-color-rgb), calc(var(--glass-opacity) * 0.24)), transparent 65%), transparent; border: var(--border-width) var(--border-style) var(--border-color); color: var(--text-main); padding: 0.55rem 1rem; border-radius: var(--radius-btn); cursor: pointer; font-size: 0.8rem; box-shadow: var(--shadow-card); font-family: var(--font-body); }
        .sign-in:hover { border-color: var(--accent-color); }
        .nav-chat-link { white-space: nowrap; }
        .nav-chat-link--with-badge { position: relative; padding-right: 2.4rem; text-decoration: none; display: inline-flex; align-items: center; }
        .nav-unread-badge { display: none; position: absolute; top: 50%; right: 0.7rem; transform: translateY(-50%); min-width: 1.15rem; height: 1.15rem; padding: 0 0.28rem; border-radius: var(--radius-btn); background: var(--accent-color); color: #000; font-size: 0.65rem; font-weight: bold; line-height: 1.15rem; text-align: center; box-shadow: var(--shadow-glow); }
        .nav-profile-menu { position: relative; }
        .nav-profile-menu summary { list-style: none; }
        .nav-profile-menu summary::-webkit-details-marker { display: none; }
        .nav-profile-trigger { display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.3rem; border-radius: var(--radius-btn); border: var(--border-width) var(--border-style) var(--border-color); background: color-mix(in srgb, var(--bg-color) 55%, var(--panel-bg)); box-shadow: var(--shadow-card); }
        .nav-profile-badge { font-size: 0.78rem; font-weight: bold; color: var(--accent-color); background: var(--accent-dim); border: var(--border-width) var(--border-style) color-mix(in srgb, var(--accent-color) 48%, var(--border-color)); padding: 0.45rem 0.8rem; border-radius: var(--radius-btn); box-shadow: var(--shadow-glow); }
        .nav-profile-caret { color: var(--text-muted); padding-right: 0.25rem; }
        .nav-profile-dropdown { position: absolute; top: calc(100% + 0.55rem); right: 0; min-width: 240px; display: flex; flex-direction: column; gap: 0.55rem; padding: 0.85rem; border-radius: var(--radius-card); border: var(--border-width) var(--border-style) color-mix(in srgb, var(--accent-color) 16%, var(--border-color)); background: linear-gradient(180deg, rgba(var(--accent-color-rgb), calc(var(--glass-opacity) * 0.4)), transparent 35%), var(--panel-bg); box-shadow: var(--shadow-card), var(--shadow-glow); backdrop-filter: blur(calc(4px + 14px * var(--glass-opacity))); }
        .nav-profile-item { color: var(--text-main); text-decoration: none; font-size: 0.72rem; text-transform: uppercase; padding: 0.65rem 0.75rem; border-radius: calc(var(--radius-base) + 4px); border: var(--border-width) var(--border-style) var(--border-color); background: color-mix(in srgb, var(--bg-color) 34%, var(--panel-bg)); box-shadow: var(--shadow-card); letter-spacing: max(var(--letter-spacing), 0.06em); }
        .nav-profile-item:hover { border-color: color-mix(in srgb, var(--accent-color) 35%, var(--border-color)); }
        .nav-profile-item-button { width: 100%; text-align: left; }
        .nav-profile-label { font-size: 0.64rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: max(var(--letter-spacing), 0.08em); padding: 0 0.2rem; }
        .nav-theme-select { width: 100%; font-size: 0.7rem; }

        .theme-select-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .theme-select-row { display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; }
        .theme-select--compact { font-size: 0.7rem; padding: 0.3rem 1.5rem 0.3rem 0.6rem; }
        .theme-select { background: linear-gradient(180deg, rgba(var(--accent-color-rgb), calc(var(--glass-opacity) * 0.18)), transparent 70%), var(--panel-bg); color: var(--text-main); border: var(--border-width) var(--border-style) var(--border-color); padding: 0.4rem 0.8rem; border-radius: calc(var(--radius-base) + 4px); outline: none; cursor: pointer; font-size: 0.7rem; text-transform: uppercase; letter-spacing: max(var(--letter-spacing), 0.05em); font-family: var(--font-body); appearance: none; padding-right: 2rem; box-shadow: var(--shadow-card); }
        .theme-select:focus { border-color: var(--accent-color); }
        .theme-select optgroup { background: var(--bg-panel); color: var(--text-muted); font-weight: bold; }
        .theme-select option { background: var(--bg-panel); color: var(--text-main); }
        .mature-toggle { display: inline-flex; align-items: center; gap: 0.4rem; color: var(--text-muted); font-size: 0.64rem; text-transform: uppercase; letter-spacing: max(var(--letter-spacing), 0.06em); cursor: pointer; user-select: none; }
        .mature-toggle input { accent-color: var(--accent-color); }
        .extreme-themes-optgroup { display: none; }
        .nav-links--guest { flex: 1; display: flex; justify-content: flex-end; margin-right: 2rem; }
        .nav-theme-group--guest { margin-right: 1.5rem; }
        .logout-form { margin: 0; }

        .hero { display: flex; min-height: 60vh; border-bottom: var(--border-width) var(--border-style) var(--border-color); position: relative; }
        .hero-left { flex: 1; padding: 4rem; display: flex; flex-direction: column; justify-content: center; position: relative; }
        .pill { display: inline-flex; align-items: center; gap: 0.5rem; background: var(--accent-dim); color: var(--accent-color); padding: 0.25rem 0.75rem; border-radius: var(--radius-btn); font-size: 0.8rem; width: fit-content; margin-bottom: 2rem; border: var(--border-width) var(--border-style) var(--accent-color); box-shadow: var(--shadow-glow); letter-spacing: max(var(--letter-spacing), 0.05em); }
        .dot { width: 6px; height: 6px; background: var(--accent-color); border-radius: 50%; animation: signal-pulse calc(2s * var(--anim-speed)) infinite; }
        @keyframes signal-pulse { 0% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.5); opacity: 0.5; } 100% { transform: scale(1); opacity: 1; } }
        h1, h2, h3, .pillar-title, .stat-num, .manifesto-text span { font-family: var(--font-display); font-weight: var(--font-weight-heading); letter-spacing: var(--letter-spacing); }
        h1 { font-size: 4rem; line-height: 1.1; margin-bottom: 1.5rem; font-weight: var(--font-weight-heading); z-index: 10; position: relative; text-transform: uppercase; }
        h1 span.light { color: var(--text-main); }
        h1 span.dim { color: var(--text-muted); }
        h1 span.highlight { color: var(--accent-color); }
        .hero-text { color: var(--text-muted); font-size: 1.2rem; max-width: 400px; margin-bottom: 2.5rem; line-height: 1.5; z-index: 10; position: relative;}
        .btn-group { display: flex; gap: 1rem; z-index: 10; position: relative;}
        .btn { padding: 0.8rem 1.5rem; border-radius: var(--radius-btn); cursor: pointer; font-size: 0.9rem; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; border: var(--border-width) var(--border-style) transparent; box-shadow: var(--shadow-card); letter-spacing: max(var(--letter-spacing), 0.04em); text-transform: uppercase; }
        .btn-primary { background: var(--accent-color); color: #000; border-color: transparent; font-weight: bold; }
        .btn-primary:hover .arrow { transform: translateX(4px); }
        .arrow { transition: transform calc(0.2s * var(--anim-speed)); display: inline-block; }
        .btn-ghost { background: transparent; border-color: var(--text-muted); color: var(--text-main); }
        .btn-ghost:hover { border-color: var(--text-main); }
        .hero-right { flex: 1; background: var(--panel-bg); position: relative; overflow: hidden; border-left: var(--border-width) var(--border-style) var(--border-color); }
        .hero-right::before { content: ''; position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 20px 20px; z-index: 1; }
        .hero-right::after { content: ''; position: absolute; inset: 0; background: radial-gradient(circle, transparent 20%, var(--bg-panel) 90%); z-index: 2; }
        canvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; }
        .canvas-label { position: absolute; bottom: 2rem; left: 2rem; z-index: 10; }
        .canvas-label-title { font-size: 0.7rem; color: var(--accent-color); margin-bottom: 0.5rem; display: block; }
        .canvas-quote { color: var(--text-muted); font-size: 0.9rem; opacity: 0.7; }
        .ticker-wrap { width: 100%; overflow: hidden; background: var(--bg-color); border-bottom: var(--border-width) var(--border-style) var(--border-color); padding: 0.8rem 0; white-space: nowrap; }
        .ticker-content { display: inline-block; animation: ticker calc(30s * var(--anim-speed)) linear infinite; font-size: 0.7rem; color: var(--text-muted); }
        @keyframes ticker { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .ticker-item { display: inline-flex; align-items: center; margin-right: 2rem; }
        .ticker-diamond { color: var(--accent-color); margin-left: 2rem; font-size: 0.6rem; }
        .pillars { display: flex; background: var(--bg-color); border-bottom: var(--border-width) var(--border-style) var(--border-color); gap: 1rem; padding: 1rem; }
        .pillar { flex: 1; padding: 3rem 2rem; border: var(--border-width) var(--border-style) var(--border-color); border-radius: var(--radius-card); position: relative; background: var(--panel-bg); box-shadow: var(--shadow-card); }
        .pillar:last-child { border-right: none; }
        .pillar:hover { background-color: var(--bg-panel); box-shadow: var(--shadow-card), var(--shadow-glow); }
        .pillar-dot { width: 6px; height: 6px; background: var(--accent-color); border-radius: 50%; position: absolute; top: 1.5rem; left: 1.5rem; }
        .pillar-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; margin-top: 1rem; }
        .pillar-num { font-size: 0.8rem; color: var(--text-muted); }
        .pillar-title { font-size: 1.1rem; color: var(--text-main); }
        .pillar-text { color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; }
        
        /* Human Translation Styles */
        .human-translation { margin-top: 1.2rem; font-size: 0.65rem; color: var(--accent-color); line-height: 1.4; border-top: var(--border-width) dashed rgba(255,255,255,0.1); padding-top: 0.8rem; text-transform: uppercase; letter-spacing: max(var(--letter-spacing), 0.06em); }
        .human-translation span { color: var(--text-muted); font-family: var(--font-body); letter-spacing: 0; text-transform: none; font-size: 0.8rem; display: block; margin-top: 0.3rem; }

        .manifesto { padding: 6rem 4rem; background: var(--panel-bg); position: relative; overflow: hidden; display: flex; gap: 4rem; }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 20vw; color: rgba(255,255,255,0.02); z-index: 0; pointer-events: none; font-family: var(--font-display); }
        .manifesto-left { flex: 1; z-index: 1; }
        .manifesto-label { font-size: 0.7rem; color: var(--text-muted); margin-bottom: 2rem; display: block; letter-spacing: max(var(--letter-spacing), 0.08em); text-transform: uppercase; }
        .manifesto-text { font-size: 1.5rem; line-height: 1.6; color: var(--text-muted); }
        .manifesto-text span { color: var(--accent-color); }
        .manifesto-right { flex: 1; z-index: 1; display: flex; flex-direction: column; gap: 2rem; justify-content: center; }
        .stat { padding: 1.25rem 1.5rem; border: var(--border-width) var(--border-style) color-mix(in srgb, var(--accent-color) 28%, var(--border-color)); border-left: 4px solid var(--accent-color); border-radius: var(--radius-card); background: color-mix(in srgb, var(--bg-color) 25%, var(--panel-bg)); box-shadow: var(--shadow-card); }
        .stat-num { font-size: 2rem; color: var(--text-main); margin-bottom: 0.2rem; }
        .stat-desc { color: var(--text-muted); font-size: 0.9rem; }
        
        /* The Switcher */
        .mode-switcher {
            display: flex;
            align-items: stretch;
            background: linear-gradient(180deg, color-mix(in srgb, var(--bg-color) 65%, var(--bg-panel)), var(--bg-panel));
            border: var(--border-width) var(--border-style) color-mix(in srgb, var(--accent-color) 18%, var(--border-color));
            border-radius: var(--radius-card);
            padding: 4px;
            position: relative;
            overflow: hidden;
            width: max-content;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.04), var(--shadow-card);
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
            border: var(--border-width) var(--border-style) color-mix(in srgb, var(--accent-color) 50%, var(--border-color));
            border-radius: calc(var(--radius-base) + 6px);
            box-shadow: var(--shadow-card), inset 0 1px 0 rgba(255,255,255,0.16);
            transition: transform calc(0.52s * var(--anim-speed)) cubic-bezier(0.16, 1, 0.3, 1), background calc(0.3s * var(--anim-speed)) ease, box-shadow calc(0.3s * var(--anim-speed)) ease, filter calc(0.3s * var(--anim-speed)) ease;
            z-index: 1;
            pointer-events: none;
            transform-origin: center center;
        }
        .mode-switcher.is-switching .mode-slider {
            box-shadow: 0 12px 24px rgba(0,0,0,0.2), 0 0 0 1px color-mix(in srgb, var(--accent-color) 16%, transparent), inset 0 1px 0 rgba(255,255,255,0.2);
            filter: saturate(1.12) brightness(1.04);
            animation: mode-slider-pop calc(380ms * var(--anim-speed)) cubic-bezier(0.16, 1, 0.3, 1);
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
            transition: color calc(0.25s * var(--anim-speed)) ease, transform calc(0.22s * var(--anim-speed)) ease, opacity calc(0.2s * var(--anim-speed)) ease, filter calc(0.22s * var(--anim-speed)) ease;
            display: flex;
            align-items: center;
            gap: 0.55rem;
            border-radius: calc(var(--radius-base) + 6px);
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
            animation: mode-btn-sheen calc(420ms * var(--anim-speed)) cubic-bezier(0.22, 1, 0.36, 1);
        }
        .mode-btn.is-clicked .mode-btn-icon {
            animation: mode-icon-flip calc(380ms * var(--anim-speed)) cubic-bezier(0.2, 0.9, 0.2, 1);
        }
        .mode-btn.active {
            color: var(--text-main);
            font-weight: bold;
        }
        .mode-btn-icon {
            width: 1.45rem;
            height: 1.45rem;
            border-radius: calc(var(--radius-base) + 4px);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: color-mix(in srgb, var(--bg-panel) 85%, transparent);
            border: var(--border-width) var(--border-style) color-mix(in srgb, var(--accent-color) 20%, var(--border-color));
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
            letter-spacing: max(var(--letter-spacing), 0.08em);
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
            background: var(--panel-bg);
            border: var(--border-width) var(--border-style) color-mix(in srgb, var(--accent-color) 24%, var(--border-color));
            border-left: 4px solid var(--accent-color);
            color: var(--text-main);
            padding: 1rem 1.5rem;
            border-radius: var(--radius-card);
            font-size: 0.85rem;
            z-index: 9999;
            box-shadow: var(--shadow-card), var(--shadow-glow);
            transform: translateY(150%);
            opacity: 0;
            transition: transform calc(0.6s * var(--anim-speed)) cubic-bezier(0.68, -0.55, 0.265, 1.55), opacity calc(0.5s * var(--anim-speed)) ease;
        }
        .toast-notification.show {
            transform: translateY(0);
            opacity: 1;
        }
        .toast-notification span.diamond {
            color: var(--accent-color);
            margin-right: 0.5rem;
        }
        .request-drawer-card { text-decoration: none; color: inherit; background: color-mix(in srgb, var(--bg-color) 56%, var(--panel-bg)); border: var(--border-width) var(--border-style) var(--border-color); border-radius: var(--radius-card); padding: 0.95rem; display: flex; gap: 0.85rem; justify-content: space-between; align-items: flex-start; box-shadow: var(--shadow-card); }
        .request-drawer-meta { font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.3rem; }
        .request-drawer-title { font-weight: 600; margin-bottom: 0.25rem; font-family: var(--font-body); }
        .request-drawer-body { font-size: 0.85rem; color: var(--text-muted); line-height: 1.5; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .request-drawer-side { display: flex; flex-direction: column; align-items: flex-end; gap: 0.4rem; }
        .request-drawer-badge { min-width: 1.25rem; height: 1.25rem; padding: 0 0.35rem; border-radius: var(--radius-btn); background: var(--accent-color); color: #000; font-size: 0.65rem; font-weight: bold; line-height: 1.25rem; text-align: center; box-shadow: var(--shadow-glow); }
        .request-drawer-time { font-size: 0.65rem; color: var(--text-muted); white-space: nowrap; }

        footer { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; background: var(--bg-color); border-top: var(--border-width) var(--border-style) var(--border-color); font-size: 0.7rem; color: var(--text-muted); }
        .footer-links { display: flex; gap: 1.5rem; }
        .footer-links a { color: var(--text-muted); text-decoration: none; }
        .footer-links a:hover { color: var(--accent-color); }
        @media (max-width: 1100px) {
            .app-nav-shell { grid-template-columns: 1fr; }
            .app-nav-left, .app-nav-center, .app-nav-right { justify-content: flex-start; }
            nav { align-items: flex-start; }
        }
        @media (max-width: 900px) { .hero, .pillars, .manifesto { flex-direction: column; } .hero-right { min-height: 300px; border-left: none; border-top: var(--border-width) var(--border-style) var(--border-color); } .pillar { border-right: none; border-bottom: var(--border-width) var(--border-style) var(--border-color); } }
        @media (max-width: 720px) {
            nav { padding: 0.9rem 1rem; }
            .app-nav-right { flex-wrap: wrap; }
            .nav-profile-dropdown { left: 0; right: auto; min-width: min(280px, calc(100vw - 2rem)); }
            .nav-menu-panel { grid-template-columns: 1fr; min-width: min(280px, calc(100vw - 2rem)); }
        }
        body::before { transform-origin: center center; }

        [data-theme="hypno"] body::before { animation: hypno-bg-drift calc(18s * var(--anim-speed)) linear infinite; }
        @keyframes hypno-bg-drift { 0% { background-position: 0% 0%; transform: rotate(0deg) scale(1.1); } 100% { background-position: 100% 100%; transform: rotate(360deg) scale(1.1); } }

        [data-theme="musk"] body::before { animation: musk-bg-drift calc(22s * var(--anim-speed)) ease-in-out infinite alternate; }
        @keyframes musk-bg-drift { 0% { background-position: 0% 0%; } 100% { background-position: 100% 80%; } }

        [data-theme="parasites"] body::before { animation: parasites-bg-wriggle calc(9s * var(--anim-speed)) ease-in-out infinite; }
        @keyframes parasites-bg-wriggle { 0%, 100% { background-position: 0% 0%; } 33% { background-position: 30% 20%; } 66% { background-position: 70% 10%; } }

        [data-theme="vore"] body::before { animation: vore-bg-breathe calc(6s * var(--anim-speed)) ease-in-out infinite; }
        @keyframes vore-bg-breathe { 0%, 100% { transform: scale(1); opacity: var(--noise-opacity); } 50% { transform: scale(1.04); opacity: calc(var(--noise-opacity) * 1.6); } }

        [data-theme="gay"] body::before { animation: gay-bg-shimmer calc(14s * var(--anim-speed)) linear infinite; }
        @keyframes gay-bg-shimmer { 0% { background-position: 0% 50%; } 100% { background-position: 200% 50%; } }

        [data-theme="rubber"] body::before { animation: rubber-bg-crawl calc(16s * var(--anim-speed)) linear infinite; }
        @keyframes rubber-bg-crawl { 0% { background-position: 0% 0%; } 100% { background-position: 100% 0%; } }

        [data-theme="hypno"] body::before { background-size: 120px 120px; }
        [data-theme="musk"] body::before { background-size: 200px 200px; }
        [data-theme="parasites"] body::before { background-size: 80px 80px; }

        body.theme-entering { pointer-events: none; }

        @keyframes hypno-enter { 0% { filter: blur(8px) hue-rotate(180deg); opacity: 0.7; } 100% { filter: blur(0) hue-rotate(0deg); opacity: 1; } }
        body.theme-entering-hypno { animation: hypno-enter calc(0.65s * var(--anim-speed)) ease-out forwards; }

        @keyframes dominant-enter { 0% { filter: brightness(2.2) saturate(0); } 50% { filter: brightness(1.4) saturate(1.2); } 100% { filter: brightness(1) saturate(1); } }
        body.theme-entering-dominant { animation: dominant-enter calc(0.35s * var(--anim-speed)) ease-out forwards; }

        @keyframes rubber-enter { 0% { filter: contrast(3) brightness(2); } 100% { filter: contrast(1) brightness(1); } }
        body.theme-entering-rubber { animation: rubber-enter calc(0.3s * var(--anim-speed)) ease-out forwards; }

        @keyframes inflation-enter { 0% { transform: scale(0.96); filter: brightness(1.2) saturate(1.4); } 60% { transform: scale(1.01); } 100% { transform: scale(1); filter: brightness(1) saturate(1); } }
        body.theme-entering-inflation { animation: inflation-enter calc(0.6s * var(--anim-speed)) cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }

        @keyframes vore-enter { 0% { transform: scale(1.04); filter: brightness(0.6) saturate(1.5); } 100% { transform: scale(1); filter: brightness(1) saturate(1); } }
        body.theme-entering-vore { animation: vore-enter calc(0.5s * var(--anim-speed)) ease-out forwards; }

        @keyframes gay-enter { 0% { filter: saturate(3) brightness(1.3) hue-rotate(-20deg); } 100% { filter: saturate(1) brightness(1) hue-rotate(0deg); } }
        body.theme-entering-gay { animation: gay-enter calc(0.55s * var(--anim-speed)) ease-out forwards; }

        @keyframes trans-enter { 0% { filter: blur(4px) brightness(1.2); } 100% { filter: blur(0) brightness(1); } }
        body.theme-entering-trans { animation: trans-enter calc(0.45s * var(--anim-speed)) ease-out forwards; }

        @keyframes femboy-enter { 0% { transform: scale(1.02) rotate(0.5deg); } 60% { transform: scale(0.995) rotate(-0.2deg); } 100% { transform: scale(1) rotate(0deg); } }
        body.theme-entering-femboy { animation: femboy-enter calc(0.55s * var(--anim-speed)) cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }

        @keyframes parasites-enter { 0% { filter: brightness(0.7) saturate(2.2) hue-rotate(30deg); } 100% { filter: brightness(1) saturate(1) hue-rotate(0deg); } }
        body.theme-entering-parasites { animation: parasites-enter calc(0.5s * var(--anim-speed)) ease-out forwards; }
/* Micro-UX Layer */
        [data-theme="pup"] .btn:hover { animation: pup-wag calc(0.4s * var(--anim-speed)) ease-in-out infinite; }
        @keyframes pup-wag { 0%,100% { transform: rotate(-3deg) scale(1.02); } 50% { transform: rotate(3deg) scale(1.04); } }

        [data-theme="dominant"] .btn:hover { transform: scale(0.97); transition: transform 0.08s linear; }
        [data-theme="dominant"] .btn:active { transform: scale(0.95); }
        [data-theme="dominant"] .btn { transition: transform 0.08s linear, background 0.1s, border-color 0.1s; }

        [data-theme="femboy"] .btn:hover { transform: translateY(-3px) scale(1.03); }
        [data-theme="femboy"] .btn:active { transform: translateY(1px) scale(0.98); }
        [data-theme="femboy"] .btn { transition: transform calc(0.3s * var(--anim-speed)) cubic-bezier(0.34, 1.56, 0.64, 1), background 0.3s, border-color 0.3s; }

        [data-theme="hypno"] .btn:hover { box-shadow: var(--shadow-glow), 0 0 0 3px color-mix(in srgb, var(--accent-color) 30%, transparent), 0 0 24px rgba(var(--accent-color-rgb), 0.4); animation: hypno-btn-pulse calc(2s * var(--anim-speed)) ease-in-out infinite; }
        @keyframes hypno-btn-pulse { 0%,100% { box-shadow: var(--shadow-glow), 0 0 0 3px color-mix(in srgb, var(--accent-color) 20%, transparent); } 50% { box-shadow: var(--shadow-glow), 0 0 0 6px color-mix(in srgb, var(--accent-color) 40%, transparent), 0 0 32px rgba(var(--accent-color-rgb), 0.5); } }

        [data-theme="inflation"] .btn:hover { transform: scale(1.06); transition: transform calc(0.5s * var(--anim-speed)) cubic-bezier(0.34, 1.56, 0.64, 1); box-shadow: var(--shadow-glow), 0 8px 24px rgba(var(--accent-color-rgb), 0.28); }
        [data-theme="inflation"] .btn:active { transform: scale(0.97); transition: transform 0.12s ease; }

        [data-theme="rubber"] .btn:hover { animation: rubber-squeeze calc(0.3s * var(--anim-speed)) ease-out forwards; }
        @keyframes rubber-squeeze { 0% { transform: scaleX(1) scaleY(1); } 30% { transform: scaleX(0.92) scaleY(1.08); } 100% { transform: scaleX(1) scaleY(1); } }

        [data-theme="vore"] .btn:active { animation: vore-absorb calc(0.35s * var(--anim-speed)) ease-in; }
        @keyframes vore-absorb { 0% { transform: scale(1); filter: brightness(1); } 50% { transform: scale(0.93); filter: brightness(0.7) saturate(2); } 100% { transform: scale(1); filter: brightness(1); } }

        [data-theme="submissive"] .btn:hover { transform: translateY(2px) scale(0.98); opacity: 0.88; }

        [data-theme="werewolf"] .btn:hover { transform: scale(1.04); filter: brightness(1.12); transition: transform calc(0.15s * var(--anim-speed)) ease-out; }

        /* note: custom cursor images would need SVG data URIs - use the best available CSS cursors */
        [data-theme="dominant"] { cursor: crosshair; }
        [data-theme="dominant"] .btn, [data-theme="dominant"] a { cursor: crosshair; }
        [data-theme="pup"] { cursor: default; }
        [data-theme="hypno"] * { cursor: cell; }
        [data-theme="vore"] * { cursor: zoom-in; }
        [data-theme="femboy"] .btn { cursor: pointer; }
        [data-theme="guro"] { cursor: crosshair; }
        [data-theme="rope"] { cursor: grab; }
        [data-theme="rope"] *:active { cursor: grabbing; }

        :root ::selection { background: rgba(var(--accent-color-rgb), 0.3); color: var(--text-main); }
        [data-theme="gay"] ::selection { background: rgba(125, 65, 217, 0.5); color: #fff; }
        [data-theme="trans"] ::selection { background: rgba(85, 205, 252, 0.4); color: #fff; }
        [data-theme="lesbian"] ::selection { background: rgba(214, 41, 0, 0.45); color: #fff; }
        [data-theme="dominant"] ::selection { background: rgba(204, 0, 0, 0.65); color: #fff; }
        [data-theme="femboy"] ::selection { background: rgba(245, 169, 184, 0.5); color: #111; }
        [data-theme="hypno"] ::selection { background: rgba(138, 125, 255, 0.6); color: #fff; }
        [data-theme="rubber"] ::selection { background: rgba(255, 255, 255, 0.85); color: #000; }
        [data-theme="inflation"] ::selection { background: rgba(255, 183, 223, 0.5); color: #111; }
        [data-theme="vore"] ::selection { background: rgba(107, 184, 111, 0.55); color: #0d170f; }
        [data-theme="musk"] ::selection { background: rgba(181, 141, 61, 0.45); color: #fff; }
        [data-theme="pup"] ::selection { background: rgba(43, 92, 255, 0.5); color: #fff; }
        [data-theme="guro"] ::selection { background: rgba(141, 15, 34, 0.55); color: #fff; }
        [data-theme="parasites"] ::selection { background: rgba(134, 196, 83, 0.5); color: #0d170f; }

        html { scrollbar-width: thin; scrollbar-color: var(--border-color) transparent; }
        html::-webkit-scrollbar { width: 7px; }
        html::-webkit-scrollbar-track { background: transparent; }
        html::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 999px; }
        html::-webkit-scrollbar-thumb:hover { background: color-mix(in srgb, var(--accent-color) 50%, var(--border-color)); }

        [data-theme="dominant"] { scrollbar-color: #cc0000 #0b0a0a; }
        [data-theme="dominant"]::-webkit-scrollbar-thumb { background: #cc0000; border-radius: 0; }

        [data-theme="rubber"] { scrollbar-color: #f1f1f1 #111; }
        [data-theme="rubber"]::-webkit-scrollbar { width: 4px; }
        [data-theme="rubber"]::-webkit-scrollbar-thumb { background: #f1f1f1; border-radius: 0; }

        [data-theme="femboy"] { scrollbar-color: #f5a9b8 #1b1e22; }
        [data-theme="femboy"]::-webkit-scrollbar-thumb { border-radius: 999px; }

        [data-theme="hypno"] { scrollbar-color: #8a7dff #0d1024; }
        [data-theme="hypno"]::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #8a7dff, #3d2fa4); }

        [data-theme="vore"]::-webkit-scrollbar-thumb { background: #375a2e; }

        [data-theme="musk"]::-webkit-scrollbar-thumb { background: #b58d3d; }

        [data-theme="inflation"]::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #ffb7df, #bde7ff); border-radius: 999px; }

        [data-theme="parasites"]::-webkit-scrollbar-thumb { background: #86c453; border-radius: 4px; }

        :focus-visible { outline: 2px solid var(--accent-color); outline-offset: 3px; border-radius: calc(var(--radius-base) + 2px); }
        [data-theme="dominant"] :focus-visible { outline-color: #cc0000; border-radius: 0; outline-width: 3px; }
        [data-theme="rubber"] :focus-visible { outline-color: #f1f1f1; border-radius: 0; outline-style: dashed; }
        [data-theme="hypno"] :focus-visible { outline-color: #8a7dff; box-shadow: 0 0 0 4px rgba(138,125,255,0.25); }
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
            const themeSelectors = Array.from(document.querySelectorAll('[data-theme-selector]'));
            const matureToggles = Array.from(document.querySelectorAll('[data-theme-mature-toggle]'));
            const extremeThemeWrappers = Array.from(document.querySelectorAll('.extreme-themes-optgroup'));
            const extremeThemes = ['guro', 'living_toilet', 'parasites'];
            const currentTheme = localStorage.getItem('atelier_theme') || 'default';
            let themeEntranceTimeout;

            function applyTheme(theme) {
                localStorage.setItem('atelier_theme', theme);
                if (theme === 'default') {
                    document.documentElement.removeAttribute('data-theme');
                } else {
                    document.documentElement.setAttribute('data-theme', theme);
                }
                const body = document.body;
                const enteringClasses = Array.from(body.classList).filter((className) => className === 'theme-entering' || className.startsWith('theme-entering-'));
                if (enteringClasses.length) {
                    body.classList.remove(...enteringClasses);
                }
                body.classList.add('theme-entering', `theme-entering-${theme}`);
                clearTimeout(themeEntranceTimeout);
                themeEntranceTimeout = window.setTimeout(() => {
                    body.classList.remove('theme-entering', `theme-entering-${theme}`);
                }, 700);
                themeSelectors.forEach((selector) => {
                    selector.value = theme;
                });
            }

            function syncExtremeThemes(show) {
                themeSelectors.forEach((selector, index) => {
                    const existing = selector.querySelector('[data-extreme-optgroup]');
                    if (existing) existing.remove();
                    if (!show) return;
                    const template = extremeThemeWrappers[index]?.querySelector('template');
                    if (!template) return;
                    const fragment = template.content.cloneNode(true);
                    const optgroup = fragment.querySelector('optgroup');
                    if (optgroup) {
                        optgroup.setAttribute('data-extreme-optgroup', 'true');
                        selector.appendChild(optgroup);
                    }
                });
            }

            const initialShowExtreme = localStorage.getItem('atelier_show_extreme_themes') === 'true' || extremeThemes.includes(currentTheme);
            syncExtremeThemes(initialShowExtreme);
            matureToggles.forEach((toggle) => {
                toggle.checked = initialShowExtreme;
                toggle.addEventListener('change', (event) => {
                    const show = Boolean(event.target.checked);
                    localStorage.setItem('atelier_show_extreme_themes', show ? 'true' : 'false');
                    syncExtremeThemes(show);
                    matureToggles.forEach((other) => {
                        other.checked = show;
                    });
                    const activeTheme = localStorage.getItem('atelier_theme') || 'default';
                    themeSelectors.forEach((selector) => {
                        selector.value = activeTheme;
                    });
                });
            });

            if (themeSelectors.length) {
                themeSelectors.forEach((selector) => {
                    selector.value = currentTheme;
                    selector.addEventListener('change', (event) => {
                        const theme = event.target.value;
                        if (extremeThemes.includes(theme)) {
                            localStorage.setItem('atelier_show_extreme_themes', 'true');
                            syncExtremeThemes(true);
                            matureToggles.forEach((toggle) => {
                                toggle.checked = true;
                            });
                        }
                        applyTheme(theme);
                    });
                });
                applyTheme(currentTheme);
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
                    <a href="${item.url}" class="request-drawer-card">
                        <div style="min-width:0; flex:1;">
                            <div class="mono request-drawer-meta">${item.otherParty.username ? '/' + item.otherParty.username : 'request thread'} • ${String(item.status).replace('_', ' ')}</div>
                            <div class="request-drawer-title">${item.title}</div>
                            <div class="request-drawer-body">${item.latestMessage ? item.latestMessage.message : 'No messages yet.'}</div>
                        </div>
                        <div class="request-drawer-side">
                            ${item.unread > 0 ? `<span class="mono request-drawer-badge">${item.unread}</span>` : ''}
                            <span class="mono request-drawer-time">${item.updatedHuman}</span>
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
    <script src="{{ asset('js/particles.js') }}"></script>
</body>
</html>
