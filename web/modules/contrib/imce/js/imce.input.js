(function ($, Drupal) {
  "use strict";

  /**
   * @file
   * Provides methods for integrating Imce into text fields.
   */

  /**
   * Drupal behavior to handle url input integration.
   */
  Drupal.behaviors.imceUrlInput = {
    attach: function (context, settings) {
      $('.imce-url-input', context).not('.imce-url-processed').addClass('imce-url-processed').each(imceInput.processUrlInput);
    }
  };

  /**
   * Global container for integration helpers.
   */
  var imceInput = window.imceInput = window.imceInput || {

    /**
     * Processes an url input.
     */
    processUrlInput: function(i, el) {
      var button = imceInput.createUrlButton(el.id, el.getAttribute('data-imce-type'));
      el.parentNode.insertBefore(button, el);
    },

    /**
     * Creates an url input button.
     */
    createUrlButton: function(inputId, inputType) {
      var button = document.createElement('a');
      button.href = '#';
      button.className = 'imce-url-button';
      button.title = Drupal.t('Open File Browser');
      button.innerHTML = '<span>' + button.title + '</span>';
      button.onclick = imceInput.urlButtonClick;
      button.setAttribute('data-input-id', inputId || 'imce-url-input-' + (Math.random() + '').substring(2));
      button.setAttribute('data-input-type', inputType || 'link');
      return button;
    },

    /**
     * Click event of an url button.
     */
    urlButtonClick: function(e) {
      const inputId = this.getAttribute('data-input-id');
      const type = this.getAttribute('data-input-type');
      $('#' + inputId).trigger('focus');
      imceInput.openImce('imceInput.urlSendto', type, 'inputId=' + inputId);
      return false;
    },

    /**
     * Opens an Imce window with a global sendto callback.
     */
    openImce: function (sendto, type, params) {
      var url = imceInput.url(
        'sendto=' +
          sendto +
          '&type=' +
          type +
          (params ? '&' + params : ''),
      );
      return imceInput.openWindow(url);
    },

    /**
     * Returns imce url.
     */
    url: function (params) {
      var url = Drupal.url('imce');
      if (params) {
        url += (url.indexOf('?') === -1 ? '?' : '&') + params;
      }
      return url;
    },

    /**
     * Opens a new window with an url.
     */
    openWindow: function (url, win) {
      var width = Math.min(1000, parseInt(screen.availWidth * 0.8));
      var height = Math.min(800, parseInt(screen.availHeight * 0.8));
      return (win || window).open(
        url,
        '',
        'width=' + width + ',height=' + height + ',resizable=1',
      );
    },

    /**
     * Sendto handler for an url input.
     */
    urlSendto: function(File, win) {
      var url = File.getUrl();
      var el = $('#' + win.imce.getQuery('inputId'))[0];
      win.close();
      if (el) {
        $(el).val(url).trigger('change').trigger('focus');
      }
    }

  };

})(jQuery, Drupal);
