# BakerySoft — Brand Identity (2026 / draft)

## Positioning
A software engineering studio for teams that need reliable, thoughtfully designed web products. **Brand promise:** considered design, practical engineering, accountable delivery. Do not imply named clients, measured performance, certifications or project outcomes without verified evidence.

## Visual system

| Token | Value | Usage |
| --- | --- | --- |
| `--brand-ink` | `#091924` | Primary text, strong dark sections, code/product framing |
| `--brand-jade` | `#169f84` | Primary accent, focus moments and product cues |
| `--accent-dark` | `#0b715b` | CTA hover, accessible accent copy |
| `--brand-pearl` | `#f6f9f7` | Editorial page ground / breathable whitespace |
| `--brand-mist` | `#e5f3ee` | Featured surfaces, case-study highlights |
| `--brand-hairline` | `#d3e0da` | Subtle dividing lines and form borders |

Typography: **Manrope** for expressive editorial headings and **DM Sans** for reading/UI. Avoid introducing other display typefaces without a design review. Large headings pair with short, precise descriptions; use real product screenshots instead of mock performance numbers. Language should sound human, restrained and technically credible.

## Component guidelines

- **Primary CTA:** one clear action in the hero, same family of pill button throughout the site; never hide all navigation on mobile.
- **Cards:** 20px radius, understated border, mild elevation only on hover, no loud rainbow gradients. Respect reduced-motion settings.
- **Product preview:** show verified work, not placeholder screenshots labelled as customers or fabricated benchmarks. If images are unavailable, use an abstract brand illustration without numbers.
- **Trust:** verify any years of experience, testimonials, metrics, published prices or client names against source records before release.
- **Interaction:** keyboard `:focus-visible` ring, legible text at 320px, accessible form labels and correct Arabic/English direction when localized.

## Rollout / QA

Stage the source changes on a dedicated feature branch in `mohamedbakerysoft/bakerysoft-website`; verify source build and responsive/a11y review. Do not deploy directly to `bakerysoft.net`. Record screenshots of mobile and desktop, verify contact endpoint separately, and keep a rollback commit. This draft does not claim a Lighthouse score or completed device audit.
