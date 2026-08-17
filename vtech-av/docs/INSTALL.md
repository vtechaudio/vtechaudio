# Installation Guide — VTECH Audio Visual Theme

## 1. Requirements
- WordPress 6.4+ · PHP 8.0+ · MySQL 5.7+/MariaDB 10.3+
- HTTPS enabled · Recommended: a caching layer (LiteSpeed / WP Rocket / server cache)

## 2. Install the theme
1. Compress the `vtech-av` directory into `vtech-av.zip`.
2. **Appearance → Themes → Add New → Upload Theme** → choose the zip → **Install** → **Activate**.

## 3. Recommended plugins
| Purpose | Plugin | Notes |
|---|---|---|
| Custom fields | Advanced Custom Fields | Field groups auto-register from `inc/acf-fields.php`. Free tier is fine; Pro adds Repeater/Gallery UI — the PHP already defines these so Pro is recommended. |
| Forms | Contact Form 7 (or WPForms) | Landing template calls `[contact-form-7]`. |
| SEO (optional) | Yoast / RankMath / SEOPress | Theme auto-detects and hands over meta output. |
| Images | Optional: EWWW/ShortPixel | Serve WebP + further compression. |

> If you skip an SEO plugin, the theme outputs full meta, OpenGraph, Twitter,
> canonical and title templates on its own.

## 4. Add assets
Drop these into `wp-content/themes/vtech-av/assets/`:
- `fonts/manrope-var.woff2`, `fonts/inter-var.woff2` (download from Google Fonts / fontsource; self-hosting avoids render-blocking).
- `img/hero.webp`, `img/og-default.jpg`, `img/logo.png`.
- `icons/*.svg` (see `content/06-ai-image-prompts.md`).

## 5. Menus
**Appearance → Menus** — create and assign:
- **Primary Menu**: Home, Services, Industries, Projects, Equipment Hire, About, Blog, Contact.
- **Footer Menu**: your key service pages.
- **Utility**: optional top-bar links.

## 6. Front page
**Settings → Reading → A static page** → set **Home** as front page, **Blog** as posts page.
On the Home page, use the **Full Width** template and insert the VTECH homepage patterns (Inserter → Patterns → *VTECH — Homepage Sections*): Hero → Services → Industries → Stats → FAQ + CTA.

## 7. One-click demo import
`inc/demo-import.php` registers starter content. Run **Tools → VTECH Demo Import**
(if the companion importer plugin is active) OR import the provided WXR:
`docs/demo-content.xml` *(generate/export from your staging site — see DEPLOY.md)*.
This creates sample Services, Projects, Industries, Testimonials and FAQs.

## 8. Theme Options
**Appearance → Customize → VTECH Theme Options**:
- Company Details (NAP), Business Hours, Google Map embed URL.
- Homepage Hero (title, subtitle, LCP image).
- Conversion Elements (toggle WhatsApp / call / sticky CTA / exit intent).
- Analytics / GTM ID.

## 9. Permalinks
**Settings → Permalinks → Post name** (then Save once to flush rewrite rules — important for CPT slugs and the `/client-portal/` endpoint).

## 10. Verify
- Run Google Rich Results Test on the home + a service page (should show LocalBusiness, Service, FAQ, Breadcrumb).
- Run PageSpeed Insights; confirm fonts preload and CSS is non-render-blocking.
- Confirm floating WhatsApp/call use the correct number.
