(function ($) {
  $(document).ready(function () {
    /*
     *   This file is based on content that is licensed according to the W3C Software License at
     *   https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
     *
     */

    "use strict";

    class MenuButtonLinks {
      constructor(domNode) {
        this.domNode = domNode;
        this.buttonNode = domNode.querySelector("button");
        this.buttonNode.setAttribute("aria-expanded", "false");
        this.menuNode = domNode.querySelector("ul.menu[data-depth='1']");
        this.menuitemNodes = [];
        this.firstMenuitem = false;
        this.lastMenuitem = false;

        this.buttonNode.addEventListener(
          "keydown",
          this.onButtonKeydown.bind(this)
        );
        this.buttonNode.addEventListener(
          "click",
          this.onButtonClick.bind(this)
        );

        var nodes = domNode.querySelectorAll(".menu__link");

        for (var i = 0; i < nodes.length; i++) {
          var menuitem = nodes[i];
          this.menuitemNodes.push(menuitem);

          menuitem.addEventListener(
            "keydown",
            this.onMenuitemKeydown.bind(this)
          );

          menuitem.addEventListener(
            "mouseover",
            this.onMenuitemMouseover.bind(this)
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
          // this.buttonNode.removeAttribute("aria-expanded");
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
      onMenuitemMouseover(event) {
        var tgt = event.currentTarget;
        tgt.focus();
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

    // Initialize main menu buttons
    var menuButtons = document.querySelectorAll(
      ".menu--main[data-depth='0'] > li"
    );
    for (let i = 0; i < menuButtons.length; i++) {
      new MenuButtonLinks(menuButtons[i]);
    }

    // Mobile Navigation trigger function
    $("#nav-trigger").on("click", (e) => {
      // Attach overlay to body
      $("body").toggleClass("js-prevent-scroll");
      $("#nav-trigger").toggleClass("checked");

      if ($("#nav-trigger").hasClass("checked")) {
        $("#nav-trigger").attr("aria-expanded", "true");
        if ($(window).width() > 1024) {
          var utilityItems = document.querySelectorAll(
            ".menu--utility-nav > li > a"
          );

          var searchInput = document.querySelectorAll(".search-form input");
          hideIndex(utilityItems);

          hideIndex(searchInput);
        }
      } else {
        $("#nav-trigger").attr("aria-expanded", "false");

        var utilityItems = document.querySelectorAll(
          ".menu--utility-nav > li > a"
        );
        var searchInput = document.querySelectorAll(".search-form input");

        displayIndex(utilityItems);
        displayIndex(searchInput);
      }

      function hideIndex(items) {
        for (let i = 0; i < items.length; i++) {
          var menuitem = items[i];
          menuitem.tabIndex = -1;
        }
      }
      function displayIndex(items) {
        for (let i = 0; i < items.length; i++) {
          var menuitem = items[i];
          menuitem.removeAttribute("tabindex");
        }
      }
    });
  });
})(jQuery);
