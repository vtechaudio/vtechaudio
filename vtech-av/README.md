# VTECH Audio Visual — Premium WordPress Theme

Version 2.0.0 · A complete, install-and-go custom WordPress theme for VTECH
Audio Visual Solutions (Kenya). Native Gutenberg, block-based, SEO-first,
Core Web Vitals optimised. No page builders.

---

## Quick Start (3 steps)

### 1. Install & activate
WP Admin → **Appearance → Themes → Add New → Upload Theme** → select
`vtech-av.zip` → **Install Now** → **Activate**.

> If you are updating from an earlier build, DELETE the old "VTECH Audio Visual"
> theme first, then upload. This version is 2.0.0 so WordPress treats it as a
> fresh install (no stale files).

### 2. Install recommended plugins
After activation a notice lists the plugins to install & activate:
- **Advanced Custom Fields** (powers service/testimonial/equipment fields)
- **Contact Form 7** (powers quote/contact forms)
- **One Click Demo Import** (optional)

### 3. Build the whole site (one click)
Left admin menu → **VTECH Setup** → **Set up my website**.
This creates every page, seeds services/industries/projects/testimonials/FAQs
(each with a bundled image), sets the logo + hero, builds the menus, and sets
the static front page. When it finishes, **Settings → Permalinks → Save Changes**
once, then view your site.

The homepage renders directly from the theme (hero, services, industries,
stats, FAQ, CTA) using the bundled images — it shows even before setup runs.

---

## What's included
- **Design system** (theme.json): #0A4FA3 / #111 / #FFB400, Manrope + Inter (self-hosted), fluid type.
- **8 Custom Post Types + 7 taxonomies**: Service, Project, Industry, Equipment, Brand, Testimonial, Case Study, FAQ.
- **19 bundled WebP/JPG images**, 12 SVG icons, self-hosted variable fonts (OFL).
- **SEO layer**: LocalBusiness/Organization/Service/FAQ/Breadcrumb/Review JSON-LD, OpenGraph, Twitter, canonical, title templates. Defers to Yoast/RankMath/SEOPress if installed.
- **Performance**: inlined critical CSS, deferred main CSS, preloaded fonts, LCP hero preload, lazy-load, no jQuery.
- **Conversion**: sticky header, sticky CTA, floating WhatsApp + call, exit-intent, project filters, animated stats.
- **One-click setup wizard** + **demo importer** (docs/demo-content.xml).
- **WooCommerce + Contact Form 7 compatible, translation-ready, child-theme ready, security-hardened.**

## Make it yours (like any WordPress theme)
- **Customize → VTECH Theme Options**: phone, WhatsApp, email, address, hours, Google Map, hero, conversion toggles, analytics ID.
  > The official VTECH number is +254 728 135 246 (WhatsApp 254728135246). It is the default
  > here and in schema; change it only if the business number changes.
- **Pages / Services / Projects**: edit the text.
- **Media library**: the AI placeholder images are real files — replace each with your own VTECH photos (keep similar dimensions).
- **Appearance → Menus**: reorder or add items.

## Two ways to load demo content
1. **One-click**: VTECH Setup → Set up my website (recommended).
2. **Manual WXR**: Tools → Import → WordPress → upload `docs/demo-content.xml`.

## Troubleshooting
- **Homepage looks unstyled / blank**: Settings → Permalinks → Save Changes once, then hard-refresh (Ctrl+Shift+R). Confirm Settings → Reading → Homepage = Home.
- **CPT pages 404**: Settings → Permalinks → Save Changes (flushes rewrite rules).
- **Fonts/images missing after FTP upload**: ensure the whole `assets/` folder uploaded.

## Requirements
WordPress 6.4+ · PHP 8.0+ · HTTPS. A caching plugin is recommended for best PageSpeed.

## Structure
```
vtech-av/
├── style.css theme.json functions.php
├── front-page.php  (renders the full homepage directly — no pattern dependency)
├── header.php footer.php index.php single-service.php archive-project.php
├── single.php page.php page-full-width.php template-landing.php searchform.php
├── inc/  post-types, taxonomies, acf-fields, seo-meta, schema, breadcrumbs,
│         performance, security, block-patterns, theme-options, demo-import,
│         setup-wizard, required-plugins, lib/, features/
├── patterns/  (Gutenberg block patterns)
├── assets/  css/ js/ fonts/ icons/ img/
├── languages/ vtech-av.pot
└── docs/  INSTALL.md DEPLOY.md STYLE-GUIDE.md demo-content.xml
content/  (copy, 110 article ideas, image prompts — reference)
```
