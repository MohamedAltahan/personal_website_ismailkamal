# Ismail Kamal — Portfolio

Portfolio website and dashboard for a motion & graphic designer.

- **Stack:** Laravel 12 · Tailwind CSS 4 · Alpine.js · Vite
- **Languages:** Arabic (RTL) and English
- **Themes:** light, dark and system

## Features

- **Public site (minimal editorial):**
  - `/{ar|en}` URLs. Visitors are sent to their browser language automatically, and the choice is remembered in a cookie.
  - Pages: Home, Work (with category filters), project pages, About, Contact and custom pages.
  - Lightbox, video player, YouTube/Vimeo embeds, scroll animations and a custom cursor.
  - SEO: `sitemap.xml`, `hreflang`, JSON-LD and Open Graph tags.
  - Old URLs (`/design-details/{id}`, `/category/{id}`) redirect permanently to the new ones.
- **Dashboard** (`/admin`, same identity as the Alam dashboard):
  - Projects, categories, pages, media library, messages, social links, settings and profile.
- **Page builder:**
  - Projects and pages are built from blocks: hero, heading, text, image, gallery (grid / masonry / slider / stack), video, embed, media + text, columns, before/after, quote, credits, call to action, projects grid, spacer.
  - Every block has content and style settings, with Arabic and English content.
  - Live preview on desktop, tablet or mobile.
  - Undo and redo, local draft recovery, and drag & drop ordering.
- **Media pipeline:**
  - Chunked uploads, so large videos work regardless of PHP limits.
  - Video posters are captured automatically.
  - Responsive WebP/AVIF/JPG renditions at the quality set in *Settings → Images & media*.
  - Optional watermark.
  - One-click re-processing of all images.

## Local development

Run each step in order:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan portfolio:migrate-legacy
npm run dev
php artisan serve
```

`portfolio:migrate-legacy` is only needed once, on databases that come from the old site.

A local-only admin account can be created with `php artisan db:seed --class=LocalAdminSeeder`. The credentials are in that seeder file. It does nothing in production.

Run the tests with `php artisan test`.

## Deploying the upgrade to production

1. **Back up** the database and the `public/uploads` folder.
2. **Upload** the new code, keeping `public/uploads`.
3. **Install dependencies:** `composer install --no-dev --optimize-autoloader`. The server needs PHP ≥ 8.2 with the `gd` extension (WebP/AVIF support) and `fileinfo`.
4. **Build assets:** run `npm ci && npm run build`, or build locally and upload `public/build`.
5. **Update `.env`:** these keys are new or changed:
   - `APP_LOCALE=ar`
   - `CACHE_STORE=file`
   - `QUEUE_CONNECTION=sync`
   - the `MAIL_*` settings, if you want email notifications for contact messages
6. **Migrate:** `php artisan migrate --force`. The migrations are additive: legacy tables and files are kept.
7. **Convert the old content:** first run `php artisan portfolio:migrate-legacy --dry-run`, then `php artisan portfolio:migrate-legacy`. It is safe to re-run; nothing is duplicated.
8. **Cache:** `php artisan optimize`.

After the new site is confirmed, the legacy tables (`images`, `videos`, `abouts`, `home_page_settings`, `logo_settings`, `website_colors`, `settings`) are no longer read. They can be dropped later.

### Useful commands

| Command | What it does |
|---|---|
| `php artisan media:regenerate` | Rebuild every image with the current media settings. |
| `php artisan media:regenerate --missing` | Only images that have no renditions yet. |
| `php artisan portfolio:migrate-legacy --force` | Rebuild projects and pages from the legacy tables. This overwrites edits made in the builder. |

## Structure

- `app/Support/Blocks/BlockRegistry.php` — block types, their defaults, and server-side sanitising.
- `resources/views/blocks/*` — how each block renders on the site. The builder preview uses the same views.
- `resources/views/admin/builder/*` — the editor and one inspector per block type.
- `resources/js/builder/index.js` — editor state, history and live preview.
- `app/Services/MediaService.php` — uploads, renditions, watermark and embeds.
- `config/site.php` — locales, fonts and default values for every setting.
- `lang/ar.json` — Arabic interface strings. The keys are the English text.

### Adding a new block type

1. Define it in `BlockRegistry::types()`.
2. Create `resources/views/blocks/{type}.blade.php`.
3. Create `resources/views/admin/builder/inspectors/{type}.blade.php`.
