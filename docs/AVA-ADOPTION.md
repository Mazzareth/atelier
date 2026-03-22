# Ava Adoption — Atelier / SaaS

I am adopting this project as one of my real work threads.

## What this project is
A Laravel commission platform for artists, with strong theme/personality goals and a custom UI system in the middle of a redesign.

## What matters most right now
1. Make the theme foundation actually build and stay buildable.
2. Keep architecture notes close so I stop rediscovering the same shape.
3. Turn the theme system into something components can survive under.
4. Pick concrete page/module slices instead of vaguely "working on themes."

## Baseline I established tonight
- The project has substantial uncommitted theme/system work in flight.
- `ARCHITECTURE.md` defines the target clearly: data / lens / display.
- `README.md` was stock Laravel on entry, but has now been replaced with a real Atelier-facing README that names the docs and the container-shaped local runtime honestly.
- `npm run build` was broken on entry because `resources/css/app.css` imported local files in a way Vite/Tailwind could not resolve.
- `php artisan test` is not a clean baseline on this machine because storage/log permissions are owned by `www-data` and the test environment also reports a missing sqlite driver.
- Plain host-PHP probing is not an honest default runtime for this repo right now; the local stack is Docker/Sail-shaped, so verification needs to respect that or adapt the host environment deliberately.

## My active lane
### Lane 1 — foundation
- keep CSS entry/build healthy
- keep architecture notes current
- make it obvious what is baseline breakage versus feature work

### Lane 2 — component/theme migration
- neutral tokens
- stable base/components
- page-by-page replacement of inline styling and theme branches

### Lane 3 — real user-facing slices
- browse
- welcome
- commission create
- conversations
- profile wrapper

## Current first target
Theme foundation and build sanity, then the first page/module slice.

— Ava
2026-03-21
