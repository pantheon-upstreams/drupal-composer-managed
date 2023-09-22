Drupal.behaviors.surfMobileMenu={attach(a){var a=once("surf-mobile-menu",a.querySelector(".mobile-menu"));if(0!==a.length){var n=a[0];const r="is-open";let t,e;const s=()=>{n.classList.remove(r),e.setAttribute("aria-expanded","false")},i=()=>{n.classList.add(r),e.setAttribute("aria-expanded","true")},o=e=>{(n.classList.contains(r)?s:i)()},l=e=>{"Enter"!==e.key&&" "!==e.key||(e.preventDefault(),(n.classList.contains(r)?s:i)()),"Escape"!==e.key&&"End"!==e.key||s()},c=e=>{e.target===t&&s()};a=n,t=a.querySelector(".mobile-menu__flyout"),(e=a.querySelector(".mobile-menu__trigger")).addEventListener("click",o),e.addEventListener("keydown",l),a.addEventListener("click",c)}}},Drupal.behaviors.surfMobileMove={attach(e){const l=once("surf-move-mobile-destination",e.querySelector(".mobile-menu__flyout-content"));[{id:"surf-move-mobile-01",class:".search-form",breakpoint:992},{id:"surf-move-mobile-02",class:".menu__name--main",breakpoint:1400},{id:"surf-move-mobile-03",class:".menu__name--utility-nav",breakpoint:992}].forEach(a=>{var n=once(a.id,e.querySelector(a.class));if(0!==n.length&&0!==l.length){var r=n[0];var s=l[0];n=a.breakpoint;const i=window.matchMedia(`(min-width: ${n}px)`);let e,t;const o=()=>{i.matches?(t="desktop",r.classList.contains("menu__name--main")?e.querySelector(".site-branding").after(r):e.prepend(r)):(t="mobile",r.classList.contains("search-form")?s.prepend(r):s.append(r))};e=r.parentElement,i.addEventListener("change",o),o()}})}},Drupal.behaviors.surfScrolled={attach(s){[".region__name--site-alert",".region__name--header-primary",".region__name--header-utility"].forEach(e=>{e=once("surf-scrolled",s.querySelector(e));if(0!==e.length){var t=e[0];const a="scrolled",n=0,r=()=>{window.scrollY>n?t.classList.add(a):t.classList.remove(a)};document.addEventListener("scroll",r)}})}};