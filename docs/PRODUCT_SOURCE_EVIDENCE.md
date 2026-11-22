# BakerySoft #461 — product source & delivery evidence

Status: **staging/source only**. This feature branch is a clean, isolated Git worktree based on the verified SHA of existing draft PR #2, `632aa5d8187225484b20f0d8d4d7d16ef3166b21`, not an edit of the dirty `main` checkout. No production deployment or purchase flow is authorized here.

## Verified source references — 22 November 2026

| Reference | Evidence observed | Permitted page claim |
|---|---|---|
| HD Images demo | GET `https://hdimages.bakerysoft.net/` returned HTTP **200**. The server HTML title was `HD Images - Premium Wallpaper Demo`, and navigation paths included `/site/popular` and wallpaper categories. | A reachable demo preview. This **does not establish** that all features work, that the item is currently sold, that its PHP version is secure, or that it is production-ready. |
| CodeCanyon product listings | No current authoritative author/SKU sales page confirmed. | No item link, price, sales claims, or paid CTA. |
| YouTube Coins | Owner-project history references an existing script; no current demo, item listing or build release confirmed here. | Display clearly as **listing/demo under verification**, without fake screenshot or purchase link. |
| Customer case studies | No customer-approved case study evidence available in this workspace. | No client names, revenue outcomes, testimonials or case-study imagery. |
| Screenshots | None verified as actual HD Images / YouTube Coins screenshots in this website repository. | Clearly labeled screenshot placeholder only; never show generic stock images as product proof. |

## Implementation

- `src/products.html` is a separate accessible products/demo page with a real HD Images demo CTA, a carefully qualified YouTube Coins listing, no invented price, and an explicit customer case-study evidence policy.
- `src/index.html` now links to the page and includes a short product teaser with a working local route.
- `webpack.config.js` builds a third HTML entry `products.html`; `src/scss/style.scss` styles it with existing Ink / Pearl / Jade design tokens and reduced-motion/focus/mobile support.
- `tests/product-pages.test.js` verifies relative links, anchor destinations, the one sourced external demo, image/alt/focus and absence of fabricated prices, case studies or CodeCanyon links.

## Acceptance still pending

Real responsive desktop/mobile screenshots, independent customer/case-study permission, current CodeCanyon seller authorization and actual price/demos, full HD Images functional QA/security, owner approval and merge/deploy. A green local build is a source-level check, not a live-release acceptance. Do not overwrite or merge draft PR #2 without its review.
