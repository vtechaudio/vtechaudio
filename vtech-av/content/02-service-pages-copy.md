# VTECH — Service Pages Copy (full depth, field-mapped)

Production-ready copy for all 11 services, mapped to the `single-service.php` ACF template.
Fill each field on the Service post and the page renders in this order automatically:
**Hero → Intro → Benefits ("Why choose VTECH") → Process → Gallery → FAQ → Related services → Quote CTA.**

Positioning throughout: **VTECH is an AV systems integrator** (design, supply, installation,
integration, hire, support) — not an equipment supplier. Keep the voice consistent across pages.

Canonical NAP (keep identical everywhere): Phone/WhatsApp **+254 728 135 246** ·
info@vtechaudio.co.ke · Ground Floor, Mpaka Plaza, Mpaka Road, Westlands, Nairobi · Mon–Fri 9:00 AM – 6:00 PM.

## Rules that apply to EVERY service page (set once, per page)
- **Rank Math Schema Type: None / Off** — the theme already emits Service + FAQPage schema on
  service pages. Leaving Rank Math schema off avoids duplicate markup.
- **No Rank Math FAQ block** — fill the FAQ field; it produces both the visible FAQ and FAQPage schema.
- **Price from: leave empty** — no fabricated prices (empty = no price shown, no Offer schema).
- **Gallery: real photos only** — leave empty until genuine project photos exist; the section auto-hides.
- **Excerpt matters** — it becomes the Service schema *description* and the hero tagline fallback. Set it on every service.
- **CTAs** point to `/consultation/` (see flag below — align that page's slug).
- **Equipment Hire is the exception** — it's a top-level page, not a Service CPT entry, so the schema/FAQ
  rules flip there (Rank Math Schema = Service + FAQ via a Rank Math block). Flagged in that section.

## Standard Process *(reuse on every service — adjust only where noted)*
1. **Consultation & Site Survey** — We visit your site to assess the space, requirements and how you'll use it — no quoting blind.
2. **Design & Fixed Quote** — A tailored system design and specification, with a fixed, itemised written quote within 24 hours (Nairobi) or 48 hours upcountry.
3. **Supply & Installation** — Professional, standards-compliant installation and cabling, with minimal disruption.
4. **Testing & Commissioning** — Full testing, calibration and configuration for your space.
5. **Training & Handover** — We train your team and hand over clear documentation.
6. **Support & Maintenance** — 12-month support, with optional annual servicing.

## Two build flags
1. **Consultation slug.** The theme's "Request a Site Survey" / "Book a Free Consultation" buttons link to
   `/consultation/`. Ensure your consultation page lives at `/consultation/` (simplest), or tell me to
   repoint the theme links to `/book-a-consultation/` in the next bump — otherwise those buttons 404.
2. **Social profiles (`sameAs`).** Add Facebook, Instagram, LinkedIn, X, YouTube **and TikTok** URLs under
   **Appearance → Customize → VTECH Theme Options → Social**. As of v5.30.0 a TikTok slot exists, and the
   theme ships confirmed defaults for Facebook, TikTok and LinkedIn; enter Instagram / YouTube / X to complete it.

---

# 1. Professional Sound Systems

## Page setup
- **Post type:** Service · **Permalink:** `/services/sound-systems/`
- **Excerpt:** Professional sound system design and installation in Kenya — line arrays, PA, digital mixing and DSP, tuned to your venue for clear, even, reliable sound.

## Rank Math
- **Focus keyword:** sound system installation Kenya
- **SEO Title:** Professional Sound Systems Kenya | PA & Line Array Installation | VTECH
- **Meta:** Professional sound system installation in Kenya — line arrays, PA, digital mixing and DSP for churches, halls, stadiums and events. Free site survey, fixed quote in 24 hours.

## Tagline
Clear, powerful, intelligible sound — engineered for your room.

## Intro *(post body)*
> Professional sound systems in Kenya fail for one reason: they're specified for a spec sheet, not a space. VTECH designs every system around your actual venue — its acoustics, seating and how it's used — so speech stays intelligible and music stays clean at any volume. From a 100-seat church to a national stadium, we take responsibility for the whole system: design, supply, installation, tuning, commissioning and support.
>
> We're an AV systems integrator, not a box-shifter. That means we're accountable for the result — even coverage, clear speech and reliable operation — not just the equipment on the invoice.
>
> **What we provide**
> - Line-array systems
> - Point-source speaker systems
> - Public address (PA) systems
> - Speech reinforcement and conferencing audio
> - Digital mixing consoles
> - Wireless and wired microphone systems
> - Amplification
> - Digital signal processing (DSP)
> - Stage and floor monitoring
> - System tuning and optimisation
> - Professional installation and cabling
> - Commissioning, operator training and documentation
>
> **Where we install**
> - Churches and houses of worship
> - Hotels and conference centres
> - Schools and universities
> - Auditoriums and theatres
> - Corporate offices and boardrooms
> - Government and county facilities
> - Stadiums and outdoor venues
> - Event and production spaces
>
> Every system is delivered and supported across all 47 counties in Kenya, with select projects across East Africa. Where a room's acoustics work against clarity, we pair the system with acoustic treatment so the sound has a fair chance — because more volume in a reverberant room only makes speech harder to understand.
>
> *(Link "acoustic treatment" → Acoustic Design & Soundproofing; add a natural link to Conference & Boardroom where "conference room audio" fits.)*

## Benefits *(6)*
1. **Room-Tuned Coverage** — Every system is designed around your venue's acoustics and seating, so sound is even from the front row to the back — no dead spots, no painful hotspots.
2. **Speech You Can Actually Understand** — We engineer for intelligibility first, so sermons, speeches and announcements stay clear even in hard, reverberant halls.
3. **Feedback-Resistant by Design** — Careful gain structure, microphone selection and DSP keep the system stable and squeal-free, even at high volume.
4. **Simple for Your Team to Run** — Recallable settings and clean control mean volunteers and staff can operate it confidently, service after service.
5. **Professional Installation & Commissioning** — Standards-compliant installation by trained technicians, then tuned and commissioned to your room — with full documentation.
6. **12-Month Support, Nationwide** — Every installation includes a 12-month support window, with annual maintenance available, across all 47 counties.

## Process
Use the Standard Process. (Step 4 = "Tuning & Commissioning" — set levels, EQ, coverage and feedback margins for your specific room.)

## FAQ *(6)*
1. **How much does a sound system installation cost in Kenya?** — It depends on your venue size, coverage and equipment tier — there's no single price. After a free site survey we provide a fixed, itemised written quote within 24 hours in Nairobi (48 hours upcountry).
2. **Do you install church sound systems?** — Yes — houses of worship are one of our core specialisms, with speech-first tuning and simple operation for volunteer teams.
3. **Can you tune or upgrade our existing system?** — Yes. We offer audio commissioning and re-tuning as a standalone service, and we integrate compatible existing equipment wherever possible to protect your investment.
4. **How do you prevent feedback and dead spots?** — Through proper coverage design, microphone selection and DSP — we engineer speaker placement and gain structure around your specific room.
5. **Do you cover venues outside Nairobi?** — Yes. We design, install and support sound systems across all 47 counties in Kenya, and select projects across East Africa.
6. **What's included after installation?** — A 12-month support window, operator training and full system documentation, with annual maintenance contracts available.

## Gallery
SLOT — real sound-system installation photos only (speakers, racks, mixing positions, completed venues).

## Related services *(pick 3)*
LED Screens & Video Walls · Conference & Boardroom Systems · Acoustic Design & Soundproofing

---

# 2. LED Screens & Video Walls

## Page setup
- **Post type:** Service · **Permalink:** `/services/led-screens/`
- **Excerpt:** LED screen and video wall supply and installation in Kenya — indoor and outdoor displays matched to your pixel pitch, brightness and viewing distance.

## Rank Math
- **Focus keyword:** LED screen installation Kenya
- **SEO Title:** LED Screens Kenya | Indoor & Outdoor LED Video Wall Installation | VTECH
- **Meta:** LED screen and video wall installation in Kenya — indoor and outdoor displays for events, churches, retail and corporate, matched to your brightness and pixel pitch. Free survey, quote in 24 hours.

## Tagline
Bright, seamless LED that performs in Kenyan daylight.

## Intro *(post body)*
> Choosing an LED screen isn't about the biggest panel — it's about the right brightness, pixel pitch and viewing distance for your space. VTECH supplies and installs LED screens and video walls engineered to your venue and content, and we handle the parts most suppliers leave to you: structure, power, content, control and support.
>
> As an AV systems integrator, we take responsibility for the whole display — not just the panels, but how they mount, how they're driven, and how they'll keep performing.
>
> **What we provide**
> - Indoor LED video walls
> - Outdoor LED screens (daylight-rated, weatherproof)
> - Fine-pitch LED for boardrooms and broadcast
> - Stage-backdrop and worship LED
> - Retail and advertising displays
> - Video processing and scaling
> - Content and scheduling setup
> - Control systems
> - Structural rigging and mounting
> - Power and cable infrastructure
> - Professional installation
> - Spares and support
>
> **Where we install**
> - Corporate lobbies and boardrooms
> - Churches and worship centres
> - Retail stores and malls
> - Events and conferences
> - Stadiums and outdoor advertising
> - Hotels and hospitality
> - Broadcast and media
> - Control and command rooms
>
> Supplied and supported across all 47 counties in Kenya, with select East Africa projects. For screens in bright or naturally-lit spaces, ask us about the difference between an LED wall and a projector — we help you avoid overspending on the wrong technology.

## Benefits *(6)*
1. **Daylight-Readable Brightness** — Displays specified to stay vivid in Kenyan sunlight and bright interiors, not just dim rooms.
2. **Seamless, Modular Panels** — Bezel-free video walls that scale to any size or shape, with panels matched for colour and brightness.
3. **The Right Pixel Pitch** — We choose pitch from your real viewing distance, so the image is sharp where people actually stand — without overpaying.
4. **Structurally Safe Rigging** — Proper mounting, rigging and power, engineered for safety and longevity.
5. **Content & Control Set Up for You** — We configure processing, scheduling and control so the screen is ready to run, not just installed.
6. **Spares & Support** — Warrantied panels, spares strategy and a 12-month support window.

## Process
Use the Standard Process. (Step 4 = "Testing, calibration & content configuration".)

## FAQ *(6)*
1. **What pixel pitch do I need?** — It's set by how close viewers stand — the closer they are, the finer the pitch. We calculate it from your room during a free survey.
2. **Do you supply outdoor LED screens?** — Yes — daylight-rated, weatherproof LED for both permanent installation and events.
3. **LED wall or projector — which is right for me?** — It depends on ambient light, size and budget. In bright rooms LED wins; in controlled light a projector can be the smarter spend. We advise honestly during the survey.
4. **How much does an LED screen cost in Kenya?** — It depends on size, pixel pitch and indoor/outdoor rating — no single price. We provide a fixed quote within 24 hours of a free survey.
5. **Can you handle content and scheduling?** — Yes — we set up video processing, content and scheduling so the display is ready to use.
6. **What about maintenance and spares?** — Every install includes a 12-month support window and a spares strategy, with annual maintenance available.

## Gallery
SLOT — real LED installation photos only.

## Related services *(pick 3)*
Digital Signage · Conference & Boardroom Systems · Professional Sound Systems

---

# 3. Conference & Boardroom Systems

## Page setup
- **Post type:** Service · **Permalink:** `/services/conference-systems/`
- **Excerpt:** Conference room and boardroom AV installation in Kenya — video conferencing, microphones, displays and one-touch control, integrated to work every time.

## Rank Math
- **Focus keyword:** conference room AV Kenya
- **SEO Title:** Conference Room AV Kenya | Boardroom AV & Video Conferencing | VTECH
- **Meta:** Conference room and boardroom AV installation in Kenya — video conferencing, one-touch control and room automation for corporates and government. Fixed quote in 24 hours.

## Tagline
One-touch meeting rooms that just work.

## Intro *(post body)*
> A boardroom shouldn't need an IT technician to start a call. VTECH builds conference and boardroom AV where one button connects the display, camera, microphones and video-conferencing platform — reliably, for every user, every meeting. We design the room around how you actually meet: in person, hybrid, or fully remote.
>
> As an AV systems integrator, we engineer the whole room to work as one system — audio, video, control and connectivity — not a collection of parts.
>
> **What we provide**
> - Video-conferencing rooms (Zoom, Teams, Google Meet)
> - Ceiling and beamforming microphones
> - Room loudspeakers and DSP
> - Acoustic echo cancellation
> - Displays and video walls
> - Room cameras (PTZ and auto-framing)
> - One-touch control systems
> - Wireless and wired content sharing (BYOD)
> - Room-scheduling panels
> - Cable management and infrastructure
> - Professional installation
> - Support and maintenance
>
> **Where we install**
> - Corporate boardrooms
> - Conference and training rooms
> - Government facilities and council chambers
> - Universities and colleges
> - Hotel meeting rooms
> - Huddle and focus rooms
> - Auditoriums
> - Operations and briefing rooms
>
> Delivered and supported across all 47 counties in Kenya. Where a glass-and-concrete room echoes, we pair it with acoustic treatment so the far end hears you clearly.

## Benefits *(6)*
1. **One-Touch Join** — Walk in, tap start, and the display, camera, mics and platform are connected. No fuss, no delays.
2. **Everyone Heard Clearly** — Microphone coverage and echo cancellation designed so remote participants hear every seat.
3. **Platform-Ready** — Built for Zoom, Teams or Google Meet, standardised the way your organisation works.
4. **Reliable, Every Meeting** — Engineered and commissioned for consistency, so meetings start on time.
5. **Central Room Management** — Multiple rooms configured and manageable to a common standard.
6. **Predictable Support** — A 12-month support window with annual maintenance available.

## Process
Use the Standard Process. (Step 4 = "Testing, platform configuration & calibration".)

## FAQ *(6)*
1. **How many microphones does a room need?** — Enough to cover every seat evenly — set by room size and layout, not a fixed number. A coverage plan determines it.
2. **Can we just use a laptop and webcam?** — For a huddle, sometimes. For a real boardroom or hybrid meetings, a designed room system delivers far better audio, camera framing and reliability.
3. **Which platforms do you support?** — Zoom, Microsoft Teams and Google Meet — configured to your organisation's standard.
4. **How much does conference room AV cost in Kenya?** — It depends on room size, platform and features — no single price. We quote a fixed price within 24 hours of a free survey.
5. **Do conference rooms need acoustic treatment?** — Hard, glassy rooms often do — it sharpens speech and stops the far end hearing echo. We assess this during the survey.
6. **Can you standardise several rooms?** — Yes — we roll out multiple rooms to a common, manageable standard.

## Gallery
SLOT — real boardroom/conference installation photos only.

## Related services *(pick 3)*
Video Conferencing · LED Screens & Video Walls · AV Integration

---

# 4. Acoustic Design & Soundproofing

## Page setup
- **Post type:** Service · **Permalink:** `/services/acoustic-design/`
- **Excerpt:** Acoustic treatment, soundproofing and acoustic design in Kenya — echo control and sound isolation engineered to your room and its purpose.

## Rank Math
- **Focus keyword:** acoustic treatment Kenya
- **SEO Title:** Acoustic Treatment & Soundproofing Kenya | Acoustic Design | VTECH
- **Meta:** Acoustic treatment and soundproofing in Kenya — echo control, sound isolation and acoustic design for churches, studios, boardrooms and auditoriums. Free acoustic assessment.

## Tagline
Rooms that sound as good as they look.

## Intro *(post body)*
> Acoustic treatment and soundproofing solve two different problems: treatment controls sound *inside* a room (echo, reverberation, clarity); soundproofing stops sound getting *in or out* (isolation). Confusing the two wastes money — foam on a wall will not soundproof a room. VTECH assesses your space, identifies which you actually need, and designs the right solution.
>
> As an AV systems integrator, we treat acoustics as part of the system — because the best sound system in the world can't fix a room that fights it.
>
> **What we provide**
> - Acoustic assessment and measurement
> - Room acoustic design
> - Absorption (acoustic panels, bass traps)
> - Diffusion
> - Soundproofing and sound isolation
> - Decoupling and sealing
> - Ceiling and wall treatments
> - Studio and control-room acoustics
> - Auditorium and worship acoustics
> - Noise control for offices and hospitality
> - Architectural and aesthetic acoustic finishes
> - Professional installation
>
> **Where we install**
> - Churches and auditoriums
> - Recording and broadcast studios
> - Boardrooms and meeting rooms
> - Home theatres
> - Restaurants and hospitality
> - Open-plan offices
> - Schools and universities
> - Healthcare facilities
>
> Delivered across all 47 counties in Kenya. Acoustic work pairs naturally with a professional sound system — treat the room, then tune the system, and speech finally becomes clear.

## Benefits *(6)*
1. **We Fix the Right Problem** — We diagnose treatment vs soundproofing first, so your budget solves the actual issue.
2. **Measured, Not Guessed** — We assess and, where needed, measure the room before designing.
3. **Clarity & Intelligibility** — Treatment that tames echo and reverberation so speech and music are clear.
4. **Genuine Sound Isolation** — Real soundproofing using mass, decoupling and sealing — not foam that does nothing for isolation.
5. **Aesthetic Finishes** — Acoustic solutions that look intentional, not industrial.
6. **Works With Your AV** — Acoustics designed alongside your sound system for the best possible result.

## Process
Use the Standard Process. (Step 1 emphasises acoustic assessment and measurement; Step 4 = "Verification and fine-tuning".)

## FAQ *(6)*
1. **What's the difference between acoustic treatment and soundproofing?** — Treatment controls sound inside a room (echo, clarity); soundproofing stops sound passing in or out (isolation). They're different jobs and different materials.
2. **Will acoustic foam soundproof my room?** — No. Foam absorbs reflections inside the room; it does not block sound through walls. Isolation needs mass and sealing.
3. **Do I need both?** — Sometimes — studios and worship centres often isolate the space and treat the interior. An assessment tells you which.
4. **How much does acoustic treatment cost in Kenya?** — It depends on room size and the problem — no single price. We quote after a free acoustic assessment.
5. **Do you treat churches and auditoriums?** — Yes — clarity in large, reverberant rooms is one of our core specialisms.
6. **Do you measure the room?** — We assess every space and measure where the problem needs it, so the design targets the real issue.

## Gallery
SLOT — real acoustic project photos only.

## Related services *(pick 3)*
Professional Sound Systems · AV Consultation & System Design · Conference & Boardroom Systems

---

# 5. Stage & Architectural Lighting

## Page setup
- **Post type:** Service · **Permalink:** `/services/lighting/`
- **Excerpt:** Stage, event and architectural lighting design and installation in Kenya — engineered for impact, control and reliability.

## Rank Math
- **Focus keyword:** stage lighting Kenya
- **SEO Title:** Stage & Architectural Lighting Kenya | Event & Venue Lighting | VTECH
- **Meta:** Stage, event and architectural lighting design and installation in Kenya — for churches, auditoriums, events, hotels and venues. Free site survey, quote in 24 hours.

## Tagline
Lighting that shapes the room — and the moment.

## Intro *(post body)*
> Lighting does more than make a space visible — it directs attention, sets mood and makes video look its best. VTECH designs and installs stage, event and architectural lighting engineered for your venue, your content and reliable, repeatable control.
>
> As an AV systems integrator, we design lighting to work with your sound and video — one coordinated system, not three that fight each other.
>
> **What we provide**
> - Stage and performance lighting
> - Architectural and facade lighting
> - Event and production lighting
> - LED wash, spot and beam fixtures
> - Moving lights
> - Lighting control consoles
> - DMX systems and networking
> - Dimming and power distribution
> - Truss and rigging
> - House-of-worship lighting
> - Lighting design and programming
> - Installation and commissioning
>
> **Where we install**
> - Churches and worship centres
> - Auditoriums and theatres
> - Hotels and hospitality
> - Corporate and events
> - Retail and architectural spaces
> - Stadiums and outdoor venues
> - Conference centres
> - Broadcast and studios
>
> Designed, installed and supported across all 47 counties in Kenya.

## Benefits *(6)*
1. **Designed for Your Space & Content** — Lighting planned around your venue, performances and camera, not a generic kit.
2. **Reliable, Repeatable Control** — DMX and console programming so scenes recall perfectly every time.
3. **Safe Rigging & Power** — Truss, rigging and power distribution engineered for safety and code.
4. **Energy-Efficient LED** — Modern LED fixtures that cut power and heat without cutting impact.
5. **Programmed & Ready to Run** — We program scenes and train your team, so it's usable from day one.
6. **Support & Spares** — A 12-month support window with spares and annual servicing available.

## Process
Use the Standard Process. (Step 4 = "Focusing, programming & commissioning".)

## FAQ *(6)*
1. **Do you do both stage and architectural lighting?** — Yes — performance/stage lighting and permanent architectural or facade lighting, plus event lighting.
2. **Can you program lighting scenes for our team?** — Yes — we program scenes and train your operators so recall is one button.
3. **Do you install church and worship lighting?** — Yes — worship venues are a core specialism, balancing stage impact with camera-friendly light.
4. **How much does a lighting installation cost in Kenya?** — It depends on venue and fixture count — no single price. We quote after a free survey.
5. **Can we hire lighting for a one-off event?** — Yes — ask about equipment hire for events; we also install permanent systems.
6. **Do you provide maintenance?** — Yes — a 12-month support window with spares and optional annual servicing.

## Gallery
SLOT — real lighting project photos only.

## Related services *(pick 3)*
Professional Sound Systems · LED Screens & Video Walls · AV Integration

---

# 6. AV Integration

## Page setup
- **Post type:** Service · **Permalink:** `/services/av-integration/`
- **Excerpt:** AV integration in Kenya — complete audio visual systems (sound, video, control, conferencing) designed, installed and supported as one by VTECH.

## Rank Math
- **Focus keyword:** AV integration Kenya
- **SEO Title:** AV Integration Kenya | Audio Visual Systems Integrator | VTECH
- **Meta:** AV integration in Kenya — VTECH designs, installs and integrates complete audio visual systems (sound, video, control, conferencing) that work as one. Book a consultation.

## Tagline
One integrator. One system. One point of accountability.

## Intro *(post body)*
> This is the page that says what VTECH really is. Anyone can supply a speaker or a screen — an **integrator** takes responsibility for the whole system: how sound, video, control and conferencing work together, reliably, as one. VTECH designs, installs, documents and supports complete AV systems so you have a single point of accountability, not a pile of parts and finger-pointing.
>
> **What we provide**
> - Complete AV system design
> - Multi-room and multi-system integration
> - Unified audio, video, control and conferencing
> - One-touch control systems
> - Signal distribution and AV-over-IP networking
> - System programming
> - Rack design and build
> - Technical documentation (drawings, schedules, signal flow)
> - Commissioning and testing
> - Training and handover
> - Ongoing support and maintenance
> - Upgrades and expansion
>
> **Where we install**
> - Corporate headquarters
> - Government and county facilities
> - Universities and colleges
> - Conference centres
> - Houses of worship
> - Hotels and hospitality
> - Media houses
> - Command and control rooms
>
> Delivered and supported across all 47 counties in Kenya and select East Africa projects.

## Benefits *(6)*
1. **One Point of Accountability** — One team owns the result — design, install, commission and support — so nothing falls between vendors.
2. **Systems That Work as One** — Audio, video, control and conferencing engineered to operate together, not side by side.
3. **Engineered, Documented Design** — Drawings, equipment schedules and signal-flow diagrams — a system you can maintain and extend.
4. **Scalable & Future-Ready** — Designed with headroom so it grows with you instead of being ripped out.
5. **One-Touch Simplicity** — Complex systems that are simple to operate for everyday users.
6. **Long-Term Partnership** — Support, maintenance and upgrades — a relationship, not a transaction.

## Process
Use the Standard Process. (Step 2 emphasises system architecture, specification and documentation.)

## FAQ *(6)*
1. **What is AV integration?** — It's designing and connecting all the AV parts — sound, video, control, conferencing — into one system that works reliably and is simple to use.
2. **Can you integrate our existing equipment?** — Wherever it's compatible, yes — we assess and reuse what makes sense to protect your investment.
3. **Do you provide technical documentation?** — Yes — drawings, equipment schedules and signal-flow diagrams, so the system is maintainable.
4. **Can you handle multi-site or multi-room rollouts?** — Yes — we standardise rooms across sites to a common, manageable design.
5. **How much does an integrated AV system cost in Kenya?** — It depends on scope and complexity — no single price. We design first, then quote a fixed, itemised price.
6. **Do you support the system after installation?** — Yes — a 12-month support window with maintenance contracts and upgrades available.

## Gallery
SLOT — real integration project photos only.

## Related services *(pick 3)*
Conference & Boardroom Systems · AV Consultation & System Design · Professional Sound Systems

---

# 7. AV Consultation & System Design

## Page setup
- **Post type:** Service · **Permalink:** `/services/av-consultation/`
- **Excerpt:** AV consultation and system design in Kenya — engineering-led design, specification and documentation for AV projects, new builds and upgrades.

## Rank Math
- **Focus keyword:** AV system design Kenya *(kept distinct from "AV consultation Kenya" on the Book-a-Consultation page, to avoid cannibalisation)*
- **SEO Title:** AV System Design Kenya | AV Consultation & Engineering | VTECH
- **Meta:** AV consultation and system design in Kenya — independent, engineering-led AV design, specification and documentation for new builds and upgrades. Book a consultation.

## Tagline
Get the design right before anyone buys a thing.

## Intro *(post body)*
> The most expensive AV mistakes are made before installation — in the design. VTECH provides engineering-led AV consultation and system design: we define what you actually need, design the system around your space and budget, and document it so it can be built correctly — by us or anyone else.
>
> Because we're engineers first, our advice is about the right outcome, not the biggest invoice.
>
> **What we provide**
> - Needs analysis and requirements gathering
> - Site survey and acoustic assessment
> - AV system design and architecture
> - Equipment specification
> - Signal-flow and rack design
> - Technical drawings and documentation
> - Budgeting and project phasing
> - Tender and specification support
> - Design for new builds and fit-outs
> - Design review of existing systems
> - Standards-based design
> - Independent, vendor-neutral advice
>
> **Who we help**
> - Architects and consultants
> - Corporates and developers
> - Government and county projects
> - Universities and colleges
> - Houses of worship
> - Hotels and hospitality
> - New builds and fit-outs
> - Event and conference venues
>
> Serving projects across all 47 counties in Kenya.

## Benefits *(6)*
1. **Design Before You Spend** — Decisions made on paper are far cheaper to change than decisions made on site.
2. **Engineering-Led, Not Sales-Led** — Advice aimed at the right result, independent of any single brand.
3. **Full Documentation** — Drawings, schedules and signal flow that any competent installer can build to.
4. **Budget & Phasing Clarity** — A design you can budget accurately and roll out in stages.
5. **Standards-Compliant** — Designed to recognised standards, not guesswork.
6. **Buildable by Anyone** — Documentation good enough to tender — you're never locked in.

## Process
Use the Standard Process. (This service often ends at Step 2 — design + documentation — with build optional.)

## FAQ *(6)*
1. **What does an AV consultation include?** — Understanding your needs, surveying the space, and producing a system design, specification and documentation you can act on.
2. **Do you work with architects and new builds?** — Yes — we design AV into new builds and fit-outs alongside architects and consultants.
3. **Can you review a design or system we already have?** — Yes — we provide independent design reviews and recommendations.
4. **Do you only design what you install?** — No — our documentation is built to tender, so you can build with whoever you choose.
5. **How much does AV design cost in Kenya?** — It depends on project scope — we scope and quote the design work up front.
6. **What documentation do we get?** — System drawings, equipment schedules and signal-flow diagrams, plus budget and phasing guidance.

## Gallery
SLOT — real design/consultation project photos only (drawings, site surveys, completed designs).

## Related services *(pick 3)*
AV Integration · Acoustic Design & Soundproofing · Professional Sound Systems

---

# 8. Digital Signage

## Page setup
- **Post type:** Service · **Permalink:** `/services/digital-signage/`
- **Excerpt:** Digital signage in Kenya — displays, video walls, players and content management for retail, corporate, hospitality and wayfinding.

## Rank Math
- **Focus keyword:** digital signage Kenya
- **SEO Title:** Digital Signage Kenya | Screens, Displays & Content Management | VTECH
- **Meta:** Digital signage in Kenya — commercial screens, video walls, players and content management for retail, corporate, hospitality and wayfinding. Free survey, quote in 24 hours.

## Tagline
The right message, on the right screen, at the right time.

## Intro *(post body)*
> Digital signage only works when the screens, the players, the network and the content all pull together. VTECH supplies and installs commercial-grade digital signage — from a single reception screen to a multi-site network — and sets up the content management so your team controls it all remotely.
>
> **What we provide**
> - Commercial display screens
> - LED video walls
> - Menu boards and price displays
> - Wayfinding and directory displays
> - Media players
> - Content management systems (CMS)
> - Scheduling and remote management
> - Video-wall controllers
> - Mounting and network infrastructure
> - Content setup and templates
> - Installation
> - Support and spares
>
> **Where we install**
> - Retail stores and malls
> - Corporate lobbies and offices
> - Hotels and restaurants
> - Banks and branches
> - Hospitals and healthcare
> - Transport hubs and wayfinding
> - Schools and universities
> - Events and exhibitions

## Benefits *(6)*
1. **Displays Built for 24/7** — Commercial-grade screens rated for continuous use, not consumer TVs.
2. **Remote Content Control** — Update any screen, anywhere, from one dashboard.
3. **Scheduling & Automation** — Content that changes by time, day or location automatically.
4. **Reliable, Managed Network** — Players and network set up to stay online and recover cleanly.
5. **Scalable Across Sites** — Start with one screen; grow to a national network on the same platform.
6. **Support & Spares** — A 12-month support window with a spares strategy.

## Process
Use the Standard Process. (Step 4 = "Content setup, scheduling & testing".)

## FAQ *(6)*
1. **What is digital signage?** — Networked screens showing managed content — adverts, menus, wayfinding, information — updated centrally.
2. **Can we manage content remotely?** — Yes — you control screens from a single CMS dashboard, from anywhere.
3. **Can you do a single screen or a whole network?** — Both — from one reception display to a multi-site rollout on one platform.
4. **How much does digital signage cost in Kenya?** — It depends on screen count, size and CMS — no single price. We quote after a free survey.
5. **Do you do menu boards and wayfinding?** — Yes — menu boards, price displays, directories and wayfinding are all supported.
6. **What about maintenance and spares?** — A 12-month support window with a spares strategy and annual maintenance available.

## Gallery
SLOT — real digital signage photos only.

## Related services *(pick 3)*
LED Screens & Video Walls · AV Integration · Conference & Boardroom Systems

---

# 9. Video Conferencing

## Page setup
- **Post type:** Service · **Permalink:** `/services/video-conferencing/`
- **Excerpt:** Video conferencing installation in Kenya — Zoom, Teams and Google Meet room systems with cameras, mics and one-touch control.

## Rank Math
- **Focus keyword:** video conferencing Kenya
- **SEO Title:** Video Conferencing Kenya | Zoom, Teams & Meet Room Systems | VTECH
- **Meta:** Video conferencing installation in Kenya — Zoom, Teams and Google Meet room systems with cameras, microphones and one-touch control. Free survey, quote in 24 hours.

## Tagline
Meetings that connect the first time, every time.

## Intro *(post body)*
> Video conferencing lives or dies on audio and reliability. VTECH installs certified room systems for Zoom, Microsoft Teams and Google Meet — cameras, microphones, speakers and one-touch control — engineered so remote participants see and hear everyone clearly, and meetings start on time. (For full meeting-room builds, see Conference & Boardroom.)
>
> **What we provide**
> - Zoom Rooms, Teams Rooms and Google Meet systems
> - PTZ and auto-framing cameras
> - Ceiling and beamforming microphones
> - Speakers, DSP and acoustic echo cancellation
> - Displays
> - One-touch room controllers
> - BYOD and wireless content sharing
> - Room-scheduling panels
> - Multi-site standardisation
> - Integration with your UC platform
> - Installation
> - Support and maintenance
>
> **Where we install**
> - Corporate offices
> - Government facilities and courts
> - Universities and colleges
> - Hotels and hospitality
> - Healthcare and telemedicine
> - Huddle and focus rooms
> - Auditoriums
> - Operations and briefing rooms

## Benefits *(6)*
1. **Platform-Certified Rooms** — Systems built and certified for Zoom, Teams or Meet — no improvising.
2. **Look & Sound Professional** — Good cameras plus proper mic coverage and echo cancellation.
3. **One-Touch Join** — Tap once to start or join — reliable for every user.
4. **Standardised Across Rooms** — Every room works the same way, easy to support.
5. **Reliable & Supported** — Engineered for consistency, backed by 12-month support.
6. **Integrates With Your IT** — Works with your platform, calendar and network.

## Process
Use the Standard Process. (Step 4 = "Testing, platform certification & calibration".)

## FAQ *(6)*
1. **Which platforms do you support?** — Zoom, Microsoft Teams and Google Meet — configured to your standard.
2. **What camera and mic do we need?** — It depends on room size and layout — we specify the right coverage during the survey.
3. **Isn't a laptop and webcam enough?** — For a quick huddle, maybe; for reliable, professional rooms, a certified system is far better.
4. **How much does a VC room cost in Kenya?** — It depends on room size and platform — no single price. We quote after a free survey.
5. **Can you standardise rooms across sites?** — Yes — a common design across all rooms, easy to manage and support.
6. **Do you support hybrid meetings?** — Yes — rooms are designed so in-person and remote participants have an equal experience.

## Gallery
SLOT — real VC room photos only.

## Related services *(pick 3)*
Conference & Boardroom Systems · AV Integration · Professional Sound Systems

---

# 10. Equipment Hire  *(TOP-LEVEL PAGE — different rules)*

> **Important:** Equipment Hire is the standalone `/equipment-hire/` page (backed by the `hire_package` CPT),
> NOT a Service CPT entry. The theme does **not** auto-emit Service or FAQPage schema here, which flips two rules:
> - **Rank Math Schema Type: Service** (set it here — the theme won't).
> - **FAQ: add via a Rank Math FAQ block** (the theme won't emit FAQPage on this page).
> Everything else (no fabricated prices, real photos only) still holds. This is page/body content, not ACF fields.
> Individual hire packages live in `content/04-hire-packages-copy.md`.

## Page setup
- **Page:** `/equipment-hire/`
- **Focus keyword:** AV equipment hire Kenya
- **SEO Title:** AV Equipment Hire Kenya | Audio Visual Equipment Rental | VTECH
- **Meta:** AV equipment hire in Kenya — sound systems, PA, microphones, LED screens, projectors, lighting and conference gear with delivery, setup and technical support.

## Page body
> Need professional AV for an event, conference or production — without buying it? VTECH hires out reliable, professionally-maintained equipment with delivery, setup and technical support, so it works when it matters. From a single PA for a church service to a full conference AV package, we've got it covered across Nairobi and beyond.
>
> **What you can hire**
> - Sound systems and PA
> - Speakers and subwoofers
> - Wireless and wired microphones
> - Mixing consoles
> - LED screens and video walls
> - Projectors and screens
> - Stage and event lighting
> - Conference and video-conferencing equipment
> - Audio processing (DSP)
> - Cabling, stands and power distribution
> - Backup equipment on standby
>
> **How hire works**
> 1. Tell us your event, venue and dates
> 2. We recommend the right equipment and send a fixed quote
> 3. Delivery and professional setup
> 4. Optional on-site technician for the event
> 5. Teardown and collection
>
> **Why hire from VTECH**
> - Professionally maintained, event-ready equipment
> - Delivery, setup and teardown handled
> - Optional technician on site
> - Backup gear on standby
> - Countrywide coverage
>
> **Hire packages** *(link each to its hire_package entry — only list packages you genuinely offer)*
> - Conference AV Package
> - Corporate Event AV Package
> - Wedding AV Package
> - Church Event AV Package
> - Outdoor Event AV Package

## FAQ *(add via Rank Math FAQ block)*
1. **What AV equipment can I hire?** — Sound and PA, microphones, mixers, LED screens, projectors, lighting and conference gear — for events of any size.
2. **Do you deliver and set up?** — Yes — delivery, professional setup and teardown are included; an on-site technician is optional.
3. **Can I get a technician for the event?** — Yes — we can provide a technician to run the equipment during your event.
4. **How far ahead should I book?** — As early as possible for busy periods; contact us and we'll confirm availability for your dates.
5. **Do you cover events outside Nairobi?** — Yes — we deliver and support events countrywide.
6. **How much does AV equipment hire cost in Kenya?** — It depends on the equipment and duration — tell us your event and we'll send a fixed quote.

## CTA
Primary CTA: **Request Equipment Hire** (link to the Equipment Hire Request page).

---

# 11. AV Maintenance & Support

## Page setup
- **Post type:** Service · **Permalink:** `/services/maintenance/`
- **Excerpt:** AV maintenance and support in Kenya — service contracts, preventive maintenance, repairs and technical support for AV systems.

## Rank Math
- **Focus keyword:** AV maintenance Kenya
- **SEO Title:** AV Maintenance & Support Kenya | Service Contracts | VTECH
- **Meta:** AV maintenance and support in Kenya — service contracts, preventive maintenance, repairs and technical support for sound, video and conferencing systems.

## Tagline
Keep every system performing — long after install.

## Intro *(post body)*
> AV systems are an investment — they should keep working. VTECH provides maintenance and support that keeps your sound, video, lighting and conferencing systems reliable: preventive servicing, fast repairs, remote and on-site support, and clear service-level agreements. And we'll support systems we didn't install.
>
> **What we provide**
> - Annual maintenance contracts
> - Preventive maintenance visits
> - System health checks
> - Repairs and fault-finding
> - Firmware and software updates
> - Remote support
> - On-site support
> - Spares management
> - System re-tuning and recalibration
> - Emergency call-out
> - Support for systems we didn't install
> - Flexible SLA options
>
> **Who we support**
> - Corporates
> - Government and county facilities
> - Universities
> - Houses of worship
> - Hotels and hospitality
> - Conference centres
> - Media houses
> - Healthcare

## Benefits *(6)*
1. **Systems That Stay Reliable** — Preventive servicing catches problems before they become failures.
2. **Preventive, Not Just Reactive** — Scheduled health checks, not only emergency fixes.
3. **Fast Response** — Remote and on-site support with agreed response times.
4. **We Support Others' Systems Too** — Inherited a system from another installer? We'll maintain it.
5. **Spares on Hand** — A spares strategy so downtime is short.
6. **Flexible SLAs** — Support levels matched to how critical your AV is.

## Process
Use the Standard Process. (For maintenance this reads as: assessment → SLA proposal → onboarding → scheduled servicing → reporting → renewal.)

## FAQ *(6)*
1. **What's included in a maintenance contract?** — Preventive visits, health checks, repairs, updates and support — scoped to an SLA that fits your systems.
2. **Do you support systems you didn't install?** — Yes — we assess and take on systems installed by others.
3. **What are your response times?** — Agreed in your SLA — we offer remote and on-site support with defined targets.
4. **How much does AV maintenance cost in Kenya?** — It depends on your systems and SLA level — we scope and quote per site.
5. **Do you offer remote support?** — Yes — many issues are resolved remotely; on-site follows where needed.
6. **Can we get emergency support?** — Yes — emergency call-out is available, and priority response can be built into your SLA.

## Gallery
SLOT — real service/maintenance photos only.

## Related services *(pick 3)*
AV Integration · Professional Sound Systems · Conference & Boardroom Systems

---

## Build order (highest commercial value first)
Sound Systems → LED Screens → Conference & Boardroom → AV Integration → Equipment Hire → Acoustic Design → then the rest (Lighting, AV Consultation, Digital Signage, Video Conferencing, AV Maintenance).

## Companion files
- `content/01-homepage-copy.md` — homepage copy
- `content/03-industry-pages-copy.md` — 9 industry/sector pages
- `content/04-hire-packages-copy.md` — the 5 hire packages that sit under Equipment Hire
