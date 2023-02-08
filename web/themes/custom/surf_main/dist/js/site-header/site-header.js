/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
(function ($) {
  $(document).ready(function () {
    /*
     *   This file is based on content that is licensed according to the W3C Software License at
     *   https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
     *
     */

    "use strict";

    var MenuButtonLinks = /*#__PURE__*/function () {
      function MenuButtonLinks(domNode) {
        _classCallCheck(this, MenuButtonLinks);
        this.domNode = domNode;
        this.buttonNode = domNode.querySelector("button");
        this.buttonNode.setAttribute("aria-expanded", "false");
        this.menuNode = domNode.querySelector("ul.menu[data-depth='1']");
        this.menuitemNodes = [];
        this.firstMenuitem = false;
        this.lastMenuitem = false;
        this.buttonNode.addEventListener("keydown", this.onButtonKeydown.bind(this));
        this.buttonNode.addEventListener("click", this.onButtonClick.bind(this));
        var nodes = domNode.querySelectorAll(".menu__link");
        for (var i = 0; i < nodes.length; i++) {
          var menuitem = nodes[i];
          this.menuitemNodes.push(menuitem);
          menuitem.addEventListener("keydown", this.onMenuitemKeydown.bind(this));
          menuitem.addEventListener("mouseover", this.onMenuitemMouseover.bind(this));
          if (!this.firstMenuitem) {
            this.firstMenuitem = menuitem;
          }
          this.lastMenuitem = menuitem;
        }
        domNode.addEventListener("focusin", this.onFocusin.bind(this));
        domNode.addEventListener("focusout", this.onFocusout.bind(this));
        window.addEventListener("mousedown", this.onBackgroundMousedown.bind(this), true);
      }
      _createClass(MenuButtonLinks, [{
        key: "setFocusToMenuitem",
        value: function setFocusToMenuitem(newMenuitem) {
          this.menuitemNodes.forEach(function (item) {
            if (item === newMenuitem) {
              newMenuitem.focus();
            }
          });
        }
      }, {
        key: "setFocusToFirstMenuitem",
        value: function setFocusToFirstMenuitem() {
          this.setFocusToMenuitem(this.firstMenuitem);
        }
      }, {
        key: "setFocusToLastMenuitem",
        value: function setFocusToLastMenuitem() {
          this.setFocusToMenuitem(this.lastMenuitem);
        }
      }, {
        key: "setFocusToPreviousMenuitem",
        value: function setFocusToPreviousMenuitem(currentMenuitem) {
          var newMenuitem, index;
          if (currentMenuitem === this.firstMenuitem) {
            newMenuitem = this.lastMenuitem;
          } else {
            index = this.menuitemNodes.indexOf(currentMenuitem);
            newMenuitem = this.menuitemNodes[index - 1];
          }
          this.setFocusToMenuitem(newMenuitem);
          return newMenuitem;
        }
      }, {
        key: "setFocusToNextMenuitem",
        value: function setFocusToNextMenuitem(currentMenuitem) {
          var newMenuitem, index;
          if (currentMenuitem === this.lastMenuitem) {
            newMenuitem = this.firstMenuitem;
          } else {
            index = this.menuitemNodes.indexOf(currentMenuitem);
            newMenuitem = this.menuitemNodes[index + 1];
          }
          this.setFocusToMenuitem(newMenuitem);
          return newMenuitem;
        }

        // Popup menu methods
      }, {
        key: "openPopup",
        value: function openPopup() {
          this.menuNode.classList.add("open");
          this.buttonNode.setAttribute("aria-expanded", "true");
        }
      }, {
        key: "closePopup",
        value: function closePopup() {
          if (this.isOpen()) {
            // this.buttonNode.removeAttribute("aria-expanded");
            this.buttonNode.setAttribute("aria-expanded", "false");
            this.menuNode.classList.remove("open");
          }
        }
      }, {
        key: "isOpen",
        value: function isOpen() {
          return this.buttonNode.getAttribute("aria-expanded") === "true";
        }

        // Menu event handlers
      }, {
        key: "onFocusin",
        value: function onFocusin() {
          this.domNode.classList.add("focus");
        }
      }, {
        key: "onFocusout",
        value: function onFocusout() {
          this.domNode.classList.remove("focus");
        }
      }, {
        key: "onButtonKeydown",
        value: function onButtonKeydown(event) {
          var key = event.key,
            flag = false;
          switch (key) {
            case " ":
            case "Enter":
              if (this.isOpen()) {
                this.closePopup();
                this.buttonNode.focus();
              } else {
                this.openPopup();
                this.setFocusToFirstMenuitem();
                flag = true;
                break;
              }
            case "Down":
            case "ArrowDown":
              this.openPopup();
              this.setFocusToFirstMenuitem();
              flag = true;
              break;
            case "Esc":
            case "Escape":
              this.closePopup();
              this.buttonNode.focus();
              flag = true;
              break;
            case "Up":
            case "ArrowUp":
              this.openPopup();
              this.setFocusToLastMenuitem();
              flag = true;
              break;
            case "Tab":
              // Allow tabs to pass through
              flag = false;
              break;
            default:
              break;
          }
          if (flag) {
            event.stopPropagation();
            event.preventDefault();
          }
        }
      }, {
        key: "onButtonClick",
        value: function onButtonClick(event) {
          if (this.isOpen()) {
            this.closePopup();
            this.buttonNode.focus();
          } else {
            this.openPopup();
            this.setFocusToFirstMenuitem();
          }
          event.stopPropagation();
          event.preventDefault();
        }
      }, {
        key: "onMenuitemKeydown",
        value: function onMenuitemKeydown(event) {
          var tgt = event.currentTarget,
            key = event.key,
            flag = false;
          if (event.ctrlKey || event.altKey || event.metaKey) {
            return;
          }
          switch (key) {
            case " ":
              window.location.href = tgt.href;
              break;
            case "Esc":
            case "Escape":
              this.closePopup();
              this.buttonNode.focus();
              flag = true;
              break;
            case "Up":
            case "ArrowUp":
              this.setFocusToPreviousMenuitem(tgt);
              flag = true;
              break;
            case "ArrowDown":
            case "Down":
              this.setFocusToNextMenuitem(tgt);
              flag = true;
              break;
            case "Left":
            case "Right":
            case "ArrowLeft":
            case "ArrowRight":
              if (this.isOpen()) {
                this.closePopup();
                this.buttonNode.focus();
              } else {
                // Find the next element and set it as the focus
                this.buttonNode.next().focus();
                flag = false;
                break;
              }
            case "Home":
            case "PageUp":
              this.setFocusToFirstMenuitem();
              flag = true;
              break;
            case "End":
            case "PageDown":
              this.setFocusToLastMenuitem();
              flag = true;
              break;
            case "Tab":
              var last = $(tgt).parent().is(":last-child");
              if (this.isOpen() && !last) {
                flag = false;
                break;
              } else {
                this.closePopup();
                break;
              }
            default:
              break;
          }
          if (flag) {
            event.stopPropagation();
            event.preventDefault();
          }
        }

        // On hover set focus to current menu button
      }, {
        key: "onMenuitemMouseover",
        value: function onMenuitemMouseover(event) {
          var tgt = event.currentTarget;
          tgt.focus();
        }

        // If user clicks away, close menu
      }, {
        key: "onBackgroundMousedown",
        value: function onBackgroundMousedown(event) {
          if (!this.domNode.contains(event.target)) {
            if (this.isOpen()) {
              this.closePopup();
              this.buttonNode.focus();
            }
          }
        }
      }]);
      return MenuButtonLinks;
    }(); // Initialize main menu buttons
    var menuButtons = document.querySelectorAll(".menu--main[data-depth='0'] > li");
    for (var i = 0; i < menuButtons.length; i++) {
      new MenuButtonLinks(menuButtons[i]);
    }

    // Utility Menu Links
    var utilityItems = document.querySelectorAll(".menu--utility-nav > li");

    // Search Form Inputs
    var searchInput = document.querySelectorAll(".search-form input");

    // const menuButtons = document.querySelectorAll(
    //   ".c-site-header__menu-main ul > li > button"
    // );

    // document
    //   .getElementById("nav-trigger")
    //   .addEventListener("keydown", mobileButtonKeydown);

    // Initialize main menu buttons
    var mobileNav = document.getElementById("nav-trigger");
    var mobileOpen = mobileNav.getAttribute("aria-expanded");

    // When ESC Key is pressed close ALL open menus
    $(document).on("keydown", function (event) {
      // Mobile 📱
      if (event.key === "Escape" && mobileOpen) {
        console.log("here?");
        menuButtons.each(function name() {
          $(this).attr("aria-expanded", "false");
          var buttons = menuButtons.querySelector("button");
          var menus = menuButtons.querySelector("ul.menu[data-depth='1']");
        });
        mobileNav.attr("aria-expanded", "false");
        mobileNav.removeClass("checked");
        mobileNav.focus();
        $("body").removeClass("js-prevent-scroll");

        // Desktop 🖥️
      } else if (event.key === "Escape" && !mobileOpen) {
        $($allNavListItems).each(function name() {
          $(this).removeClass("active");
          $(this).find("button").attr("aria-expanded", "false");
        });
      }
      return;
    });
    var MobileMenu = /*#__PURE__*/function () {
      function MobileMenu(domControl, domNode) {
        _classCallCheck(this, MobileMenu);
        console.log(domControl, domNode);
        this.domNode = domNode;
        this.buttonNode = domControl;

        // this.menuNode = this.domNode.querySelectorAll("a");
        this.menuitemNodes = [];
        this.firstMenuitem = false;
        this.lastMenuitem = false;
        this.buttonNode.addEventListener("keydown", this.onButtonKeydown.bind(this));
        // this.buttonNode.addEventListener(
        //   "click",
        //   this.onButtonClick.bind(this)
        // );
      }
      _createClass(MobileMenu, [{
        key: "isOpen",
        value: function isOpen() {
          return this.buttonNode.getAttribute("aria-expanded") === "true";
        }

        // Popup menu methods
      }, {
        key: "openMenu",
        value: function openMenu() {
          $("body").toggleClass("js-prevent-scroll");
          this.buttonNode.setAttribute("aria-expanded", "true");
          this.buttonNode.classList.add("checked");
        }
      }, {
        key: "closeMenu",
        value: function closeMenu() {
          if (this.isOpen()) {
            this.buttonNode.setAttribute("aria-expanded", "false");
            this.buttonNode.classList.remove("checked");
            $("body").toggleClass("js-prevent-scroll");
          }
        }
      }, {
        key: "onButtonKeydown",
        value: function onButtonKeydown(event) {
          var key = event.key,
            flag = false;
          console.log(key);
          switch (key) {
            case " ":
            case "Enter":
              if (this.isOpen()) {
                this.closeMenu();
                this.buttonNode.focus();
              } else {
                this.openMenu();
                // this.setFocusToFirstMenuitem();
                flag = true;
                break;
              }

            // case "Down":
            // case "ArrowDown":
            //   this.setFocusToFirstMenuitem();
            //   flag = true;
            //   break;

            case "Esc":
            case "Escape":
              this.closeMenu();
              this.buttonNode.focus();
              flag = true;
              break;

            // case "Up":
            // case "ArrowUp":
            //   this.setFocusToLastMenuitem();
            //   flag = true;
            //   break;

            case "Tab":
              // Allow tabs to pass through
              flag = false;
              break;
            default:
              break;
          }
          if (flag) {
            event.stopPropagation();
            event.preventDefault();
          }
        }
      }]);
      return MobileMenu;
    }();
    new MobileMenu(mobileNav, utilityItems);

    // function mobileButtonKeydown(element) {
    //   console.log(element);
    // }
    // // Mobile Navigation trigger function
    // $("#nav-trigger").on("click", (e) => {
    //   // Attach overlay to body
    //   $("body").toggleClass("js-prevent-scroll");
    //   $("#nav-trigger").toggleClass("checked");

    //   if ($("#nav-trigger").hasClass("checked")) {
    //     $("#nav-trigger").attr("aria-expanded", "true");

    //     //If mobile menu is open and utlity nav is not inside wrapper
    //     if ($(window).width() > 1024) {
    //       hideIndex(utilityItems);
    //       hideIndex(searchInput);
    //     }
    //   } else {
    //     $("#nav-trigger").attr("aria-expanded", "false");
    //     displayIndex(utilityItems);
    //     displayIndex(searchInput);
    //   }

    //   // Add negative index when mobile menu is open on small desktop
    //   function hideIndex(items) {
    //     for (let i = 0; i < items.length; i++) {
    //       var menuitem = items[i];
    //       menuitem.tabIndex = -1;
    //     }
    //   }
    //   // Remove added tabindex
    //   function displayIndex(items) {
    //     for (let i = 0; i < items.length; i++) {
    //       var menuitem = items[i];
    //       menuitem.removeAttribute("tabindex");
    //     }
    //   }
    // });
  });

  // // Copy the Secondary nav for mobile 📱
  // $('#block-submenu .menu--sub-menu[data-depth="0"]').clone().prependTo("#block-vertafore-main-menu");

  // // Prepend `Solutions for` to each top level item in the Secondary Nav just in the mobile nav
  // $( "#block-vertafore-main-menu .menu--sub-menu[data-depth='0'] > li > button.menu__link " ).each(function() {
  //   $(this).text('Solutions for ' + $( this ).text());
  // });
})(jQuery);
/******/ })()
;
//# sourceMappingURL=site-header.js.map