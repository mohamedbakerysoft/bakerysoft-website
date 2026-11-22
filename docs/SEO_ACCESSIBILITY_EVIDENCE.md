# BakerySoft 06 — SEO, responsive accessibility and measured source facts

**Source-only branch** built on exact products PR #3 head `a7c24e8ef1e913488650a419070ddd19f54230ac`. No staging/public deployment, no published performance scores, no actual device acceptance.

## Verified live URLs, 22 Nov 2026

With a bounded HTTP GET using the same browser-like user agent:
- `https://bakerysoft.net/` returned HTTP 200; live title `BakerySoft — Thoughtful web development`.
- `https://bakerysoft.net/products.html` returned HTTP 200 **but the HTML body was the homepage**, not the new product catalog (there was no `hd-images` product card marker). HTTP 200 **does not prove deployment**.
- `https://bakerysoft.net/gitintouch.html` returned HTTP 200 with the legacy template title `BackerySoft` and no canonical contact section. Local source previously contained `@janedoe`, `domain.ext/path`, and lorem-style placeholder copy. This is unverified/unsafe social proof and must not persist into publication.
- Homepage contact form references `./contactUs/index.php`. A live read-only GET check does NOT prove form delivery. **Never submit a real user message during QA**; full safe contact-form acceptance is a separate task (#463).

## Actual changes

- Three pages now have unique absolute canonical URL, title, meta description, Open Graph title/description/url/site and summary-card metadata (no fabricated social image).
- Responsive accessibility: removes `user-scalable=no` on contact; skip-to-content link, focus visibility, mobile 16px fields, real anchor targets and lazy footer logo, and reduced-motion.
- Replaces legacy contact-page demo template with a concise BakerySoft page whose CTA points to the existing main-site `#contact` anchor; no invented phone/email/social media or promise that posting works.
- Baseline/product-site build size and tests measured from local build. No Lighthouse, Web Vitals, traffic/SEO ranking or physical iPhone device numbers are inferred.

## QA & acceptance

Run `node tests/seo-a11y.test.js` and the updated source test `node tests/product-pages.test.js` to distinguish actual product CTA URLs from canonical URLs. Run Webpack production build and serve three generated HTML routes and their assets over loopback. Verify no unverified links/placeholder text in output.

Still pending: Lighthouse on real delivery, LCP/CLS/INP field data, live source deployment and contact form server, physical mobile screenshots and owner release approval.
