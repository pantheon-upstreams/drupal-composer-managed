/*global imce:true*/
(function ($, Drupal, imce) {
  'use strict';

  /**
   * @file
   * Defines imce Toolbar Button object.
   */

  /**
   * Toolbar button constructor.
   */
  imce.Tbb = function (id, opt) {
    this.construct(id, opt);
  };

  /**
   * Toolbar button prototype.
   */
  var Tbb = imce.Tbb.prototype;

  /**
   * Constructs button object.
   */
  Tbb.construct = function (id, opt) {
    var Tbb = imce.toolbarButtons[id] = this;
    Tbb.id = id;
    $.extend(Tbb, opt);
    // Append or prepend the element.
    var el = Tbb.createEl();
    var parent = imce.toolbarEl;
    if (Tbb.prepend && parent.firstChild) {
      parent.insertBefore(el, parent.firstChild);
    }
    else {
      parent.appendChild(el);
    }
    // Add shortcut
    if (Tbb.shortcut) {
      imce.addShortcut(Tbb.shortcut, el);
    }
  };

  /**
   * Creates toolbar button element.
   */
  Tbb.createEl = function () {
    var Tbb = this;
    var el = Tbb.el;
    var icon;
    if (!el) {
      el = Tbb.el = imce.createEl('<span class="imce-tbb imce-ficon" role="button"><span class="imce-tbb-title"></span></span>');
      if (icon = Tbb.icon) {
        el.className += ' imce-ficon-' + icon;
      }
      el.className += ' imce-tbb--' + Tbb.id;
      el.title = (Tbb.tooltip || Tbb.title) + (Tbb.shortcut ? ' (' + Tbb.shortcut + ')' : '');
      el.onclick = imce.eTbbClick;
      el.Tbb = Tbb;
      el.firstChild.innerHTML = Tbb.title;
    }
    return el;
  };

  /**
   * Create item popup.
   */
  Tbb.createPopupEl = function () {
    var Tbb = this;
    var el = Tbb.popupEl;
    if (!el) {
      el = Tbb.popupEl = imce.createLayer('imce-tbb-popup');
      el.className += ' imce-tbb-popup--' + Tbb.id;
      el.onkeydown = imce.eTbbPopupKeydown;
      el.Tbb = Tbb;
      if (Tbb.content) {
        el.appendChild(Tbb.content);
      }
    }
    return el;
  };

  /**
   * Open item popup.
   */
  Tbb.openPopup = function (autoclose) {
    var Tbb = this;
    if (!Tbb.active) {
      Tbb.createPopupEl();
      Tbb.setActive(true);
      var popupEl = Tbb.popupEl;
      var $el = $(Tbb.el);
      var css = $el.offset();
      css.top += $el.outerHeight(true);
      $(popupEl).css(css).fadeIn();
      imce.fixPosition(popupEl);
      // Focus on first input
      $('form').find('input,select,textarea').filter(':visible').eq(0).trigger('focus');
      if (autoclose) {
        $(document).on('mousedown', {Tbb: Tbb}, imce.eTbbDocMousedown);
      }
      if (Tbb.onopen) {
        Tbb.onopen.apply(Tbb, arguments);
      }
    }
  };

  /**
   * Close item popup.
   */
  Tbb.closePopup = function () {
    var Tbb = this;
    if (Tbb.popupEl && Tbb.active) {
      Tbb.setActive(false);
      $(Tbb.popupEl).hide();
      imce.contentEl.focus();
      if (Tbb.onclose) {
        Tbb.onclose.apply(Tbb, arguments);
      }
    }
  };

  /**
   * Set active state of the item.
   */
  Tbb.setActive = function (state) {
    this.toggleState('active', !!state);
  };

  /**
   * Set busy state of the item.
   */
  Tbb.setBusy = function (state) {
    this.toggleState('busy', !!state);
  };

  /**
   * Set disabled state of the item.
   */
  Tbb.setDisabled = function (state) {
    this.toggleState('disabled', !!state);
  };

  /**
   * Set/unset a state by name.
   */
  Tbb.toggleState = function (name, state) {
    var Tbb = this;
    var oldState = Tbb[name];
    if (state == null) {
      state = !oldState;
    }
    if (state) {
      if (!oldState) {
        Tbb[name] = true;
        $(Tbb.el).addClass(name);
      }
    }
    else if (oldState) {
      Tbb[name] = false;
      $(Tbb.el).removeClass(name);
    }
  };

  /**
   * Trigger click handler of the button.
   */
  Tbb.click = function (event) {
    var Tbb = this;
    if (!Tbb.disabled) {
      if (Tbb.handler && !Tbb.busy) {
        Tbb.handler.call(Tbb, imce.eFix(event));
      }
      if (Tbb.content) {
        Tbb.openPopup(true);
      }
    }
  };


  /**
   * Click event for toolbar buttons.
   */
  imce.eTbbClick = function (event) {
    this.Tbb.click(event);
  };

  /**
   * Mousedown event for document in order to close toolbar button popup.
   */
  imce.eTbbDocMousedown = function (e) {
    var Tbb = e.data.Tbb;
    var el = Tbb.popupEl;
    if (el !== e.target && !$.contains(el, e.target)) {
      Tbb.closePopup();
      $(document).off('mousedown', imce.eTbbDocMousedown);
    }
  };

  /**
   * Keydown event for toolbar button popup.
   */
  imce.eTbbPopupKeydown = function (event) {
    var e = event || window.event;
    // Close on Esc
    if (e.keyCode === 27) {
      this.Tbb.closePopup();
      return false;
    }
  };

})(jQuery, Drupal, imce);
