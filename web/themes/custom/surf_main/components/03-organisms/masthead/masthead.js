!function(){"use strict";var n,o,r={5:function(e,t,r){r.r(t),r.d(t,{a11yDropdown:function(){return n}});const n=t=>{const r="is-open";let n,e;const o=()=>{t.classList.add(r),e.setAttribute("aria-expanded","true")},a=()=>{t.classList.remove(r),e.setAttribute("aria-expanded","false")},s=e=>{(t.classList.contains(r)?a:o)(t)},i=e=>{"Enter"!==e.key&&" "!==e.key||(e.preventDefault(),(t.classList.contains(r)?a:o)(t))},c=e=>{e.target===n&&a(t)};var d;d=t,n=d.querySelector(".dropdown__content-wrapper"),(e=d.querySelector(".dropdown__trigger")).addEventListener("click",s),e.addEventListener("keydown",i),n&&n.addEventListener("click",c)}},7:function(e,t,r){r.r(t),r.d(t,{scrolled:function(){return n}});const n=(e,t)=>{const r="scrolled",n=()=>{window.scrollY>t?e.classList.add(r):e.classList.remove(r)};document.addEventListener("scroll",n)}}},a={};function s(e){var t=a[e];return void 0!==t||(t=a[e]={exports:{}},r[e](t,t.exports,s)),t.exports}s.d=function(e,t){for(var r in t)s.o(t,r)&&!s.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},s.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},(s.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})})({}),n=s(5),o=s(7),Drupal.behaviors.surfMasthead={attach(e){var t=once("surf-masthead",e.querySelector(".o-masthead")),r=once("surf-masthead-flyout",e.querySelector(".o-masthead__flyout"));const c=once("surf-masthead-destination",e.querySelector(".o-masthead__flyout-content"));0!==r.length&&(0,n.a11yDropdown)(r[0]),0!==t.length&&(0,o.scrolled)(t[0],60),[{id:"surf-move-mobile-01",class:".m-search-bar",breakpoint:992},{id:"surf-move-mobile-02",class:".m-menu--primary-menu",breakpoint:1400},{id:"surf-move-mobile-03",class:".m-menu--utility-menu",breakpoint:992}].forEach(r=>{var n=once(r.id,e.querySelector(r.class));if(0!==r.length&&0!==c.length){var o=n[0];var a=c[0];n=r.breakpoint;const s=window.matchMedia(`(min-width: ${n}px)`);let e,t;const i=()=>{s.matches?(t="desktop",o.classList.contains("m-menu--primary-menu")?e.querySelector(".site-branding").after(o):e.prepend(o)):(t="mobile",o.classList.contains("m-search-bar")?a.prepend(o):a.append(o))};e=o.parentElement,s.addEventListener("change",i),i()}})}}}();