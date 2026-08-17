# Block Patterns

WordPress 6.x auto-registers any `.php` file in this `/patterns` directory that
has the pattern header comment (Title / Slug / Categories). No manual
registration needed — `inc/block-patterns.php` only registers the *categories*.

## Included patterns
| File | Slug | Category |
|---|---|---|
| home-hero.php | vtech-av/home-hero | VTECH — Homepage Sections |
| home-services.php | vtech-av/home-services | VTECH — Homepage Sections |
| home-industries.php | vtech-av/home-industries | VTECH — Homepage Sections |
| home-stats.php | vtech-av/home-stats | VTECH — Homepage Sections |
| home-cta-faq.php | vtech-av/home-cta-faq | VTECH — Homepage Sections |
| service-layout.php | vtech-av/service-layout | VTECH — Service Page |

## Building the homepage
Insert in this order on a Full-Width page set as your front page:
Hero → Services → Industries → Stats → CTA+FAQ.

## Adding more patterns
Copy any file, change the header `Title` + `Slug`, and it appears in the
Inserter automatically.
