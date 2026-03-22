# Atelier — Site Documentation

*Generated 2026-03-20. Riley writing. This is how the site works as of today.*

---

## What is Atelier?

Atelier is a commission platform built for artists — particularly ones that mainstream platforms shadowban or discriminate against (furry, NSFW, niche, queer communities). It is **0% platform cut**, artist-run, and designed around actual artist workflows rather than corporate monetization.

The core loop: **client browses artists → requests a commission → artist accepts/declines → they collaborate via chat + workspace → commission completes.**

---

## Architecture Overview

### Tech Stack
- **Laravel 13** on **PHP 8.3**
- **PostgreSQL** (via Docker, `saas-pgsql-1`)
- **Redis** for caching/sessions
- **Vite** for asset bundling
- **Tailwind** for utility CSS + custom CSS variables per theme
- **Resend** for transactional emails

### Project Structure
```
dev/SaaS/
├── app/
│   ├── Enums/           # UserRole, CommissionRequest statuses
│   ├── Http/Controllers/
│   │   ├── Auth/        # AuthController, RoleSwitchController
│   │   ├── Commission/  # WorkspaceController, CommissionRequestController
│   │   ├── ArtistProfileController.php
│   │   ├── ConversationController.php
│   │   ├── DashboardController.php
│   │   ├── FollowController.php
│   │   ├── GalleryController.php
│   │   ├── IdentityController.php    ← NEW: identity selector
│   │   ├── ThemeController.php
│   │   └── (more)
│   ├── Models/          # User, CommissionRequest, Conversation, WorkspaceItem, ProfileModule, ProfileConfig, etc.
│   ├── Services/        # ThemeManifest.php ← handles theme resolution + identity-aware strings
│   └── Providers/       # ThemeServiceProvider ← shares theme data to all views
├── config/
├── database/
│   └── migrations/      # 19+ migrations
├── resources/
│   ├── themes/          # Each theme has a manifest.json + CSS block in layouts/app.blade.php
│   │   ├── default/
│   │   ├── rubber/
│   │   ├── guro/
│   │   ├── dickgirl-dom/     ← NEW
│   │   └── dickgirl-mommy/   ← NEW
│   └── views/
│       ├── browse.blade.php          ← themed
│       ├── identity/                 ← NEW: select.blade.php
│       ├── profile/
│       │   ├── show.blade.php        ← profile page
│       │   ├── modules/              ← profile module views (avatar, bio, gallery, etc.)
│       │   │   ├── avatar_info.blade.php   ← themed
│       │   │   ├── bio.blade.php          ← themed
│       │   │   ├── comm_slots.blade.php   ← themed
│       │   │   ├── gallery_feed.blade.php
│       │   │   ├── links.blade.php
│       │   │   ├── banner.blade.php
│       │   │   ├── kanban_tracker.blade.php
│       │   │   ├── text_block.blade.php
│       │   │   └── tip_jar.blade.php
│       │   └── partials/
│       ├── commission/
│       │   ├── create.blade.php      ← needs theming
│       │   └── (show.blade.php)
│       ├── conversations/
│       │   ├── index.blade.php       ← needs theming
│       │   └── (show.blade.php)
│       ├── layouts/app.blade.php     ← master layout, all theme CSS vars defined here
│       ├── partials/nav.blade.php    ← navigation, theme switcher dropdown
│       └── welcome.blade.php          ← landing page, needs theming
└── routes/web.php
```

---

## The Theme System

### How Themes Work (Current)

Themes are defined in two places:

1. **`resources/themes/{theme}/manifest.json`** — Language strings, layout preferences, component styles. Example:
   ```json
   {
     "name": "Dickgirl Dom",
     "requires_identity": true,
     "identity": {
       "male": { "treatment": "teased", "pronoun": "he", "label": "Little One" },
       "female": { "treatment": "bred", "pronoun": "she", "label": "Breeding Mare" },
       "dickgirl": { "treatment": "respected", "pronoun": "she", "label": "Equal" },
       "other": { "treatment": "handled", "pronoun": "they", "label": "Guest" }
     },
     "language": {
       "buttons": { "default": "Come Here", "hover": "Earn It" },
       "identity_aware": {
         "greeting_male": "Look at you. Finally found what you were looking for.",
         "browse_male": "Find someone who'll put you in your place."
       }
     }
   }
   ```

2. **`resources/views/layouts/app.blade.php`** — CSS custom properties for each theme in a `<style>` block. Example:
   ```css
   [data-theme="dickgirl-dom"] {
     --bg-color: #0f0a0d;
     --accent-color: #e62e8a;
     --radius-base: 0px;
     --radius-card: 0px;
     --font-display: "Arial Black", "Impact", sans-serif;
     --letter-spacing: 0.14em;
     --shadow-card: 0 24px 0 rgba(230, 46, 138, 0.2), 0 28px 60px rgba(0,0,0,0.6);
     /* experiential vars */
     --hover-compression: 0.97;
     --surface-shine: 0.12;
   }
   ```

The active theme is stored in **session** (`session('theme')`) and optionally persisted to the **user's `theme` column**. The `ThemeManifest` service resolves it, caches it, and provides helpers.

### The `@theme()` Blade Directive

Used in templates to pull language strings from the manifest:
```blade
<button>@theme('language.buttons.default')</button>
<button>@theme('language.buttons.hover')</button>
```

### The `@identityAware()` Blade Directive

Used for identity-aware strings:
```blade
{{ @identityAware('greeting') }}
{{ @identityAware('browse') }}
```

This resolves `identity_aware.{key}_{identity}` from the manifest — so if identity is "male", it looks for `greeting_male`, falls back to `greeting_other`, then to the default.

### Key ThemeManifest Methods
```php
$theme->get('key')               // Get any manifest value
$theme->getTheme()               // Current theme name
$theme->requiresIdentity()       // Does this theme require identity selection?
$theme->identityAware('key', $identity, $default)  // Identity-aware string
$theme->getIdentityLabel($identity)  // "Little One", "Breeding Mare", etc.
$theme->getIdentityTreatment($identity)  // "teased", "bred", etc.
$theme->viewerIdentity()          // Current viewer's identity from session/user
```

### Active Themes (as of today)
| Theme | Key traits | Requires identity |
|-------|-----------|-------------------|
| `default` | Clean, neutral, data-forward | No |
| `rubber` | Tight, metallic, iridescent accents | No |
| `guro` | Raw, textured, brutalist | No |
| `dickgirl-dom` | Hard-edged, commanding, zero border-radius | **Yes** |
| `dickgirl-mommy` | Warm, rounded, generous spacing | **Yes** |
| Plus 30+ other themes | Various subcultures | No |

---

## User Roles

Three roles:
- **`commissioner`** — Browse, request commissions, chat with artists
- **`artist`** — Commissioner + can build profile pages, manage commissions, use the Atelier workspace
- **`admin`** — Commissioner + artist + platform admin dashboard

Artists and admins can **switch modes** (Play/Work) to toggle between the commissioner view and artist view. This is controlled by `active_profile` on the user record.

---

## Database Schema

### Core Tables
- **`users`** — name, email, password, role, active_profile, page_layout, viewer_identity (NEW), theme (NEW)
- **`profile_modules`** — artists attach modules (avatar, bio, gallery, etc.) to their profile. Each module has a `type` and a JSON `settings` field.
- **`followers`** — user_id + follower_id (simple follow relationship)
- **`commission_requests`** — links commissioner to artist for a specific piece. Statuses: pending, accepted, declined, needs_info. Tracks: budget, details, tracker_stage (queue/active/delivery/done), reference_images (JSON)
- **`conversations`** — can be standalone chat OR tied to a commission_request. Fields: kind (direct/commission), participants (JSON), title
- **`conversation_messages`** — messages within a conversation. Fields: user_id, kind (user/system), message, attachments (JSON), references (JSON)
- **`workspace_items`** — artist-side items in the Atelier workspace. Fields: commission_request_id (nullable for manual projects), type (note/asset/group), content, metadata (JSON), sort_order
- **`workspace_connections`** — SVG-style connections between workspace items (from_id → to_id with SVG path data)
- **`profile_configs`** — saved layout configurations that artists can save and load

### Profile Modules (Artist Profile Builder)
Each artist has a set of **profile modules** they arrange via drag-and-drop:
- `avatar_info` — avatar image, name, bio snippet, follow button
- `banner` — large header image
- `bio` — full bio with markdown rendering
- `gallery_feed` — image gallery with layout options (grid/masonry/featured)
- `comm_slots` — open/closed status, slots count, next opening date, pricing CTA
- `kanban_tracker` — commission progress tracker (queue/active/delivery/done)
- `links` — social media / external links
- `text_block` — arbitrary text content
- `tip_jar` — tip jar / donation link

Artists can choose a **page layout**: classic, fixed_left, editorial, stacked, magazine. The profile page (`profile/show.blade.php`) renders modules into these layouts.

---

## Commission Flow

### Step 1: Client Submits Request
- Client goes to `/commission/request/{username}`
- Fills: title, budget, details, optional reference images
- System creates `CommissionRequest` (status: pending) + `Conversation` (kind: commission)
- Reference images are auto-populated into the artist's **Atelier Workspace**

### Step 2: Artist Responds
- Artist sees request in their **Artist Request Inbox** (`/atelier/requests`)
- Actions: Accept / Decline / Needs Info
- Response updates `CommissionRequest.status` + creates a system message in the conversation
- If accepted: client notified, tracker defaults to "queue" stage

### Step 3: Collaboration
- Both parties chat via the conversation thread
- Reference images always visible in a strip above the chat
- Artist updates tracker stage: Queue → Active → Delivery → Done
- System messages record each stage change

### Step 4: Completion
- Tracker reaches "done" stage
- Payment is released (future: via actual payment integration)
- Conversation remains accessible for follow-up

---

## Atelier Workspace

Located at `/atelier/workspace/{commissionRequest?}` — artist's private commission management board.

Features:
- **Draggable cards** per commission request (or manual project)
- **Workspace items** attached to each card: notes, image uploads, groups
- **SVG connections** between items (stored as from_id/to_id/path in DB)
- **Kanban stages** on each card (queue/active/delivery/done)
- Manual projects (not tied to a commission request) also supported

The workspace is **artist-only** — clients never see it.

---

## Identity System (NEW — Dickgirl Themes)

When a user selects `dickgirl-dom` or `dickgirl-mommy`:

1. `ThemeController::switch()` detects `requires_identity: true` in the manifest
2. Redirects to `GET /identity` — the identity selector page
3. User picks: **Male / Female / Dickgirl / Other**
4. Identity stored in: session (`viewer_identity`) + user column if logged in
5. `ThemeManifest` resolves the identity for every request and feeds it to views
6. All identity-aware views get different language and presentation based on identity

### Identity Treatments (dickgirl-dom)
| Identity | Label | Treatment | Language tone |
|----------|-------|-----------|-------------|
| Male | Little One | Teased | Commanding, assertive |
| Female | Breeding Mare | Bred | Direct, transactional |
| Dickgirl | Equal | Respected | Peer-to-peer |
| Other | Guest | Handled | Neutral but firm |

### Identity Treatments (dickgirl-mommy)
| Identity | Label | Treatment | Language tone |
|----------|-------|-----------|-------------|
| Male | Good Boy | Nurtured | Warm, protective |
| Female | Sweet Girl | Cherished | Affectionate, gentle |
| Dickgirl | Dear | Respected | Warm, peer-to-peer |
| Other | Darling | Cared for | Warm, welcoming |

---

## What Still Needs to Be Done

### High Priority

1. **`gallery_feed` module theming**
   - **dom:** tight 3-column grid, hard edges, minimal padding, hover shows title overlay
   - **mommy:** large cards with generous whitespace, soft hover glow, "featured" layout prominent

2. **`links` module theming**
   - **dom:** structured table-style list, monochrome, shows URL path
   - **mommy:** card buttons with icons, friendly rounded style

3. **Commission creation form** (`commission/create.blade.php`)
   - **dom:** sharp borders, dense layout, identity-aware labels ("Tell me what you want." vs "What are you offering me?")
   - **mommy:** soft rounded form, warm placeholder text, encouraging tone

4. **Conversations/messages** (`conversations/index.blade.php`)
   - **dom:** darker sidebar, "Inbox" → "Terminal", compact message bubbles, status badges in sharp boxes
   - **mommy:** softer chat bubbles, "Inbox" → "Connections", warmer empty states

5. **Landing page** (`welcome.blade.php`)
   - **dom:** remove particle canvas, sharp geometric layout, dominant headline copy
   - **mommy:** warm gradient background, inviting copy, generous spacing

### Medium Priority

6. **CSS experiential vars not yet consumed by JS** — `--hover-compression`, `--surface-shine` etc. are set in the theme CSS but no JS applies them to interactive effects

7. **Identity-aware error/empty states** — all form validation errors, 404 pages, empty states should adapt per theme

8. **Profile page layout** (`profile/show.blade.php`) — the overall layout wrapper doesn't yet adapt per theme; module-level theming is done but the surrounding structure is neutral

### Lower Priority

9. **`text_block` module** — currently plain, could have dom/mommy variants

10. **`kanban_tracker` module** — could use themed stage names (e.g. dom: "IN THE TRENCHES" instead of "In Progress")

11. **`banner` module** — currently generic, could be fully bled/hero-style in dom vs soft/pastel in mommy

---

## CSS Experiential Variables

Each theme sets these in `layouts/app.blade.php` but they are not yet used in JavaScript:

```css
[data-theme="dickgirl-dom"] {
  --hover-compression: 0.97;   /* scale on hover: tighter */
  --hover-press: 0.94;         /* scale on click */
  --surface-shine: 0.12;       /* overlay brightness on hover */
  --shadow-blur: 0px;          /* blur radius for card shadows */
}

[data-theme="dickgirl-mommy"] {
  --hover-compression: 1.03;   /* scale on hover: expand gently */
  --hover-press: 0.98;
  --surface-shine: 0.08;
  --shadow-blur: 16px;
}
```

These should be consumed by a shared JS file that applies hover/click effects to cards and buttons throughout the site, creating a genuinely different *feel* beyond just colors and fonts.

---

## Key Routes

```
GET  /                    → Landing page
GET  /browse              → Browse artists
GET  /onboard             → Onboarding

GET  /login, /register    → Auth
POST /login, /logout

GET  /dashboard           → Commissioner's personal dashboard
GET  /messages            → Conversations inbox
GET  /messages/{conv}     → Conversation thread

GET  /commission/request/{username}  → Commission request form
GET  /commission/requests/{id}      → View a specific request

GET  /atelier/dashboard    → Artist dashboard (role: artist)
GET  /atelier/requests     → Artist request inbox
GET  /atelier/workspace    → Atelier workspace

GET  /{username}           → Public artist profile

GET  /identity             → Identity selector (dickgirl themes)
POST /identity            → Store identity choice

GET  /theme/switch/{name}  → Switch theme
GET  /theme/reset          → Reset to default theme
```

---

## File: `routes/web.php` at a Glance

The route file is organized into these sections (top to bottom):
1. Public pages (landing, pricing, auth)
2. Authenticated routes for commissioners (browse, dashboard, conversations, commissions, following)
3. Artist routes under `/atelier` prefix (workspace, requests, tracker)
4. Profile builder routes (save/load layouts, module management, gallery upload/delete)
5. Admin routes under `/admin`
6. Public artist profile (catch-all `/{username}` — must be last)
7. Identity selector routes
8. Theme switch/preview/reset routes
