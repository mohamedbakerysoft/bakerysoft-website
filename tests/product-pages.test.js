'use strict';
const assert=require('node:assert/strict'),fs=require('node:fs'),path=require('node:path');
const home=fs.readFileSync('src/index.html','utf8');
const products=fs.readFileSync('src/products.html','utf8');
const config=fs.readFileSync('webpack.config.js','utf8');
const scss=fs.readFileSync('src/scss/style.scss','utf8');
const pages={home,products};
const srcSet=new Set(['index.html','products.html','gitintouch.html']);
for(const [name,html] of Object.entries(pages)){
 assert.match(html,/<html lang="en">/,'English source lang for '+name);
 assert.match(html,/<meta name="viewport"/,'mobile viewport '+name);
 assert.match(html,/<main/,'semantic main '+name);
 assert.match(html,/<h1/,'visible h1 '+name);
 assert.match(html,/BakerySoft/);
 assert.ok(!/href=(?:"|')\s*(?:#|javascript:|https?:\/\/example\.com)(?:"|')/i.test(html),'no inert/unsafe links in '+name);
 for(const match of html.matchAll(/href="([^"]+)"/g)){
  const url=match[1];
  if(url.startsWith('#')){
   const id=url.substring(1);
   assert.ok(html.includes('id="'+id+'"'),name+' missing hash target '+id);
  }else if(url.startsWith('./')){
   const [rel,hash]=url.substring(2).split('#');
   if(rel.endsWith('.html'))assert.ok(srcSet.has(rel),name+' local HTML target missing '+rel);
   else if(rel.startsWith('images/'))assert.ok(fs.existsSync('src/'+rel),name+' icon missing '+rel);
   else assert.fail(name+' unexpected local URL '+url);
   if(hash&&rel==='index.html')assert.ok(home.includes('id="'+hash+'"'),name+' missing homepage target '+hash);
  }else if(url.startsWith('http')){
   // Canonical SEO links are first-party metadata, not demo/purchase CTAs.
   const approved=new Set(['https://hdimages.bakerysoft.net/',
     'https://bakerysoft.net/','https://bakerysoft.net/products.html']);
   assert.ok(approved.has(url),'only first-party canonical metadata and the verified demo may link externally: '+url);
  }
 }
}
assert.ok(home.includes('href="./products.html"'));
assert.ok(products.includes('id="hd-images"')&&products.includes('id="youtube-coins"'));
assert.ok(products.includes('HD Images')&&products.includes('Premium Wallpaper Demo'));
assert.ok(products.includes('Demo endpoint: HTTP 200 checked'));
assert.ok(products.includes('Listing and demo not verified'));
assert.ok(products.includes('Screenshot pending source review'));
assert.ok(products.includes('No approved screenshot yet'));
assert.ok(products.includes('No public customer case study'));
assert.ok(!/\$(?:19|29|49|99)|\d+\s+(?:sales|customers|reviews|five-star)/i.test(products),'no fabricated proof/price');
assert.ok(!/href="https?:\/\/codecanyon\.net\//.test(products),'no unverified CodeCanyon SKU');
assert.ok(config.includes('filename:"products.html"')&&config.includes('template: "./src/products.html"'));
assert.ok(scss.includes('.products-grid')&&scss.includes('@media(max-width:600px)'));
assert.ok(scss.includes(':focus-visible'),'keyboard focus preserved');
assert.ok(scss.includes('prefers-reduced-motion'),'motion accessibility preserved');
console.log('PASS BakerySoft #461: linked home/products/contact routes, only verified demo external CTA, no fake price/screenshot/client, mobile/accessibility, Webpack page configured');
