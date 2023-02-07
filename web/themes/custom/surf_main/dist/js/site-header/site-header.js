!function(){var s;function i(e){return(i="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function u(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function n(e,t){for(var o=0;o<t.length;o++){var n=t[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,function(e){e=function(e,t){if("object"!==i(e)||null===e)return e;var o=e[Symbol.toPrimitive];if(void 0===o)return("string"===t?String:Number)(e);o=o.call(e,t||"default");if("object"!==i(o))return o;throw new TypeError("@@toPrimitive must return a primitive value.")}(e,"string");return"symbol"===i(e)?e:String(e)}(n.key),n)}}function r(e,t,o){t&&n(e.prototype,t),o&&n(e,o),Object.defineProperty(e,"prototype",{writable:!1})}(s=jQuery)(document).ready(function(){"use strict";for(var e=function(){function i(e){u(this,i),this.domNode=e,this.buttonNode=e.querySelector("button"),this.buttonNode.setAttribute("aria-expanded","false"),this.menuNode=e.querySelector("ul.menu[data-depth='1']"),this.menuitemNodes=[],this.firstMenuitem=!1,this.lastMenuitem=!1,this.buttonNode.addEventListener("keydown",this.onButtonKeydown.bind(this)),this.buttonNode.addEventListener("click",this.onButtonClick.bind(this));for(var t=e.querySelectorAll(".menu__link"),o=0;o<t.length;o++){var n=t[o];this.menuitemNodes.push(n),n.addEventListener("keydown",this.onMenuitemKeydown.bind(this)),n.addEventListener("mouseover",this.onMenuitemMouseover.bind(this)),this.firstMenuitem||(this.firstMenuitem=n),this.lastMenuitem=n}e.addEventListener("focusin",this.onFocusin.bind(this)),e.addEventListener("focusout",this.onFocusout.bind(this)),window.addEventListener("mousedown",this.onBackgroundMousedown.bind(this),!0)}return r(i,[{key:"setFocusToMenuitem",value:function(t){this.menuitemNodes.forEach(function(e){e===t&&t.focus()})}},{key:"setFocusToFirstMenuitem",value:function(){this.setFocusToMenuitem(this.firstMenuitem)}},{key:"setFocusToLastMenuitem",value:function(){this.setFocusToMenuitem(this.lastMenuitem)}},{key:"setFocusToPreviousMenuitem",value:function(e){e=e===this.firstMenuitem?this.lastMenuitem:(e=this.menuitemNodes.indexOf(e),this.menuitemNodes[e-1]);return this.setFocusToMenuitem(e),e}},{key:"setFocusToNextMenuitem",value:function(e){e=e===this.lastMenuitem?this.firstMenuitem:(e=this.menuitemNodes.indexOf(e),this.menuitemNodes[e+1]);return this.setFocusToMenuitem(e),e}},{key:"openPopup",value:function(){this.menuNode.classList.add("open"),this.buttonNode.setAttribute("aria-expanded","true")}},{key:"closePopup",value:function(){this.isOpen()&&(this.buttonNode.setAttribute("aria-expanded","false"),this.menuNode.classList.remove("open"))}},{key:"isOpen",value:function(){return"true"===this.buttonNode.getAttribute("aria-expanded")}},{key:"onFocusin",value:function(){this.domNode.classList.add("focus")}},{key:"onFocusout",value:function(){this.domNode.classList.remove("focus")}},{key:"onButtonKeydown",value:function(e){var t=!1;switch(e.key){case" ":case"Enter":if(!this.isOpen()){this.openPopup(),this.setFocusToFirstMenuitem(),t=!0;break}this.closePopup(),this.buttonNode.focus();case"Down":case"ArrowDown":this.openPopup(),this.setFocusToFirstMenuitem(),t=!0;break;case"Esc":case"Escape":this.closePopup(),this.buttonNode.focus(),t=!0;break;case"Up":case"ArrowUp":this.openPopup(),this.setFocusToLastMenuitem(),t=!0;break;case"Tab":t=!1}t&&(e.stopPropagation(),e.preventDefault())}},{key:"onButtonClick",value:function(e){this.isOpen()?(this.closePopup(),this.buttonNode.focus()):(this.openPopup(),this.setFocusToFirstMenuitem()),e.stopPropagation(),e.preventDefault()}},{key:"onMenuitemKeydown",value:function(e){var t=e.currentTarget,o=e.key,n=!1;if(!(e.ctrlKey||e.altKey||e.metaKey)){switch(o){case" ":window.location.href=t.href;break;case"Esc":case"Escape":this.closePopup(),this.buttonNode.focus(),n=!0;break;case"Up":case"ArrowUp":this.setFocusToPreviousMenuitem(t),n=!0;break;case"ArrowDown":case"Down":this.setFocusToNextMenuitem(t),n=!0;break;case"Home":case"PageUp":this.setFocusToFirstMenuitem(),n=!0;break;case"End":case"PageDown":this.setFocusToLastMenuitem(),n=!0;break;case"Tab":var i=s(t).parent().is(":last-child");this.isOpen()&&!i?n=!1:this.closePopup()}n&&(e.stopPropagation(),e.preventDefault())}}},{key:"onMenuitemMouseover",value:function(e){e.currentTarget.focus()}},{key:"onBackgroundMousedown",value:function(e){this.domNode.contains(e.target)||this.isOpen()&&(this.closePopup(),this.buttonNode.focus())}}]),i}(),t=document.querySelectorAll(".menu--main[data-depth='0'] > li"),o=0;o<t.length;o++)new e(t[o]);s("#nav-trigger").on("click",function(e){var t,o;function n(e){for(var t=0;t<e.length;t++)e[t].tabIndex=-1}function i(e){for(var t=0;t<e.length;t++)e[t].removeAttribute("tabindex")}s("body").toggleClass("js-prevent-scroll"),s("#nav-trigger").toggleClass("checked"),s("#nav-trigger").hasClass("checked")?(s("#nav-trigger").attr("aria-expanded","true"),1024<s(window).width()&&(t=document.querySelectorAll(".menu--utility-nav > li > a"),o=document.querySelectorAll(".search-form input"),n(t),n(o))):(s("#nav-trigger").attr("aria-expanded","false"),t=document.querySelectorAll(".menu--utility-nav > li > a"),o=document.querySelectorAll(".search-form input"),i(t),i(o))})})}();