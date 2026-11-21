# BakerySoft: copy audit and bilingual direction (2026-11-21 draft)

## Scope, actual sources

- Source: `src/index.html` (English marketing site) and `src/gitintouch.html` (legacy generated page).
- Public source repository: `mohamedbakerysoft/bakerysoft-website` at base commit `ba1b98b5989703777b642b9f40e387fae9388fd0`.
- This is a content audit/English rewrite and **proposed Arabic localization direction**, not a published bilingual website. Arabic copy is not deployed; it needs correct RTL and content review before release.

## Changes and editorial rationale

| Location | Before | After | Reason |
| --- | --- | --- | --- |
| Hero headline | Digital products, carefully made. | Clear thinking. Well-built software. | Distinctive, specific without unverifiable ROI |
| Hero description | “clean, fast” and “grow with confidence” | Design + engineering, complexity made clear, relationship beyond launch | Avoid unsupported speed promises |
| Hero qualities | Reliable delivery / Built to scale | Thoughtful discovery / Practical engineering / Continuous improvement | Name working practices rather than guaranteed results |
| Hero mock “performance” | **98 Performance** | **01 Clarity first** | 98 has no accompanying measured Lighthouse evidence, must never be shown as real score |
| Hero mock status | Production ready | Designed with intent | Do not claim an untested product is production ready |
| Services headline | Everything you need to build it right. | From idea to working product. | Less overpromising and easier to scan |
| Process headline | Better outcomes. | Careful execution. | No unsupported customer outcomes |
| Footer | Thoughtful digital work for ambitious businesses. | Considered design. Dependable engineering. BakerySoft. | Cohesive brand message |
| Legacy contact title | BackerySoft | BakerySoft — Get in touch | Correct spelling and clear context |

## Arabic localization draft — **not currently rendered**

- Hero: **نفكر بوضوح. ونبني برمجيات بإتقان.**
- Supporting text: «نصمم ونطور مواقع وتطبيقات ويب تجعل العمليات المعقدة أوضح وأسهل، من أول نقاش وحتى ما بعد الإطلاق.»
- Primary CTA: **ناقشنا في مشروعك**
- Secondary CTA: **اكتشف خدماتنا**
- Services: **من الفكرة إلى منتج عملي**
- Process: **قرارات واضحة. وتنفيذ مدروس.**
- Contact: **احكيلنا عن فكرتك أو التحدي اللي بتواجهه.**

## Items needing evidence or explicit QA

1. The `13+ years` founder experience line appears in source; retain only after the owner verifies the timeline. No fabricated customers, testimonials, revenues, satisfaction or performance scores.
2. Current form markup points to `./contactUs/index.php`, but this path is not present in the same source repository. Before publication, independently validate the actual runtime backend, error and success states, CSRF/spam controls; do not submit real personal data as a test.
3. Production URL metadata is English only. Full Arabic launch needs `lang`, `dir`, language switcher, localized URLs, hreflang and real readability checks.
4. Do not insert fake logos/case studies or 4K score badges in Hero. Use verified product screenshots and documented outcomes only.
5. This draft should be reviewed in a browser on mobile/desktop and compiled by the hosted CI workflow before merging to `main`.
