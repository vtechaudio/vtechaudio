# Equipment Hire — Package Copy (hire_package CPT)

Production-ready copy for the five hire packages. Companion to the Equipment Hire page
(see `02-service-pages-copy.md` / Batch 4). Only publish packages VTECH genuinely offers.

## How hire packages render (single-hire_package + package-meta.php)
Each package is a `hire_package` post. The "What's Included & Pricing" meta box maps to:
- **What's Included** -> `vtech_pkg_equipment` (one item per line -> bullets)
- **Services Included** -> `vtech_pkg_services` (one per line)
- **Price (KES)** -> `vtech_pkg_price` — **leave blank until you set real pricing** (no fabricated prices)
- **Capacity (guests)** -> `vtech_pkg_capacity` — set the real figure per package (optional)
- **Description** -> the post body (the intro copy below)
- **Featured image** -> a real photo of that setup when available

The WhatsApp "book" button is generated automatically from the package title + price + capacity.

Rank Math per package: set the focus keyword/title/meta below. Schema type Product or Service
is fine (the theme does not emit Service schema on hire_package, so no duplication). Add an FAQ
via a Rank Math FAQ block only if you include Q&As.

URL base: use your hire_package CPT's actual base (shown below as `/hire/…`; adjust to match).

---

## 1. Conference AV Package (slug: /hire/conference-av-package/)
- **Focus keyword:** conference AV hire Nairobi
- **SEO Title:** Conference AV Hire Nairobi | Sound, Mics & Screens | VTECH
- **Meta:** Conference AV hire in Nairobi and Kenya — PA sound, wireless and delegate microphones, projector or LED screen, with delivery, setup and an on-site technician.

**Description (post body):** Everything you need to run a professional conference, AGM or seminar — clear sound for every seat, reliable microphones for speakers and delegates, and a screen everyone can read. Delivered, set up and run by our technicians, with backup gear on standby.

**What's Included (`vtech_pkg_equipment`)**
- PA / line-array speakers on stands, sized to the room
- Wireless handheld and lapel microphones
- Lectern / podium microphone
- Digital mixing console
- Projector and screen, or LED screen
- Laptop / HDMI connectivity and switching
- Presentation clicker
- Delegate / table microphones (on request)
- Session audio recording (on request)
- Cabling, stands and power distribution

**Services Included (`vtech_pkg_services`)**
- Delivery and collection
- Professional setup and teardown
- On-site technician
- Backup equipment on standby

**Price:** leave blank — set your rate. **Capacity:** set per configuration.

---

## 2. Corporate Event AV Package (slug: /hire/corporate-event-av-package/)
- **Focus keyword:** corporate event AV hire Kenya
- **SEO Title:** Corporate Event AV Hire Kenya | Sound, LED & Lighting | VTECH
- **Meta:** Corporate event AV hire in Kenya — line-array sound, wireless mics, LED screens and stage lighting for launches, galas and town halls. Delivery, setup and technicians.

**Description (post body):** Make your product launch, gala or awards night look and sound the part. A complete sound, screen and lighting package for corporate events, delivered and run by an experienced crew.

**What's Included (`vtech_pkg_equipment`)**
- Line-array / PA sound system
- Wireless microphones (handheld + lapel)
- Digital mixing console
- LED screen or projector and screen
- Stage and uplighting
- DJ / music playback connectivity
- Video playback and switching
- Cabling, stands and power distribution

**Services Included (`vtech_pkg_services`)**
- Delivery and collection
- Professional setup and teardown
- On-site sound and lighting technicians
- Backup equipment on standby

**Price:** leave blank — set your rate. **Capacity:** set per configuration.

---

## 3. Wedding AV Package (slug: /hire/wedding-av-package/)
- **Focus keyword:** wedding sound and lighting hire Kenya
- **SEO Title:** Wedding AV Hire Kenya | Sound, Mics & Lighting | VTECH
- **Meta:** Wedding AV hire in Kenya — ceremony and reception sound, wireless microphones, music playback and mood lighting, with delivery, setup and an on-site technician.

**Description (post body):** Clear vows, smooth speeches and the right atmosphere from ceremony to dance floor. A wedding sound and lighting package handled end to end, so your day runs without a hitch.

**What's Included (`vtech_pkg_equipment`)**
- PA sound system for ceremony and reception
- Wireless microphones (officiant, MC, toasts)
- Music playback connectivity
- Uplighting / mood lighting
- Dance-floor sound
- LED screen or projector for slideshows (optional)
- Cabling, stands and power distribution

**Services Included (`vtech_pkg_services`)**
- Delivery and collection
- Setup and teardown
- On-site technician
- Backup equipment on standby

**Price:** leave blank — set your rate. **Capacity:** set per configuration.

---

## 4. Church Event AV Package (slug: /hire/church-event-av-package/)
- **Focus keyword:** church event AV hire Kenya
- **SEO Title:** Church Event AV Hire Kenya | Crusade & Convention Sound | VTECH
- **Meta:** Church event AV hire in Kenya — worship-tuned PA sound, wireless microphones, screens and lighting for crusades, conventions and services. Delivery, setup and technicians.

**Description (post body):** Speech-first sound and worship-ready audio for crusades, conventions and services — plus screens for lyrics and scripture. Set up and run by technicians who understand worship events.

**What's Included (`vtech_pkg_equipment`)**
- PA / line-array system tuned for speech and worship
- Wireless microphones (preacher, worship team)
- Instrument inputs and stage monitoring
- Digital mixing console
- LED screen or projector for lyrics and scripture
- Stage lighting (optional)
- Streaming / recording (optional)
- Cabling, stands and power distribution

**Services Included (`vtech_pkg_services`)**
- Delivery and collection
- Setup and teardown
- On-site sound technician
- Backup equipment on standby

**Price:** leave blank — set your rate. **Capacity:** set per configuration.

---

## 5. Outdoor Event AV Package (slug: /hire/outdoor-event-av-package/)
- **Focus keyword:** outdoor event AV hire Kenya
- **SEO Title:** Outdoor Event AV Hire Kenya | Open-Air Sound & LED | VTECH
- **Meta:** Outdoor event AV hire in Kenya — high-output PA, subwoofers, daylight LED screens, stage lighting and power for concerts, rallies and festivals. Delivery, setup and crew.

**Description (post body):** Open-air events need power, coverage and screens that survive daylight. A high-output outdoor package for concerts, rallies, sports and festivals — with the crew and backup to keep it running.

**What's Included (`vtech_pkg_equipment`)**
- High-output PA / line-array for open-air coverage
- Subwoofers
- Wireless microphones
- Digital mixing console
- Stage monitoring
- Daylight-rated LED screen
- Stage and event lighting (optional)
- Generator / power distribution (on request)
- Weather protection for equipment
- Cabling and stands

**Services Included (`vtech_pkg_services`)**
- Delivery and collection
- Setup and teardown
- On-site technicians
- Backup equipment and power on standby

**Price:** leave blank — set your rate. **Capacity:** set per configuration.

---

## Build notes
- Link each package from the Equipment Hire page's "Hire packages" list.
- Leave every price blank until real pricing is set — the page and WhatsApp button handle a blank price gracefully.
- Add a real featured photo per package as they become available.
- Keep only the packages VTECH genuinely offers; delete any that don't apply.
