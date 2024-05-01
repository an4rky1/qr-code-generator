# Smart VCard Generator

Laravel 11 application for creating digital business cards with custom QR code generation.

## Features

- **VCard Constructor** — Livewire 3 form with reactive QR preview
- **Custom QR Codes** — SVG format with gradient styling, rounded corners, circular eyes
- **Avatar Embedding** — User photo embedded in QR center via SVG clip-path
- **Neo-Memphis Design** — Acid colors, bold black outlines, geometric floating shapes
- **Public Profile Pages** — Shareable vcard links at `/card/{slug}`
- **QR Download** — Stream SVG download with embedded avatar

## Tech Stack

- Laravel 11, PHP 8.3+
- Livewire 3 + Alpine.js
- Tailwind CSS + Vite
- `simplesoftwareio/simple-qrcode` for QR generation
- SQLite (default), PostgreSQL/MySQL supported

## Quick Start

```bash
composer setup
npm run build
php artisan serve
```

Visit `http://localhost:8000/card/create` to create your first vcard.

## Running Tests

```bash
composer test
```

15 tests covering QR service, Livewire component, and E2E flows.

## Deploy to Fly.io

### Prerequisites

- Install [flyctl](https://fly.io/docs/hands-on/install-flyctl/)
- Login: `fly auth login`

### Deploy

```bash
# Launch app (creates fly.toml, sets secrets, provisions DB)
fly launch

# Or deploy manually:
flyctl apps create smart-vcard
flyctl postgres create --name smart-vcard-db
flyctl postgres attach --app smart-vcard smart-vcard-db

# Set required secrets
flyctl secrets set APP_KEY=$(php artisan --no-ansi key:generate --show)
flyctl secrets set APP_ENV=production
flyctl secrets set APP_URL=https://your-app-name.fly.dev

# Deploy
flyctl deploy
```

### Volumes (for file uploads)

Add to `fly.toml`:

```toml
[mounts]
  source = "storage"
  destination = "/var/www/html/storage/app/public"
```

Then create volume:

```bash
flyctl volumes create storage --region lhr --size 1
```

See `DEPLOYMENT.md` for detailed instructions.

## License

MIT
