!function(){"use strict";var t,r={2:function(e,t,r){r.r(t),r.d(t,{a11yAccordion:function(){return n}});const n=e=>{const r="expanded";let t,n,o;const c=(e,t)=>{e.classList.add(r),t.setAttribute("aria-expanded","true")},a=(e,t)=>{e.classList.remove(r),t.setAttribute("aria-expanded","false")},i=e=>{n=e.currentTarget.parentElement.parentElement,o=e.currentTarget,(n.classList.contains(r)?a:c)(n,o)},s=e=>{n=e.currentTarget.parentElement.parentElement,o=e.currentTarget,"Enter"!==e.key&&" "!==e.key||(e.preventDefault(),(n.classList.contains(r)?a:c)(n,o))};(t=e.querySelectorAll(".accordion__item")).forEach(e=>{e=e.querySelector(".accordion__trigger");e.addEventListener("click",i),e.addEventListener("keydown",s)})}}},n={};function o(e){var t=n[e];return void 0!==t||(t=n[e]={exports:{}},r[e](t,t.exports,o)),t.exports}o.d=function(e,t){for(var r in t)o.o(t,r)&&!o.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},(o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})})({}),t=o(2),Drupal.behaviors.surfAccordion={attach(e){e=once("surf-accordion",e.querySelectorAll(".m-accordion"));0!==e.length&&e.forEach(e=>{(0,t.a11yAccordion)(e)})}}}();