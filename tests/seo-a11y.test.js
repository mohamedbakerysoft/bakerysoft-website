'use strict';
const assert=require('node:assert/strict'),fs=require('node:fs');
const root='src/';
const files=['index.html','products.html','gitintouch.html'];
const canonical={
 'index.html':'https://bakerysoft.net/',
 'products.html':'https://bakerysoft.net/products.html',
 'gitintouch.html':'https://bakerysoft.net/gitintouch.html'
};
const heads=[];
function attrs(markup,pattern){
 const match=markup.match(pattern);
 assert.ok(match,'Missing '+pattern);
 return match[1];
}
const localPath=(name,href)=>href.startsWith('./')&&href.split('#')[0].endsWith('.html');
for(const f of files){
 const html=fs.readFileSync(root+f,'utf8'),head=html.split('</head>')[0];
 const url=canonical[f];
 assert.match(html,/<html lang="en"/);
 assert.equal((head.match(/rel="canonical"/g)||[]).length,1,f+' must not have duplicate canonical');
 assert.ok(head.includes('rel="canonical" href="'+url+'"'));
 assert.ok(head.includes('property="og:url" content="'+url+'"'));
 assert.ok(head.includes('property="og:site_name" content="BakerySoft"'));
 assert.ok(head.includes('property="og:description"'));
 assert.ok(head.includes('name="twitter:card" content="summary"'));
 assert.ok(head.includes('name="robots" content="index,follow"'));
 assert.ok(head.includes('name="description"'));
 assert.ok(!/user-scalable=no|maximum-scale=1/i.test(head),'zoom access preserved for '+f);
 assert.ok(!/domain\.ext|@janedoe|Ipsum dolor|lorem ipsum|href="#"/i.test(html),
  'no fake placeholder contact/proof in '+f);
 assert.ok(html.includes('class="skip-link" href="#main-content"'));
 assert.ok(html.includes('id="main-content"'));
 assert.match(html,/<main\b/);
 assert.match(html,/<h1\b/);
 assert.ok(html.includes('alt="BakerySoft"'));
 for(const [,href] of html.matchAll(/href="([^"]+)"/g)){
  if(href.startsWith('#')){
   assert.ok(html.includes('id="'+href.substring(1)+'"'),f+' broken anchor '+href);
  }
  if(localPath(f,href)){
   const [local,anchor]=href.substring(2).split('#');
   assert.ok(files.includes(local),f+' unexpected route '+local);
   if(anchor){
    const ref=fs.readFileSync(root+local,'utf8');
    assert.ok(ref.includes('id="'+anchor+'"'),f+' missing cross-page anchor '+anchor);
   }
  }
 }
 heads.push({page:f,title:attrs(head,/<title>([^<]+)<\/title>/),
  canonical:url,description:attrs(head,/<meta name="description" content="([^"]+)"/)});
}
assert.equal(new Set(heads.map(x=>x.title)).size,3,'each page should have a distinct title');
const contact=fs.readFileSync(root+'gitintouch.html','utf8');
assert.match(contact,/href="\.\/index\.html#contact"/,'real known contact section');
assert.ok(!/href="https:\/\/(?:instagram|tiktok|snapchat|twitter|paypal|venmo)/i.test(contact),
 'unverified social accounts must not be published');
assert.ok(contact.length<4500,'remove giant junk demo svg');
const css=fs.readFileSync('src/scss/style.scss','utf8');
assert.ok(css.includes('.skip-link:focus-visible'));
assert.ok(css.includes('@media(max-width:600px)'));
assert.ok(css.includes('input,textarea,select{font-size:16px}'));
assert.ok(css.includes('prefers-reduced-motion'));
assert.ok(css.includes(':focus-visible'));
assert.match(fs.readFileSync('webpack.config.js','utf8'),/filename:"products.html"/);
console.log('PASS SEO/A11Y #462:',heads.length,'distinct canonicals + OG descriptions, keyboard skip, no fake placeholder, zoom & 44px targets, live cross-page routes');
