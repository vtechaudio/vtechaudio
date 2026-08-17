# Style Guide & Design System — VTECH Audio Visual

## Brand principles
Modern · Minimal · Corporate · Premium · Apple-quality clarity · Lots of white
space · Smooth, restrained motion · Trust through precision.

## Colour palette
| Token | Hex | Use |
|---|---|---|
| Primary Blue | `#0A4FA3` | Brand, links, primary buttons, headers |
| Primary Dark | `#083C7C` | Button hover, gradient depth |
| Primary Tint | `#E7F0FA` | Soft backgrounds, process numbers |
| Secondary Ink | `#111111` | Body text, footer, dark UI |
| Accent Amber | `#FFB400` | CTAs, highlights, stat numbers |
| Accent Soft | `#FFF4D6` | Badges, subtle emphasis |
| White | `#FFFFFF` | Primary background |
| Surface | `#F7F9FC` | Section alternation |
| Hairline | `#E4E9F0` | Borders, dividers |
| Muted | `#5B6472` | Secondary text |

**Contrast:** Amber CTAs use `#111` text (WCAG AA). Blue on white and white on
blue both pass AA. Never place amber text on white.

## Typography
- **Headings:** Manrope, 700–800, letter-spacing −0.02em, line-height 1.15.
- **Body:** Inter, 400, line-height 1.65, base 1.0625rem (17px).
- **Fluid scale** via `clamp()` — see `theme.json` fontSizes (xs → Display).
- Self-hosted variable woff2, `font-display: swap`, preloaded.

## Spacing scale
0.5 · 1 · 1.5 · 2.5 · 4 · 6 · 8 rem (theme.json spacingSizes 20–80).
Sections use `clamp(3rem, 6vw, 6rem)` vertical padding.

## Radii & elevation
- Radius: 12px (buttons), 14px (cards), 24px (CTA band).
- Shadow: `0 12px 40px rgba(10,79,163,.12)` on hover only (flat at rest).

## Components
- **Buttons:** `.btn` (primary), `.btn--accent`, `.btn--ghost`, `.btn--lg`, `.btn--block`.
- **Cards:** `.card`, `.card--service`, `.card--project`; hover lift + shadow.
- **Glass stats:** `.stat` over blue band, amber numbers, count-up on scroll.
- **FAQ:** native `<details>` accordion, no JS required.
- **Chips:** `.chip` filter pills.
- **Badges:** availability (`--available/--limited/--booked`).
- **Floating:** WhatsApp (green), call (blue), sticky CTA bar.

## Motion
- Transitions 150–200ms, ease. Hover lifts ≤4px.
- Count-up stats via IntersectionObserver.
- Respect `prefers-reduced-motion` (all motion reduced to ~0ms).

## Accessibility
- Skip link, semantic landmarks, `aria-expanded` on nav toggle.
- `:focus-visible` amber outline, 3px.
- Alt text required on all content images (enforced via editorial checklist).
- Colour never the sole signal (badges carry text labels).
- Target: WCAG 2.1 AA.

## Iconography
Line style, 2px stroke, rounded caps, 48×48 grid, primary blue. See
`content/06-ai-image-prompts.md` for the icon list.

## Voice & tone (copy)
Direct, confident, benefit-led. Lead with the client's problem, then VTECH's
answer. Local and concrete ("all 47 counties", "quote in 24 hours"). Avoid
hype and filler. Every page ends with one clear CTA.
