(function ($) {
  $(document).ready(function () {
    /*
     *   This file is based on content that is licensed according to the W3C Software License at
     *   https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
     *
     */

    "use strict";

    // Base MenuLinks Class
    class MenuLinks {
      constructor(domNode) {
        this.domNode = domNode;
        this.menuitemNodes = [];
        this.firstMenuitem = false;
        this.lastMenuitem = false;

        var nodes = domNode.querySelectorAll(".menu__link");

        for (var i = 0; i < nodes.length; i++) {
          var menuitem = nodes[i];

          this.menuitemNodes.push(menuitem);
          menuitem.tabIndex = -1;

          menuitem.addEventListener(
            "keydown",
            this.onMenuitemKeydown.bind(this)
          );

          if (!this.firstMenuitem) {
            this.firstMenuitem = menuitem;
          }
          this.lastMenuitem = menuitem;
        }
      }

      setFocusToMenuitem(newMenuitem) {
        this.menuitemNodes.forEach(function (item) {
          if (item === newMenuitem) {
            item.tabIndex = 0;
            newMenuitem.focus();
          } else {
            item.tabIndex = -1;
          }
        });
      }

      setFocusToFirstMenuitem() {
        this.setFocusToMenuitem(this.firstMenuitem);
      }

      setFocusToLastMenuitem() {
        this.setFocusToMenuitem(this.lastMenuitem);
      }

      setFocusToPreviousMenuitem(currentMenuitem) {
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

      setFocusToNextMenuitem(currentMenuitem) {
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

      onMenuitemKeydown(event) {
        var tgt = event.currentTarget,
          key = event.key;

        if (event.ctrlKey || event.altKey || event.metaKey) {
          return;
        }

        switch (key) {
          case "Left":
          case "ArrowLeft":
            // Find the prevous element and set it as the focus
            var prev = $(tgt).parent().prev();
            if ($(prev).find(".menu__link").length > 0) {
              //Move to previous element
              $(prev).find(".menu__link").focus();
            } else {
              var prev = $(
                "ul.menu--main[data-depth='0'] > .menu__item > .menu__link"
              );
              // Navigate to last list element
              prev[prev.length - 1].focus();
            }
            break;

          case "Right":
          case "ArrowRight":
            // Find the next element and set it as the focus
            var next = $(tgt).parent().next();
            if ($(next).find(".menu__link").length > 0) {
              //Move to next element
              $(next).find(".menu__link").focus();
            } else {
              var next = $(
                "ul.menu--main[data-depth='0'] > .menu__item > .menu__link"
              );
              // Wrap to first element in list
              next[0].focus();
            }
            break;

          default:
            break;
        }
      }
    }

    class MenuButtons extends MenuLinks {
      constructor(domNode) {
        //  Call parent constructor
        super(domNode);

        // Button specific properties
        this.buttonNode = domNode.querySelector("button");
        this.buttonNode.setAttribute("aria-expanded", "false");
        this.menuNode = domNode.querySelector("ul.menu[data-depth='1']");

        // Attach button specific event listeners
        this.buttonNode.addEventListener(
          "keydown",
          this.onButtonKeydown.bind(this)
        );
        this.buttonNode.addEventListener(
          "click",
          this.onButtonClick.bind(this)
        );

        this.menuitemNodes = [];
        this.firstMenuitem = false;
        this.lastMenuitem = false;

        var nodes = domNode.querySelectorAll(".menu__link");

        for (var i = 0; i < nodes.length; i++) {
          var menuitem = nodes[i];
          this.menuitemNodes.push(menuitem);

          menuitem.tabIndex = -1;

          menuitem.addEventListener(
            "keydown",
            this.onMenuitemKeydown.bind(this)
          );

          if (!this.firstMenuitem) {
            this.firstMenuitem = menuitem;
          }
          this.lastMenuitem = menuitem;
        }

        domNode.addEventListener("focusin", this.onFocusin.bind(this));
        domNode.addEventListener("focusout", this.onFocusout.bind(this));

        window.addEventListener(
          "mousedown",
          this.onBackgroundMousedown.bind(this),
          true
        );
      }

      setFocusToMenuitem(newMenuitem) {
        this.menuitemNodes.forEach(function (item) {
          if (item === newMenuitem) {
            item.tabIndex = 0;
            newMenuitem.focus();
          } else {
            item.tabIndex = -1;
          }
        });
      }

      setFocusToFirstMenuitem() {
        this.setFocusToMenuitem(this.firstMenuitem);
      }

      setFocusToLastMenuitem() {
        this.setFocusToMenuitem(this.lastMenuitem);
      }

      setFocusToPreviousMenuitem(currentMenuitem) {
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

      setFocusToNextMenuitem(currentMenuitem) {
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
      openPopup() {
        this.menuNode.classList.add("open");
        this.buttonNode.setAttribute("aria-expanded", "true");
      }

      closePopup() {
        if (this.isOpen()) {
          this.buttonNode.setAttribute("aria-expanded", "false");
          this.menuNode.classList.remove("open");
        }
      }

      isOpen() {
        return this.buttonNode.getAttribute("aria-expanded") === "true";
      }

      // Menu event handlers
      onFocusin() {
        this.domNode.classList.add("focus");
      }

      onFocusout() {
        this.domNode.classList.remove("focus");
      }

      onButtonKeydown(event) {
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

          case "Left":
          case "ArrowLeft":
            // Find prevous element and set it as the focus
            var prev = $(this).parent().prev();
            if ($(prev).find(".menu__link").length > 0) {
              //Move to previous element
              $(prev).find(".menu__link").focus();
            } else {
              // We need to account for hidden menu items that could be in the list
              var prev = $(
                "ul.menu--main[data-depth='0'] > .menu__item:not(:hidden) > .menu__link"
              );
              // Navigate to last list element
              prev[prev.length - 1].focus();
            }
            break;

          case "Right":
          case "ArrowRight":
            // Find next element and set it as the focus
            var next = $(this).parent().next();

            if ($(next).find(".menu__link").length > 0) {
              //Move to next element
              $(next).find(".menu__link").focus();
            } else {
              var next = $(
                "ul.menu--main[data-depth='0'] > .menu__item > .menu__link"
              );
              // Wrap to first element in list
              next[0].focus();
            }
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

      onButtonClick(event) {
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

      onMenuitemKeydown(event) {
        var tgt = event.currentTarget,
          key = event.key,
          flag = false;

        if (event.ctrlKey || event.altKey || event.metaKey) {
          return;
        }

        switch (key) {
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
          case "ArrowLeft":
            // Close menu if open
            if (this.isOpen()) {
              this.closePopup();
            }

            // Find the top level button prevous element and set it as the focus
            var prev = $(tgt)
              .closest(".menu__item.menu__item--expanded")
              .prev();
            if ($(prev).find(".menu__link").length > 0) {
              //Move to previous element
              $(prev).find(".menu__link").focus();
            } else {
              var prev = $(
                "ul.menu--main[data-depth='0'] > .menu__item > .menu__link"
              );
              // Navigate to last list element
              prev[prev.length - 1].focus();
            }
            break;

          case "Right":
          case "ArrowRight":
            // Close menu if open
            if (this.isOpen()) {
              this.closePopup();
            }
            // Find the top level button next element and set it as the focus
            var next = $(tgt)
              .closest(".menu__item.menu__item--expanded")
              .next();

            if ($(next).find(".menu__link").length > 0) {
              //Move to next element
              $(next).find(".menu__link").focus();
            } else {
              var next = $(
                "ul.menu--main[data-depth='0'] > .menu__item > .menu__link"
              );
              // Wrap to first element in list
              next[0].focus();
            }
            break;

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

      // If user clicks away, close menu
      onBackgroundMousedown(event) {
        if (!this.domNode.contains(event.target)) {
          if (this.isOpen()) {
            this.closePopup();
            this.buttonNode.focus();
          }
        }
      }
    }

    // Copy the Utility nav for mobile 📱
    $('#block-utilitynav .menu--utility-nav[data-depth="0"] li')
      .clone()
      .appendTo('#block-surf-main-main-menu > ul.menu--main[data-depth="0"]');

    // Initialize main menu list
    var menuButtons = document.querySelectorAll(
      ".menu--main[data-depth='0'] > li"
    );
    for (let i = 0; i < menuButtons.length; i++) {
      if (menuButtons[i].querySelector("button")) {
        new MenuButtons(menuButtons[i]);
      } else {
        new MenuLinks(menuButtons[i]);
      }
    }

    // TODO: Refactor for cleaner code

    // Utility Menu Links
    const utilityItems = $(".menu--utility-nav .menu__link");

    // Search Form Inputs
    const searchInput = $(".search-form input");

    // Initialize main menu buttons
    const mobileNavButton = $("#nav-trigger");
    const mobileNav = $(".menu--main .menu__link");
    const mobileOpen = mobileNavButton.attr("aria-expanded");

    // When ESC Key is pressed close ALL open menus
    $(document).on("keydown", function (event) {
      // Mobile 📱
      if (event.key === "Escape" && mobileOpen) {
        mobileNavButton.attr("aria-expanded", "false");
        mobileNavButton.removeClass("checked");
        mobileNavButton.focus();
        $("body").removeClass("js-prevent-scroll");
      }
      return;
    });

    // Store window width
    var windowSize = $(window).width();

    // Mobile Navigation trigger function
    $("#nav-trigger").on("click", (e) => {
      // Attach overlay to body
      $("body").toggleClass("js-prevent-scroll");
      $("#nav-trigger").toggleClass("checked");

      if ($("#nav-trigger").hasClass("checked")) {
        $("#nav-trigger").attr("aria-expanded", "true");

        //If mobile menu is open and utlity nav is not inside wrapper
        if (windowSize >= 1024) {
          hideIndex(utilityItems);
          hideIndex(searchInput);
        } else if (windowSize <= 1023) {
          // When menu open allow keyboard focus
          displayIndex(mobileNav);
          displayIndex(searchInput);
        }
      } else {
        $("#nav-trigger").attr("aria-expanded", "false");
        if (windowSize >= 1024) {
          displayIndex(utilityItems);
          displayIndex(searchInput);
        } else if (windowSize <= 1023) {
          // When menu is closed, hide from focus
          hideIndex(mobileNav);
          hideIndex(searchInput);
        }
      }
    });

    // Add negative index when mobile menu is open on small desktop
    function hideIndex(items) {
      for (let i = 0; i < items.length; i++) {
        var menuitem = items[i];
        menuitem.tabIndex = -1;
      }
    }
    // Remove added tabindex
    function displayIndex(items) {
      for (let i = 0; i < items.length; i++) {
        var menuitem = items[i];
        menuitem.removeAttribute("tabindex");
      }
    }

    // Hide from keyboard by default on load for smaller screens
    if (windowSize <= 1023 && $("#nav-trigger").attr("aria-expanded")) {
      // When menu is closed, hide from focus
      hideIndex(mobileNav);
      hideIndex(searchInput);
    }

    // TODO: Need a button listener for top level menu, if user hits escape and no other submenu items are open, close mobile menu

    // class MobileMenu {
    //   constructor(domControl, domNode) {
    //     console.log(domControl, domNode);
    //     this.domNode = domNode;
    //     this.buttonNode = domControl;

    //     this.buttonNode.addEventListener(
    //       "keydown",
    //       this.onButtonKeydown.bind(this)
    //     );
    //     // this.buttonNode.addEventListener(
    //     //   "click",
    //     //   this.onButtonClick.bind(this)
    //     // );
    //   }

    //   isOpen() {
    //     return this.buttonNode.getAttribute("aria-expanded") === "true";
    //   }

    //   // Popup menu methods
    //   openMenu() {
    //     $("body").toggleClass("js-prevent-scroll");
    //     this.buttonNode.setAttribute("aria-expanded", "true");
    //     this.buttonNode.classList.add("checked");
    //   }

    //   closeMenu() {
    //     if (this.isOpen()) {
    //       this.buttonNode.setAttribute("aria-expanded", "false");
    //       this.buttonNode.classList.remove("checked");
    //       $("body").toggleClass("js-prevent-scroll");
    //     }
    //   }
    //   onButtonKeydown(event) {
    //     var key = event.key,
    //       flag = false;
    //     console.log(key);

    //     switch (key) {
    //       case " ":
    //       case "Enter":
    //         if (this.isOpen()) {
    //           this.closeMenu();
    //           this.buttonNode.focus();
    //         } else {
    //           this.openMenu();
    //           // this.setFocusToFirstMenuitem();
    //           flag = true;
    //           break;
    //         }

    //       case "Esc":
    //       case "Escape":
    //         this.closeMenu();
    //         this.buttonNode.focus();
    //         flag = true;
    //         break;

    //       case "Tab":
    //         // Allow tabs to pass through
    //         flag = false;
    //         break;

    //       default:
    //         break;
    //     }

    //     if (flag) {
    //       event.stopPropagation();
    //       event.preventDefault();
    //     }
    //   }
    // }

    // new MobileMenu(mobileNav, utilityItems);
  });
})(jQuery);
