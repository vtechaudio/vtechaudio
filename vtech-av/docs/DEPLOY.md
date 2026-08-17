# Deployment Guide — VTECH Audio Visual Theme

## Environments
- **Staging** → build/author content here (patterns, CPTs, demo content).
- **Production** → https://vtechaudio.co.ke/

## 1. Pre-deploy checklist
- [ ] Real logo, hero, OG and project images added (WebP).
- [ ] Variable fonts self-hosted in `assets/fonts/`.
- [ ] Correct phone number confirmed and set in Customizer (see README note).
- [ ] Google Map embed URL set.
- [ ] SEO plugin configured OR theme SEO verified.
- [ ] Contact form connected + test submission received.
- [ ] Permalinks flushed (Settings → Permalinks → Save).

## 2. Export starter/demo content
On staging: **Tools → Export → All content** → download `demo-content.xml`.
Place in `docs/` for the one-click importer, or import on production via
**Tools → Import → WordPress**.

## 3. Performance hardening (production)
- Enable a page cache (LiteSpeed Cache / WP Rocket / server-level).
- Enable Gzip/Brotli compression at the server.
- Serve static assets via CDN (Cloudflare recommended — free tier is fine for Kenya).
- Confirm HTTP/2 or HTTP/3.
- Object cache (Redis) if the host supports it.
- Do NOT let a minify plugin re-inline the already-optimised CSS; the theme
  handles critical/deferred CSS itself. If using WP Rocket, disable "Optimize
  CSS delivery" (theme already does this) to avoid double work.

## 4. SEO go-live
- Verify site in Google Search Console; submit XML sitemap
  (`/wp-sitemap.xml` core, or your SEO plugin's sitemap).
- Set up Google Business Profile; embed the same NAP as the theme (must match exactly for local SEO).
- Run Rich Results Test on home + one service + one project.
- Set canonical domain (www vs non-www) and 301 the other.

## 5. Security go-live
- Force HTTPS; add HSTS at the server.
- Limit login attempts + 2FA for admins.
- Keep `DISALLOW_FILE_EDIT` on (theme sets it).
- Regular backups (daily) + a staging restore test.

## 6. Post-launch monitoring
- PageSpeed Insights weekly for the first month.
- Search Console: coverage, Core Web Vitals report, and query performance for
  the target keywords (Audio Visual Kenya, LED Screens Kenya, etc.).
- Track form + WhatsApp + call conversions in GA4.

## 7. Rollback
Keep the previous theme zip. To roll back: activate previous theme, restore
DB backup if content structure changed. CPT content persists across theme
switches (data is in the DB), but templates revert — so keep this theme zipped.
