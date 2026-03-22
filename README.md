# Atelier

Atelier is a Laravel-based commission platform for artists — especially furry, NSFW, niche, and queer communities that mainstream platforms tend to punish or shadowban.

## What it is

Core loop:

**client browses artists → requests a commission → artist accepts/declines → they collaborate in chat + workspace → commission completes**

Project notes with more detail live in:

- `docs/SITE-README.md` — broader architecture + theme system notes
- `docs/COMPONENT-LAYER.md` — current Blade component-layer shape and contracts
- `docs/AVA-ADOPTION.md` — current adoption / work-lane framing
- `docs/AVA-NEXT.md` — immediate next steps
- `docs/AVA-WORKLOG.md` — recent project movement

## Stack

- Laravel 13 / PHP 8.3
- PostgreSQL
- Redis
- Meilisearch
- Vite
- Theme manifests + CSS custom properties for identity-aware theme behavior

## Local runtime reality

This repo is currently shaped around the Docker/local-container path, not plain host PHP.

Relevant files:

- `docker-compose.yml`
- `Dockerfile`
- `.env`

Current `.env` points at the container service names directly:

- `DB_CONNECTION=pgsql`
- `DB_HOST=pgsql`
- `DB_USERNAME=sail`
- `DB_PASSWORD=password`

So if you try to run the app directly with host PHP, you may hit environment mismatches like missing `pdo_pgsql` or unreachable container-host service names. The honest path is: use the Docker stack, or deliberately adapt the host environment first.

## Local dev quick start

### Preferred: container-oriented local stack

```bash
cd /home/gote/dev/SaaS

docker compose up -d
npm run build
```

Then use the app through the container-backed environment described in `docker-compose.yml`.

### Host-PHP caveat

If you insist on running `php artisan serve` on the host, expect to fix environment mismatches first:

- PHP database driver support (notably `pdo_pgsql` for current `.env`)
- service hostnames (`pgsql`, `redis`, `meilisearch`) if you are not on the compose network
- storage/log ownership mismatches from mixed host/container writes

## Current project state

- substantial theme/components/identity work is in flight
- `commission/create` has already been refactored onto component primitives
- `welcome`, `browse`, auth views, layout/nav, and identity flow are part of the same broader theme migration cluster now
- local verification recently exposed environment seams, not just UI issues

## Notes for future me

- Do not trust the old stock Laravel assumptions here.
- Check `docs/AVA-NEXT.md` before picking the next slice.
- If the app throws weird local errors, verify whether you are in the intended container runtime before blaming the views.
