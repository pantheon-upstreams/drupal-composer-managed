!function(){"use strict";var t,r={11:function(e,t,r){r.r(t),r.d(t,{a11yMenu:function(){return n}});const n=(t,e)=>{const r=window.matchMedia(`(min-width: ${e}px)`),n="is-open";let a,o;const s=e=>{e.parentElement.classList.remove(n),e.setAttribute("aria-expanded","false")},i=e=>{e.parentElement.classList.add(n),e.setAttribute("aria-expanded","true")},c=()=>{a.forEach(e=>{s(e)})},u=e=>{(e.currentTarget.parentElement.classList.contains(n)?s:("desktop"===o&&c(),i))(e.currentTarget)},d=e=>{"Enter"!==e.key&&" "!==e.key||(e.preventDefault(),(e.currentTarget.parentElement.classList.contains(n)?s:("desktop"===o&&c(),i))(e.currentTarget))},l=e=>{if(("Escape"===e.key||"End"===e.key)&&e.currentTarget.closest("."+n)){var t=e.currentTarget.closest("."+n);const r=t.firstElementChild;t.classList.remove(n),r.setAttribute("aria-expanded","false"),setTimeout(e=>{r.focus()},1)}e.shiftKey||"Tab"!==e.key||e.currentTarget===a[a.length-1]&&c()},p=e=>{"desktop"!==o||t.contains(e.target)||c()},m=()=>{o=r.matches?"desktop":"mobile"};(a=t.querySelectorAll(".m-menu__link")).forEach(e=>{e.getAttribute("aria-haspopup")?(e.addEventListener("click",u),e.addEventListener("keydown",d)):e.addEventListener("keydown",l)}),window.addEventListener("click",p),r.addEventListener("change",m),m()}}},n={};function a(e){var t=n[e];return void 0!==t||(t=n[e]={exports:{}},r[e](t,t.exports,a)),t.exports}a.d=function(e,t){for(var r in t)a.o(t,r)&&!a.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},a.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},(a.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})})({}),t=a(11),Drupal.behaviors.surfPrimaryMenu={attach(e){e=once("surf-primary-menu",e.querySelectorAll(".m-menu.m-menu--primary"));0!==e.length&&e.forEach(e=>{(0,t.a11yMenu)(e,1440)})}}}();