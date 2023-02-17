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
      for (let i = 0; i < items.length; i++) {
        let menuitem = items[i];
        menuitem.tabIndex = -1;
      }
    }
    // Remove added tabindex
    function displayIndex(items) {
      for (let i = 0; i < items.length; i++) {
        let menuitem = items[i];
        menuitem.removeAttribute("tabindex");
      }
    }

    // Display or hide coppied utility menu items
    function toggleUtility(items, display) {
      for (let i = 0; i < items.length; i++) {
        let menulink = $(items[i]).find(".menu__link");
        if (display) {
          $(menulink).attr("data-visible", true);
        } else {
          $(menulink).attr("data-visible", false);
        }
      }
    }

    // Close menu
    function closeMobile(key) {
      // Get window width
      let windowSize = $(window).width();

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
      // Store window width
      let windowSize = $(window).width();

      // Open Menu
      if ($(mobileNavButton).attr("aria-expanded") === "false") {
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
      let delayReload;
      let resizedWindow = $(window).width();
      clearTimeout(delayReload);
      delayReload = setTimeout(windowAdjustMenus(resizedWindow), 200);
    });

    // If a user clicks outside the menu, close menu
    // Stop events from continuning to information behind overlay
    $(window).on("click", function (e) {
      // Is the menu actually open?
      if ($("body").hasClass("js-prevent-scroll")) {
        // Get window width
        let windowSize = $(window).width();
        let mainMenu;

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
        if (
          !mainMenu[0].contains(e.target) &&
          mainMenu.parent()[0] !== e.target
        ) {
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
        }

        // Stop event from traveling through to other components on the screen
        e.stopPropagation();
        e.preventDefault();
      }
    });

    /*------------------------------------*\
      - 02 - MenuLink Class (Base)
      This is the base class for attaching functions to mobile menu links
    \*------------------------------------*/
    class MenuLinks {
      constructor(domNode) {
        this.domNode = domNode;
        this.menuitemNodes = [];
        this.firstMenuitem = false;
        this.lastMenuitem = false;

        let nodes = domNode.querySelectorAll(".menu__link");

        for (let i = 0; i < nodes.length; i++) {
          let menuitem = nodes[i];

          this.menuitemNodes.push(menuitem);

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
            newMenuitem.focus();
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
        let newMenuitem, index;

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
        let newMenuitem, index;

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
        let target = event.currentTarget,
          key = event.key;

        if (event.ctrlKey || event.altKey || event.metaKey) {
          return;
        }

        switch (key) {
          case "Left":
          case "ArrowLeft":
            // Find the prevous element and set it as the focus
            let prev = $(target).parent().prev();
            if ($(prev).find(".menu__link").length > 0) {
              //Move to previous element
              $(prev).find(".menu__link").focus();
            } else {
              let prev = $(
                "ul.menu--main[data-depth='0'] > .menu__item > .menu__link"
              );
              // Navigate to last list element
              prev[prev.length - 1].focus();
            }
            break;

          case "Right":
          case "ArrowRight":
            // Find the next element and set it as the focus
            let next = $(target).parent().next();
            if ($(next).find(".menu__link").length > 0) {
              //Move to next element
              $(next).find(".menu__link").focus();
            } else {
              let next = $(
                "ul.menu--main[data-depth='0'] > .menu__item > .menu__link"
              );
              // Wrap to first element in list
              next[0].focus();
            }
            break;

          case "Tab":
            // Did the user shift + tab?
            if (event.shiftKey) {
              let first = $(target).parent().is(":first-child");
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
    }

    /*------------------------------------*\
      - 03 - MenuButton Class (Extends MenuLink)
      This is the base class for attaching functions to mobile menu links
    \*------------------------------------*/
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

        let nodes = domNode.querySelectorAll("a.menu__link");

        for (let i = 0; i < nodes.length; i++) {
          let menuitem = nodes[i];
          this.menuitemNodes.push(menuitem);

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

      // Popup menu methods
      openPopup() {
        this.menuNode.classList.add("open");
        $(this.menuNode).slideDown();
        // Attach open to parent menu for styles
        const parentMenu = $(this.menuNode).first().parent()[0];
        parentMenu.classList.add("expanded");
        this.buttonNode.setAttribute("aria-expanded", "true");
      }

      closePopup() {
        if (this.isOpen()) {
          this.buttonNode.setAttribute("aria-expanded", "false");
          this.menuNode.classList.remove("open");
          $(this.menuNode).slideUp();
          // Attach open to parent menu for styles
          const parentMenu = $(this.menuNode).first().parent()[0];
          parentMenu.classList.remove("expanded");
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
        let key = event.key,
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
              super.setFocusToFirstMenuitem();
              flag = true;
              break;
            }

          case "Down":
          case "ArrowDown":
            this.openPopup();
            super.setFocusToFirstMenuitem();
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
            super.setFocusToLastMenuitem();
            flag = true;
            break;

          case "Left":
          case "ArrowLeft":
            // Find prevous element and set it as the focus
            let prev = $(this.buttonNode).parent().prev();
            if ($(prev).find(".menu__link").length > 0) {
              //Move to previous element
              $(prev).find(".menu__link").focus();
            } else {
              // We need to account for hidden menu items that could be in the list
              let prev = $(
                "ul.menu--main[data-depth='0'] > .menu__item:not(:hidden) > .menu__link"
              );
              // Navigate to last list element
              prev[prev.length - 1].focus();
            }
            break;

          case "Right":
          case "ArrowRight":
            // Find next element and set it as the focus
            let next = $(this.buttonNode).parent().next();
            if (
              $(next).find(".menu__link:not([data-visible=false])").length > 0
            ) {
              //Move to next element
              $(next).find(".menu__link").focus();
            } else {
              let next = $(
                "ul.menu--main[data-depth='0'] > .menu__item > .menu__link"
              );
              // Wrap to first element in list
              next[0].focus();
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

      onButtonClick(event) {
        if (this.isOpen()) {
          this.closePopup();
          this.buttonNode.focus();
        } else {
          this.openPopup();
        }

        event.stopPropagation();
        event.preventDefault();
      }

      onMenuitemKeydown(event) {
        let target = event.currentTarget,
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
            super.setFocusToPreviousMenuitem(target);
            flag = true;
            break;

          case "ArrowDown":
          case "Down":
            super.setFocusToNextMenuitem(target);
            flag = true;
            break;

          case "Left":
          case "ArrowLeft":
            // Close menu if open
            if (this.isOpen()) {
              this.closePopup();
            }

            // Find the top level button prevous element and set it as the focus
            let prev = $(target)
              .closest(".menu__item.menu__item--expanded")
              .prev();
            if (
              $(prev).find(".menu__link:not([data-visible=false])").length > 0
            ) {
              //Move to previous element
              $(prev).find(".menu__link:not([data-visible=false])").focus();
            } else {
              let prev = $(
                "ul.menu--main[data-depth='0'] > .menu__item > .menu__link:not([data-visible=false])"
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
            let next = $(target)
              .closest(".menu__item.menu__item--expanded")
              .next();

            if (
              $(next).find(".menu__link:not([data-visible=false])").length > 0
            ) {
              //Move to next element
              $(next).find(".menu__link:not([data-visible=false])").focus();
            } else {
              let next = $(
                "ul.menu--main[data-depth='0'] > .menu__item > .menu__link:not([data-visible=false])"
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
            super.setFocusToLastMenuitem();
            flag = true;
            break;

          case "Tab":
            // Did the user shift + tab?
            if (event.shiftKey) {
              let first = $(target).parent().is(":first-child");
              if (this.isOpen() && !first) {
                // Allow event to pass
              } else {
                this.closePopup();
              }
              break;
            } else {
              // Check if this is the last item in a list
              let last = $(target).parent().is(":last-child");

              // Find last visible element within top level parent menu
              let lastVisible = $(
                ".menu--main[data-depth='0'] > .menu__item > .menu__link:not([data-visible=false]):last"
              );
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
      onBackgroundMousedown(event) {
        if (!this.domNode.contains(event.target)) {
          if (this.isOpen()) {
            this.closePopup();
            this.buttonNode.focus();
          }
        }
      }
    }

    /*------------------------------------*\
      - 04 - Constants
      UtilityItems relates to the utility navigation above the main menu.
      SearchInput is the global search bar inside the utility section.

      ClonedMenu specifically relates to when part of the utlity menu is copied into the
      main menu due to design strucutre and limitations this was the simplest solution.
    \*------------------------------------*/

    // Copy the Utility nav for mobile, needs to run prior to menu intialize
    $('#block-utilitynav .menu--utility-nav[data-depth="0"] li')
      .clone()
      .appendTo('#block-surf-main-main-menu > ul.menu--main[data-depth="0"]');

    // Main Menu Items
    const mainMenuItems = document.querySelectorAll(
      ".menu--main[data-depth='0'] > li"
    );

    // Initialize cloned menu list
    const clonedMenu = document.querySelectorAll(".menu--main .utility__link");

    // Utility Menu Links
    const utilityItems = $(".menu--utility-nav .menu__link");

    // Search Form Inputs
    const searchInput = $(".search-form input");

    // Initialize mobile menu button & nav
    const mobileNavButton = $("#nav-trigger");
    const mobileNav = $(".menu--main .menu__link");

    /*------------------------------------*\
      - 05 - Initalize Menus
      What items should these functions be attached to
      @todo Rework menu section for better load times & clarity
    \*------------------------------------*/

    function initalizeMenus() {
      let windowSize = $(window).width();

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
      for (let i = 0; i < mainMenuItems.length; i++) {
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
