# Fly.io Deployment Guide

## Prerequisites

1. Install [flyctl](https://fly.io/docs/hands-on/install-flyctl/):
   ```bash
   curl -L https://fly.io/install.sh | sh
   ```
2. Login: `fly auth login`

## One-Command Deploy

```bash
fly launch
```

This will:
- Create `fly.toml` (already provided, may be overwritten)
- Prompt for app name and region
- Offer to set up a PostgreSQL database
- Build and deploy

## Manual Deploy

### 1. Create App

```bash
flyctl apps create smart-vcard
```

### 2. Set Secrets

```bash
# Generate and set APP_KEY
flyctl secrets set APP_KEY=$(php artisan --no-ansi key:generate --show)

# Environment
flyctl secrets set APP_ENV=production
flyctl secrets set APP_DEBUG=false

# URL (replace with your actual app name)
flyctl secrets set APP_URL=https://smart-vcard.fly.dev
```

### 3. Create Volume (for uploads)

```bash
flyctl volumes create storage --region lhr --size 1
```

The volume is mounted to `/var/www/html/storage/app` in `fly.toml`.

### 4. Optional: PostgreSQL

```bash
flyctl postgres create --name smart-vcard-db --region lhr
flyctl postgres attach --app smart-vcard smart-vcard-db
```

If using Postgres, update `fly.toml`:

```toml
[env]
  DB_CONNECTION = "pgsql"
```

The `DATABASE_URL` secret is set automatically when attaching.

### 5. Deploy

```bash
flyctl deploy
```

### 6. Run Migrations

Add to `fly.toml` for automatic migrations:

```toml
[deploy]
  release_command = "php artisan migrate --force"
```

Or run manually:

```bash
flyctl ssh console --command "php artisan migrate --force"
```

## Environment Variables

| Variable | Required | Description |
|---|---|---|
| `APP_KEY` | Yes | Laravel encryption key |
| `APP_URL` | Yes | Your Fly app URL |
| `APP_ENV` | Yes | `production` |
| `DB_CONNECTION` | No | `sqlite` (default) or `pgsql` |
| `DATABASE_URL` | If pgsql | Set automatically by `flyctl postgres attach` |

## Storage

File uploads (avatars) are stored on the `storage` volume. Without a volume, uploads are lost on redeploy.

## Monitoring

```bash
# View logs
flyctl logs

# Check app status
flyctl status

# SSH into app
flyctl ssh console
```

## Troubleshooting

### App crashes on start

```bash
flyctl logs
flyctl ssh console --command "php artisan about"
```

### Migrations not running

Add `release_command` to `fly.toml` (see above).

### Volume not mounted

```bash
flyctl volumes list
flyctl ssh console --command "ls -la /var/www/html/storage/app"
```
