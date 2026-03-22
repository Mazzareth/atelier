# Component layer notes

## 2026-03-21

This repo’s Blade component shelf at `resources/views/components/` is no longer one simple bucket of interchangeable partials.

There are three different kinds of reusable pieces living there now:

## 1. Generic UI primitives
Examples:
- `button.blade.php`
- `card.blade.php`
- `badge.blade.php`
- `avatar.blade.php`
- `modal.blade.php`
- `empty-state.blade.php`
- `nav-link.blade.php`

These are mostly presentation/control wrappers.

## 2. Form primitives
Examples:
- `input.blade.php`
- `textarea.blade.php`
- `select.blade.php`

These are special because they now serve **two real usage modes**:

### Submitted field mode
- has a real `name`
- may also have an `id`
- may use `old($name)` fallback behavior

### Editor/control mode
- may be used with only an `id`
- may exist inside JS-driven modal/editor UI
- must not assume `name` exists

### Current contract for form primitives
- derive the field id from `id` or `name`
- only emit `name="..."` when a real name exists
- only call `old($name)` when a real name exists

This matters because the profile editor modal uses several component instances as UI controls rather than as plain posted form fields. Earlier component versions assumed `name` was always present and caused Blade crashes like `Undefined variable $name`.

## 3. Domain-specific app components
Examples:
- `artist-card.blade.php`
- `commission-slot.blade.php`
- `follow-button.blade.php`
- `gallery-grid.blade.php`
- `mode-switcher.blade.php`
- `profile-module.blade.php`

These are closer to Atelier-specific view building blocks than generic shared primitives.

## Why this doc exists
Future cleanup should not treat every component in this folder like the same kind of reusable thing.

The immediate practical rule is:
- be explicit about required props
- decide whether a component is a UI primitive, a form primitive, or a domain component
- for form primitives, do not hard-code assumptions that only make sense for posted fields

## Good next cleanup later
A useful next pass would be standardizing required/optional prop contracts across the shared components so the layer stops depending on implied usage conventions.
