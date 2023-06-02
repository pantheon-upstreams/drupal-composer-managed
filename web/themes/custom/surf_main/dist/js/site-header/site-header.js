/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
function _get() { if (typeof Reflect !== "undefined" && Reflect.get) { _get = Reflect.get.bind(); } else { _get = function _get(target, property, receiver) { var base = _superPropBase(target, property); if (!base) return; var desc = Object.getOwnPropertyDescriptor(base, property); if (desc.get) { return desc.get.call(arguments.length < 3 ? target : receiver); } return desc.value; }; } return _get.apply(this, arguments); }
function _superPropBase(object, property) { while (!Object.prototype.hasOwnProperty.call(object, property)) { object = _getPrototypeOf(object); if (object === null) break; } return object; }
function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); Object.defineProperty(subClass, "prototype", { writable: false }); if (superClass) _setPrototypeOf(subClass, superClass); }
function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }
function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } else if (call !== void 0) { throw new TypeError("Derived constructors may only return object or undefined"); } return _assertThisInitialized(self); }
function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }
function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
(function ($) {
  $(document).ready(function () {
    "use strict";

    /**
     * Accessible Menu
     * This file is based on content that is licensed according to the W3C Software License at
     * https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
     *
     * - 01 - Utility Functions
     * - 02 - MenuLink Class (Base)
     * - 03 - MenuButton Class (Extends MenuLink)
     * - 04 - Constants
     * - 05 - Initalize Menus
     */

    /*------------------------------------*\
      - 01 - Utility Functions
      hideIndex sets a negative index so that keyboards do not access links when hidden
      displayIndex removes the negative index so keyboards can access links
      toggleUtility controls when to display the coppied utlity links within the main nav
    \*------------------------------------*/

    // Add negative index when mobile menu is open on small desktop
    function hideIndex(items) {
      for (var i = 0; i < items.length; i++) {
        var menuitem = items[i];
        menuitem.tabIndex = -1;
      }
    }
    // Remove added tabindex
    function displayIndex(items) {
      for (var i = 0; i < items.length; i++) {
        var menuitem = items[i];
        menuitem.removeAttribute("tabindex");
      }
    }

    // Display or hide coppied utility menu items
    function toggleUtility(items, display) {
      for (var i = 0; i < items.length; i++) {
        var menulink = $(items[i]).find(".menu__link");
        if (display) {
          $(menulink).attr("data-visible", true);
        } else {
          $(menulink).attr("data-visible", false);
        }
      }
    }

    // Determine which menu container we should being working with
    function menuContainer() {
      // Get window width
      var windowSize = $(window).width();
      var mainMenu;

      // Due to the strucuture of the split menu, we need to set different targets
      if (windowSize >= 1024 && windowSize < 1200) {
        mainMenu = $("#block-surf-main-main-menu");
      } else if (windowSize <= 1023) {
        mainMenu = $("#mainMenuControl");
      } else {
        // We are on desktop, default to large menu
        mainMenu = $("#block-surf-main-main-menu");
      }
      return mainMenu, windowSize;
    }

    // Close menu
    function closeMobile(key) {
      var mainMenu,
        windowSize = menuContainer();

      // Close Menu
      $("body").removeClass("js-prevent-scroll");
      $(mobileNavButton).removeClass("checked");
      $(mobileNavButton).attr("aria-expanded", "false");
      if (windowSize >= 1024 && windowSize < 1200) {
        displayIndex(utilityItems);
        displayIndex(searchInput);
        hideIndex($(mainMenuItems).find(".menu__link"));
      } else if (windowSize <= 1023) {
        // When menu is closed, hide from focus
        hideIndex(utilityItems);
        hideIndex(mobileNav);
        hideIndex(searchInput);
      }

      // It was escape key, set focus
      if (key == "Esc" || key == "Escape") {
        $(mobileNavButton).focus();
      }
    }

    // Mobile Navigation trigger function
    // This function mainly handles opening the menu & controlling menu access
    // The close function has been broken out into a seperate function
    function mobileMenuNavigationControl(event) {
      // Open Menu
      if ($(mobileNavButton).attr("aria-expanded") === "false") {
        // Store window width
        var windowSize = $(window).width();

        // Toggle overlay & checked classes
        $("body").addClass("js-prevent-scroll");
        $(mobileNavButton).addClass("checked");
        // Open menu
        $(mobileNavButton).attr("aria-expanded", "true");

        // What menus should be visible based on screen size
        if (windowSize >= 1200) {
          displayIndex($(mainMenuItems).find(".menu__link"));
          // Hide cloned menu on largest screens
          toggleUtility(clonedMenu, false);
        } else if (windowSize >= 1024 && windowSize < 1200) {
          //If mobile menu is open and utlity nav is not inside wrapper
          hideIndex(utilityItems);
          hideIndex(searchInput);
          displayIndex($(mainMenuItems).find(".menu__link"));
        } else {
          // Utility menu is inside mobile menu
          hideIndex(utilityItems);
          displayIndex(mobileNav);
          displayIndex(searchInput);
          toggleUtility(clonedMenu, true);
        }
      } else {
        closeMobile();
      }
      event.stopPropagation();
      event.preventDefault();
    }

    // If user shrinks screen, run mobile setup
    // This determines which menus need to be avaliable
    function windowAdjustMenus(windowSize) {
      // What menus should be visible based on screen size
      if (windowSize >= 1200) {
        displayIndex($(mainMenuItems).find(".menu__link"));
        // Hide cloned menu on largest screens
        toggleUtility(clonedMenu, false);
      } else if (windowSize >= 1024 && windowSize < 1200) {
        // Hide from keyboard by default on load for smaller screens
        displayIndex(utilityItems);
        displayIndex(searchInput);
        hideIndex($(mainMenuItems).find(".menu__link"));
      } else if (windowSize <= 1023) {
        // When menu is closed, hide from focus
        hideIndex(utilityItems);
        hideIndex(mobileNav);
        hideIndex(searchInput);
        toggleUtility(clonedMenu, false);
      }
    }

    // Attach delayed listener for screen resize to adjust menu strucutres
    window.addEventListener("resize", function () {
      var delayReload;
      var resizedWindow = $(window).width();
      clearTimeout(delayReload);
      delayReload = setTimeout(windowAdjustMenus(resizedWindow), 200);
    });

    // If a user clicks outside the menu, close menu
    // Stop events from continuning to information behind overlay
    $(window).on("click", function (e) {
      // Is the menu actually open?
      if ($("body").hasClass("js-prevent-scroll")) {
        // Get window width
        var windowSize = $(window).width();
        var mainMenu;

        // Due to the strucuture of the split menu, we need to set different targets
        if (windowSize >= 1024 && windowSize < 1200) {
          mainMenu = $("#block-surf-main-main-menu");
        } else if (windowSize <= 1023) {
          mainMenu = $("#mainMenuControl");
        } else {
          // We are on desktop, default to large menu
          mainMenu = $("#block-surf-main-main-menu");
        }

        // Is this the main menu?
        if (!mainMenu[0].contains(e.target) && mainMenu.parent()[0] !== e.target) {
          // Close Menu
          $("body").removeClass("js-prevent-scroll");
          $(mobileNavButton).removeClass("checked");
          $(mobileNavButton).attr("aria-expanded", "false");
          if (windowSize >= 1024 && windowSize < 1200) {
            displayIndex(utilityItems);
            displayIndex(searchInput);
            hideIndex($(mainMenuItems).find(".menu__link"));
          } else if (windowSize <= 1023) {
            // When menu is closed, hide from focus
            hideIndex(utilityItems);
            hideIndex(mobileNav);
            hideIndex(searchInput);
          }

          // Stop event from traveling through to other components on the screen
          e.stopPropagation();
          e.preventDefault();
        }
      }
    });

    /*------------------------------------*\
      - 02 - MenuLink Class (Base)
      This is the base class for attaching functions to mobile menu links
    \*------------------------------------*/
    var MenuLinks = /*#__PURE__*/function () {
      function MenuLinks(domNode) {
        _classCallCheck(this, MenuLinks);
        this.domNode = domNode;
        this.menuitemNodes = [];
        this.firstMenuitem = false;
        this.lastMenuitem = false;
        var nodes = domNode.querySelectorAll(".menu__link");
        for (var i = 0; i < nodes.length; i++) {
          var menuitem = nodes[i];
          this.menuitemNodes.push(menuitem);
          menuitem.addEventListener("keydown", this.onMenuitemKeydown.bind(this));
          if (!this.firstMenuitem) {
            this.firstMenuitem = menuitem;
          }
          this.lastMenuitem = menuitem;
        }
      }
      _createClass(MenuLinks, [{
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
      }, {
        key: "onMenuitemKeydown",
        value: function onMenuitemKeydown(event) {
          var target = event.currentTarget,
            key = event.key;
          if (event.ctrlKey || event.altKey || event.metaKey) {
            return;
          }
          switch (key) {
            case "Left":
            case "ArrowLeft":
              // Find the prevous element and set it as the focus
              var prev = $(target).parent().prev();
              if ($(prev).find(".menu__link").length > 0) {
                //Move to previous element
                $(prev).find(".menu__link").focus();
              } else {
                var _prev = $("ul.menu--main[data-depth='0'] > .menu__item > .menu__link");
                // Navigate to last list element
                _prev[_prev.length - 1].focus();
              }
              break;
            case "Right":
            case "ArrowRight":
              // Find the next element and set it as the focus
              var next = $(target).parent().next();
              if ($(next).find(".menu__link").length > 0) {
                //Move to next element
                $(next).find(".menu__link").focus();
              } else {
                var _next = $("ul.menu--main[data-depth='0'] > .menu__item > .menu__link");
                // Wrap to first element in list
                _next[0].focus();
              }
              break;
            case "Tab":
              // Did the user shift + tab?
              if (event.shiftKey) {
                var first = $(target).parent().is(":first-child");
                if (!first) {
                  // Allow event to pass
                } else {
                  this.closePopup();
                }
                break;
              } else {
                // Check if this is the last item in a list
                if ($(target).parent().is(":last-child")) {
                  // Close menu
                  closeMobile();
                }
              }
              break;
            case "Esc":
            case "Escape":
              // Close mobile menu
              closeMobile("Esc");
              break;
            default:
              break;
          }
        }
      }]);
      return MenuLinks;
    }();
    /*------------------------------------*\
      - 03 - MenuButton Class (Extends MenuLink)
      This is the base class for attaching functions to mobile menu links
    \*------------------------------------*/
    var MenuButtons = /*#__PURE__*/function (_MenuLinks) {
      _inherits(MenuButtons, _MenuLinks);
      var _super = _createSuper(MenuButtons);
      function MenuButtons(domNode) {
        var _this;
        _classCallCheck(this, MenuButtons);
        //  Call parent constructor
        _this = _super.call(this, domNode);

        // Button specific properties
        _this.buttonNode = domNode.querySelector("button");
        _this.buttonNode.setAttribute("aria-expanded", "false");
        _this.menuNode = domNode.querySelector("ul.menu[data-depth='1']");

        // Attach button specific event listeners
        _this.buttonNode.addEventListener("keydown", _this.onButtonKeydown.bind(_assertThisInitialized(_this)));
        _this.buttonNode.addEventListener("click", _this.onButtonClick.bind(_assertThisInitialized(_this)));
        _this.menuitemNodes = [];
        _this.firstMenuitem = false;
        _this.lastMenuitem = false;
        var nodes = domNode.querySelectorAll("a.menu__link");
        for (var i = 0; i < nodes.length; i++) {
          var menuitem = nodes[i];
          _this.menuitemNodes.push(menuitem);
          menuitem.addEventListener("keydown", _this.onMenuitemKeydown.bind(_assertThisInitialized(_this)));
          if (!_this.firstMenuitem) {
            _this.firstMenuitem = menuitem;
          }
          _this.lastMenuitem = menuitem;
        }
        domNode.addEventListener("focusin", _this.onFocusin.bind(_assertThisInitialized(_this)));
        domNode.addEventListener("focusout", _this.onFocusout.bind(_assertThisInitialized(_this)));
        window.addEventListener("mousedown", _this.onBackgroundMousedown.bind(_assertThisInitialized(_this)), true);
        return _this;
      }

      // Popup menu methods
      _createClass(MenuButtons, [{
        key: "openPopup",
        value: function openPopup() {
          this.menuNode.classList.add("open");
          $(this.menuNode).slideDown();
          // Attach open to parent menu for styles
          var parentMenu = $(this.menuNode).first().parent()[0];
          parentMenu.classList.add("expanded");
          this.buttonNode.setAttribute("aria-expanded", "true");
        }
      }, {
        key: "closePopup",
        value: function closePopup() {
          if (this.isOpen()) {
            this.buttonNode.setAttribute("aria-expanded", "false");
            this.menuNode.classList.remove("open");
            $(this.menuNode).slideUp();
            // Attach open to parent menu for styles
            var parentMenu = $(this.menuNode).first().parent()[0];
            parentMenu.classList.remove("expanded");
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
                flag = true;
                break;
              } else {
                this.openPopup();
                // super.setFocusToFirstMenuitem();
                flag = true;
                break;
              }
            case "Down":
            case "ArrowDown":
              this.openPopup();
              // super.setFocusToFirstMenuitem();
              flag = true;
              break;
            case "Esc":
            case "Escape":
              // If a button is open,close it
              if (this.isOpen()) {
                this.closePopup();
                this.buttonNode.focus();
                flag = true;
              }
              // Close mobile menu;
              closeMobile("Esc");
              break;
            case "Up":
            case "ArrowUp":
              this.openPopup();
              _get(_getPrototypeOf(MenuButtons.prototype), "setFocusToLastMenuitem", this).call(this);
              flag = true;
              break;
            case "Left":
            case "ArrowLeft":
              // Find prevous element and set it as the focus
              var prev = $(this.buttonNode).parent().prev();
              if ($(prev).find(".menu__link").length > 0) {
                //Move to previous element
                $(prev).find(".menu__link").focus();
              } else {
                // We need to account for hidden menu items that could be in the list
                var _prev2 = $("ul.menu--main[data-depth='0'] > .menu__item:not(:hidden) > .menu__link");
                // Navigate to last list element
                _prev2[_prev2.length - 1].focus();
              }
              break;
            case "Right":
            case "ArrowRight":
              // Find next element and set it as the focus
              var next = $(this.buttonNode).parent().next();
              if ($(next).find(".menu__link:not([data-visible=false])").length > 0) {
                //Move to next element
                $(next).find(".menu__link").focus();
              } else {
                var _next2 = $("ul.menu--main[data-depth='0'] > .menu__item > .menu__link");
                // Wrap to first element in list
                _next2[0].focus();
              }
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
          }
          event.stopPropagation();
          event.preventDefault();
        }
      }, {
        key: "onMenuitemKeydown",
        value: function onMenuitemKeydown(event) {
          var target = event.currentTarget,
            key = event.key,
            flag = false;
          if (event.ctrlKey || event.altKey || event.metaKey) {
            return;
          }
          switch (key) {
            case "Esc":
            case "Escape":
              if (this.isOpen) {
                this.closePopup();
                this.buttonNode.focus();
                flag = true;
              } else {
                closeMobile("Esc");
              }
              break;
            case "Up":
            case "ArrowUp":
              _get(_getPrototypeOf(MenuButtons.prototype), "setFocusToPreviousMenuitem", this).call(this, target);
              flag = true;
              break;
            case "ArrowDown":
            case "Down":
              _get(_getPrototypeOf(MenuButtons.prototype), "setFocusToNextMenuitem", this).call(this, target);
              flag = true;
              break;
            case "Left":
            case "ArrowLeft":
              // Close menu if open
              if (this.isOpen()) {
                this.closePopup();
              }

              // Find the top level button prevous element and set it as the focus
              var prev = $(target).closest(".menu__item.menu__item--expanded").prev();
              if ($(prev).find(".menu__link:not([data-visible=false])").length > 0) {
                //Move to previous element
                $(prev).find(".menu__link:not([data-visible=false])").focus();
              } else {
                var _prev3 = $("ul.menu--main[data-depth='0'] > .menu__item > .menu__link:not([data-visible=false])");
                // Navigate to last list element
                _prev3[_prev3.length - 1].focus();
              }
              break;
            case "Right":
            case "ArrowRight":
              // Close menu if open
              if (this.isOpen()) {
                this.closePopup();
              }
              // Find the top level button next element and set it as the focus
              var next = $(target).closest(".menu__item.menu__item--expanded").next();
              if ($(next).find(".menu__link:not([data-visible=false])").length > 0) {
                //Move to next element
                $(next).find(".menu__link:not([data-visible=false])").focus();
              } else {
                var _next3 = $("ul.menu--main[data-depth='0'] > .menu__item > .menu__link:not([data-visible=false])");
                // Wrap to first element in list
                _next3[0].focus();
              }
              break;
            case "Home":
            case "PageUp":
              this.setFocusToFirstMenuitem();
              flag = true;
              break;
            case "End":
            case "PageDown":
              _get(_getPrototypeOf(MenuButtons.prototype), "setFocusToLastMenuitem", this).call(this);
              flag = true;
              break;
            case "Tab":
              // Did the user shift + tab?
              if (event.shiftKey) {
                var first = $(target).parent().is(":first-child");
                if (this.isOpen() && !first) {
                  // Allow event to pass
                } else {
                  this.closePopup();
                }
                break;
              } else {
                // Check if this is the last item in a list
                var last = $(target).parent().is(":last-child");

                // Find last visible element within top level parent menu
                var lastVisible = $(".menu--main[data-depth='0'] > .menu__item > .menu__link:not([data-visible=false]):last");
                // If the event is open, or is not the last in the list allow event to pass
                if (this.isOpen() && !last) {
                  // If this is the last visible component at the top level menu, close entire menu
                } else if (target == lastVisible[0]) {
                  closeMobile();
                } else {
                  this.closePopup();
                }
              }
              break;
            default:
              break;
          }
          if (flag) {
            event.stopPropagation();
            event.preventDefault();
          }
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
      return MenuButtons;
    }(MenuLinks);
    /*------------------------------------*\
      - 04 - Constants
      UtilityItems relates to the utility navigation above the main menu.
      SearchInput is the global search bar inside the utility section.
       ClonedMenu specifically relates to when part of the utlity menu is copied into the
      main menu due to design strucutre and limitations this was the simplest solution.
    \*------------------------------------*/
    // Copy the Utility nav for mobile, needs to run prior to menu intialize
    $('#block-utilitynav .menu--utility-nav[data-depth="0"] li').clone().appendTo('#block-surf-main-main-menu > ul.menu--main[data-depth="0"]');

    // Main Menu Items
    var mainMenuItems = document.querySelectorAll(".menu--main[data-depth='0'] > li");

    // Initialize cloned menu list
    var clonedMenu = document.querySelectorAll(".menu--main .utility__link");

    // Utility Menu Links
    var utilityItems = $(".menu--utility-nav .menu__link");

    // Search Form Inputs
    var searchInput = $(".header-search-form input");

    // Initialize mobile menu button & nav
    var mobileNavButton = $("#nav-trigger");
    var mobileNav = $(".menu--main .menu__link");

    /*------------------------------------*\
      - 05 - Initalize Menus
      What items should these functions be attached to
      @todo Rework menu section for better load times & clarity
    \*------------------------------------*/

    function initalizeMenus() {
      var windowSize = $(window).width();
      if (windowSize >= 1200) {
        // Hide cloned menu on largest screens
        toggleUtility(clonedMenu, false);
      } else if (windowSize >= 1024 && windowSize < 1200) {
        hideIndex($(mainMenuItems).find(".menu__link"));
        toggleUtility(clonedMenu, false);
      } else {
        // Configure indexs for mobile menu
        hideIndex(utilityItems);
        hideIndex(mobileNav);
        hideIndex(searchInput);
      }

      // Initialize main menu list
      for (var i = 0; i < mainMenuItems.length; i++) {
        if (mainMenuItems[i].querySelector("button")) {
          new MenuButtons(mainMenuItems[i]);
          // Initialize submenus as hidden
          $(mainMenuItems[i]).find("ul").attr("style", "display:none");
        } else {
          new MenuLinks(mainMenuItems[i]);
        }
      }
    }

    // Attach mobile menu button listener controls
    mobileNavButton.on("click", mobileMenuNavigationControl);

    // Configure menus on load
    initalizeMenus();
  });
})(jQuery);
/******/ })()
;
//# sourceMappingURL=site-header.js.map