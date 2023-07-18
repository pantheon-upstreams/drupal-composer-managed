/*global BUE:true*/
(function ($, Drupal, BUE) {
  'use strict';

  /**
   * @file
   * Defines Imce plugin for BUEditor.
   */

  /**
   * File browser handler for image/link dialogs.
   */
  BUE.fileBrowsers.imce = function (field, type, E) {
    var width = Math.min(1000, parseInt(screen.availWidth * 0.8));
    var height = Math.min(800, parseInt(screen.availHeight * 0.8));
    var field_id = BUE.imce.fields.length;
    var url = BUE.imce.url('sendto=BUE.imce.sendtoField&type=' + type + '&field_id=' + field_id);
    BUE.imce.fields[field_id] = field;
    window.open(url, '', 'width=' + width + ',height=' + height + ',resizable=1');
  };

  /**
   * Global container for helper methods.
   */
  BUE.imce = BUE.imce || {

    /**
     * Active form fields currently using the file browser.
     */
    fields: [],

    /**
     * Imce sendto handler for inserting a file url into a form field.
     */
    sendtoField: function (File, win) {
      var field;
      var id = win.imce.getQuery('field_id');
      if (field = BUE.imce.fields[id]) {
        // Set field value
        field.value = File.getUrl();
        // Check other fields
        var name;
        var input;
        var value;
        var values = {width: File.width, height: File.height, alt: File.formatName()};
        for (name in values) {
          if (value = values[name]) {
            if (input = field.form.elements[name]) {
              input.value = value;
            }
          }
        }
        field.focus();
        BUE.imce.fields[id] = null;
      }
      win.close();
    },

    /**
     * Returns imce url.
     */
    url: function (query) {
      var url = Drupal.url('imce');
      if (query) {
        url += (url.indexOf('?') === -1 ? '?' : '&') + query;
      }
      return url;
    }

  };

})(jQuery, Drupal, BUE);
