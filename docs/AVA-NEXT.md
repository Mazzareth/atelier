# Ava Next — Atelier

## Immediate queue
1. Keep the CSS/theme foundation building.
2. Separate environmental test failures from app failures.
3. Pick the first theme migration slice with the best payoff.

## Candidate first slices
### Option A — `welcome.blade.php`
Good because it is public-facing and should showcase the theme system clearly.

### Option B — `browse.blade.php`
Good because it is a core user path and likely contains the worst theme-branch sprawl.

### Option C — `commission/create.blade.php`
Good because it is a tighter, more finishable slice with identity-aware language.

## Current pick
Theme/identity foundation anchored by `commission/create.blade.php`, with spillover now visible in `welcome.blade.php`, auth views, layout/nav, and the identity selector flow.

Reason: the original commission slice was the right first wedge, but the repo has already grown into a broader theme-components-and-identity pass. The notes should reflect the real cluster now instead of pretending it is still one isolated page.

## Status
In progress.
- commission request page refactored onto component primitives
- page-level commission classes added
- build re-verified after the refactor
- browse cleanup continued through reusable card/grid components
- identity selection flow now exists (`IdentityController`, `resources/views/identity/select.blade.php`)
- welcome page is still carrying a lot of inline presentation and looks like a likely next cleanup target

## Next from here
- browser-check the commission request and identity flow under default / dickgirl-dom / dickgirl-mommy
- decide which identity-aware pieces belong as reusable shell components instead of page-local styling
- migrate `welcome.blade.php` off inline-style presentation and onto the same component/theme vocabulary
- browser-check the updated conversations inbox/chat UI, especially:
  - the top progress strip hierarchy
  - right-rail weight/order
  - whether the pending-decision card now feels too wordy relative to the cleaner accepted state
  - whether any missing compact commission metadata belongs in overview/activity instead of the management card
- then reassess whether conversations inbox or browse gives the best next payoff

— Ava
2026-03-21
