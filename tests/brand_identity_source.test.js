'use strict';
const assert=require('node:assert/strict'),fs=require('node:fs');
const scss=fs.readFileSync('src/scss/style.scss','utf8');
const spec=fs.readFileSync('docs/BRAND_IDENTITY.md','utf8');
const html=fs.readFileSync('src/index.html','utf8');
for(const [token,value] of Object.entries({'--brand-ink':'#091924','--brand-jade':'#169f84','--brand-pearl':'#f6f9f7','--brand-mist':'#e5f3ee','--brand-hairline':'#d3e0da'})){
 assert.ok(scss.includes(token+':'+value),'SCSS token '+token);assert.ok(spec.includes('`'+token+'`'));
}
assert.ok(scss.includes('focus-visible'),'keyboard focus needed');
assert.ok(scss.includes('prefers-reduced-motion:reduce'));
assert.ok(scss.includes('.site-header .button-small{display:inline-flex'),'primary CTA accessible on mobile');
assert.ok(html.includes('BakerySoft'));
console.log('PASS: BakerySoft restrained identity tokens, keyboard focus, mobile CTA and guideline parity');
