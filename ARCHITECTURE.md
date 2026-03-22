# Atelier — UI Architecture Redesign

## Goal
Strip all opinionated styling from the UI layer. Build a clean, neutral design system where the *only* variable is the theme. Themes should feel like real personalities — not coats of paint on a broken foundation.

---

## Core Principle: Data / Lens / Display

```
DATA  = Pure application data (artists, commissions, users)
LENS  = Theme tokens (colors, fonts, radius, spacing multiplier)
DISPLAY = Components (buttons, cards, inputs — behave identically regardless of lens)
```

The component never knows what theme is active. The theme never changes component structure.

---

## Layer 1: Design Tokens (CSS Custom Properties)

### Base Tokens — Neutral Foundation

All values are deliberately boring. Black, white, greys, no personality.

```css
:root {
  /* Spacing — 4px base unit */
  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-6: 24px;
  --space-8: 32px;
  --space-12: 48px;
  --space-16: 64px;
  --space-24: 96px;

  /* Typography */
  --font-sans: 'Inter', system-ui, sans-serif;
  --font-serif: 'Georgia', serif;
  --font-mono: 'Courier New', monospace;
  
  --text-xs: 0.75rem;   /* 12px */
  --text-sm: 0.875rem;  /* 14px */
  --text-base: 1rem;    /* 16px */
  --text-lg: 1.125rem;  /* 18px */
  --text-xl: 1.25rem;   /* 20px */
  --text-2xl: 1.5rem;   /* 24px */
  --text-3xl: 1.875rem; /* 30px */
  --text-4xl: 2.25rem; /* 36px */
  --text-5xl: 3rem;     /* 48px */

  /* Borders */
  --radius-sm: 4px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 16px;
  --radius-full: 9999px;

  /* Shadows */
  --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
  --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
  --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
  --shadow-xl: 0 20px 25px rgba(0,0,0,0.1);

  /* Transitions */
  --transition-fast: 150ms ease;
  --transition-base: 200ms ease;
  --transition-slow: 300ms ease;
}
```

### Theme Tokens — The Personality Layer

Themes override only these. Nothing structural.

```css
[data-theme="default"] {
  --color-bg: #0f1210;
  --color-surface: #161b18;
  --color-border: #232b26;
  --color-accent: #2bdc6c;
  --color-text: #f0f4f2;
  --color-muted: #8c9b92;
  
  --theme-radius-mult: 1;
  --theme-font: var(--font-sans);
  --theme-weight: 400;
  --theme-letter: 0;
}

[data-theme="dickgirl-dom"] {
  --color-bg: #0f0a0d;
  --color-surface: #1a1218;
  --color-border: #3d2035;
  --color-accent: #e62e8a;
  --color-text: #fff1f7;
  --color-muted: #c49bb0;
  
  --theme-radius-mult: 0;      /* square everything */
  --theme-font: 'Arial Black', sans-serif;
  --theme-weight: 900;
  --theme-letter: 0.14em;
}

[data-theme="dickgirl-mommy"] {
  --color-bg: #1a0f18;
  --color-surface: #261520;
  --color-border: #4d2a3a;
  --color-accent: #ff85c0;
  --color-text: #fff4fa;
  --color-muted: #d8a8c0;
  
  --theme-radius-mult: 2.5;    /* round everything */
  --theme-font: var(--font-serif);
  --theme-weight: 600;
  --theme-letter: 0.04em;
}
```

### Derived Tokens — Components Use These

Components NEVER reference `--color-bg` directly. They use derived tokens:

```css
:root {
  /* Semantic tokens — what the component actually uses */
  --btn-bg: var(--color-accent);
  --btn-color: var(--color-bg);
  --btn-border: transparent;
  --btn-radius: calc(var(--radius-md) * var(--theme-radius-mult, 1));
  
  --card-bg: var(--color-surface);
  --card-border: var(--color-border);
  --card-radius: calc(var(--radius-lg) * var(--theme-radius-mult, 1));
  --card-shadow: var(--shadow-md);
  
  --input-bg: var(--color-bg);
  --input-border: var(--color-border);
  --input-color: var(--color-text);
  --input-radius: calc(var(--radius-sm) * var(--theme-radius-mult, 1));
  
  --nav-bg: var(--color-surface);
  --nav-border: var(--color-border);
}
```

This means: a theme changes `--color-accent` and `--theme-radius-mult` and the button automatically gets the right bg, color, and border-radius. The component's CSS never changes.

---

## Layer 2: Component Library

Location: `resources/views/components/`

### Core Components (always available)

```
x-button.blade.php        — variant (primary|ghost|secondary), size (sm|md|lg), href|onclick|type
x-card.blade.php          — padding, hoverable, clickable (wraps in <a> or <button>)
x-input.blade.php         — label, name, type, value, placeholder, error, required
x-select.blade.php        — label, name, options[], selected, placeholder
x-textarea.blade.php      — label, name, value, rows, maxlength
x-badge.blade.php         — variant (accent|muted|success|error), size
x-avatar.blade.php        — src, alt, size (sm|md|lg|xl)
x-modal.blade.php         — title, size (sm|md|lg|full), closeable
x-dropdown.blade.php      — trigger slot, content slot
x-tabs.blade.php          — tabs[], active, route|action
x-empty-state.blade.php   — title, description, action label + route
x-skeleton.blade.php      — variant (text|avatar|card), lines
```

### Domain Components (Atelier-specific)

```
x-artist-card.blade.php   — artist, variant (list|grid|compact)
x-commission-slot.blade.php — slots, max, status
x-gallery-grid.blade.php   — images[], layout (grid|featured|masonry)
x-profile-module.blade.php — module, zone, editable
x-nav-link.blade.php      — href, active, label, badge?
x-commission-card.blade.php — commission, status
x-follow-button.blade.php  — artist, following
```

### Component Contract

Every component:
- Accepts a `class` prop for extension
- Never hardcodes colors — uses semantic tokens
- Uses spacing scale only
- Has a single `@doc` comment block explaining props
- Works identically under every theme

---

## Layer 3: File Structure

```
resources/
  css/
    tokens.css          ← all CSS custom properties (base + themes)
    base.css             ← reset, html/body, scrollbar, selection, focus
    components.css       ← all component base styles
    themes/
      default.css
      dickgirl-dom.css
      dickgirl-mommy.css
      ... (per-theme overrides only, no full redeclarations)

  views/
    components/          ← all x-* blade components
    layouts/
      app.blade.php      ← thin layout: loads CSS, yields content
      blank.blade.php    ← auth views, fullscreen pages
    pages/               ← or keep at root — browse.blade.php, etc.
```

---

## Layer 4: View Restructuring

### browse.blade.php

```
- Remove ALL inline CSS (the 500-line <style> block)
- Remove ALL @if($isDomTheme) / @if($isMommyTheme) layout branches
- Use <x-artist-card variant="list"> for each artist
- Search form: <x-form> wrapping <x-input>, <x-select>, <x-button>
- Results bar: pure HTML, spacing tokens only
```

### welcome.blade.php

```
- Hero: <section> with grid layout, <x-button> for CTAs
- Remove canvas particles (or move to optional JS layer)
- Remove ticker animation
- Pillars: <x-card> grid
- Manifesto: semantic HTML, no watermark, no floating elements
```

### Navigation (partials/nav.blade.php)

```
- Keep HTML structure, remove all inline styles
- Replace class names: .nav-quick-link → <x-nav-link>
- Mode switcher: <x-tabs> or <x-button-group>
- Theme selector: <x-select> with optgroups
- Use flex/grid with spacing tokens only
```

### Profile Pages

```
- Modules use <x-profile-module> component
- Layout grid: CSS grid with gap tokens only
- Each module type (avatar, bio, slots, gallery) is its own x-* component
- Remove edit-drawer inline CSS → component styles in components.css
```

### Workspace

```
- Keep the canvas/board system (it's genuinely functional)
- Extract CSS to components.css or a workspace.css partial
- Remove inline style attributes
```

---

## Layer 5: Theme System (Simplified)

### Current State (broken)
- 35 themes × 40+ CSS custom properties = 1,400+ overrides
- Themes redefine fonts, borders, animations, cursors, scrollbars
- Themes change HTML structure in browse.blade.php

### Target State
- ~20 base tokens that themes override
- Themes ONLY override tokens — no structural CSS
- Identity system stays (viewer gender → language tone) but themes don't branch HTML
- Extreme themes (guro, etc.) opt-in via toggle

### Theme Manifest (simplified)

```json
{
  "name": "Dickgirl Dom",
  "tokens": {
    "color-accent": "#e62e8a",
    "color-bg": "#0f0a0d",
    "theme-radius-mult": "0",
    "theme-font": "'Arial Black', sans-serif",
    "theme-weight": "900",
    "theme-letter": "0.14em"
  },
  "identity": {
    "required": true,
    "greeting": {
      "male": "Little One, reporting for duty.",
      "female": "Hello, princess."
    }
  }
}
```

---

## Implementation Phases

### Phase 1: Foundation (this session)
- [ ] Write `tokens.css` with all base + theme token sets
- [ ] Write `base.css` (reset, body, scrollbar, focus)
- [ ] Build core components: x-button, x-card, x-input, x-select, x-badge, x-avatar
- [ ] Update `app.blade.php` layout to load CSS files (no inline styles)

### Phase 2: Components
- [ ] Build domain components: x-artist-card, x-commission-slot, x-gallery-grid
- [ ] Build navigation components: x-nav-link, x-mode-switcher
- [ ] Build form components: x-form, x-textarea, x-empty-state

### Phase 3: Pages
- [ ] Rewrite browse.blade.php using components (no inline CSS)
- [ ] Rewrite welcome.blade.php using components
- [ ] Rewrite nav.blade.php using components
- [ ] Rewrite commission/create.blade.php using form components

### Phase 4: Profile + Workspace
- [ ] Profile modules → x-* components
- [ ] Workspace CSS extraction
- [ ] Auth views (login, register, onboard) using component library

### Phase 5: Theme Manifest Cleanup
- [ ] Convert all 35 theme CSS blocks to token overrides in themes/*.css
- [ ] Remove ~2,000 lines of inline CSS from app.blade.php
- [ ] Verify all themes work with new component library

---

## Naming Conventions

| Thing | Convention | Example |
|---|---|---|
| Components | kebab-case | `x-artist-card`, `x-follow-button` |
| CSS classes | kebab-case | `.artist-card`, `.commissions-grid` |
| CSS custom props | kebab-case | `--color-accent`, `--space-4` |
| Blade sections | kebab-case | `@section('content')` |
| JS data attributes | kebab-case | `data-follow-btn`, `data-artist-id` |

## No-Go Zones

- No inline `<style>` in blade files (exceptions: email templates, PDF)
- No `style="..."` attributes on HTML elements (use CSS classes)
- No `class="btn btn-primary"` — use `<x-button variant="primary">`
- No hardcoded colors — use semantic tokens
- No animation properties in component CSS — themes can add via `--transition-*`

---

*Last updated: 2026-03-20 — Riley*
