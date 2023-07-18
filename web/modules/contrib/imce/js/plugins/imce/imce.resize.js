/*global imce:true*/
(function ($, Drupal, imce) {
  'use strict';

  /**
   * @file
   * Defines Resize plugin for Imce.
   */

  /**
   * Init handler for Resize.
   */
  imce.bind('init', imce.resizeInit = function () {
    if (imce.hasPermission('resize_images')) {
      // Add toolbar button
      imce.addTbb('resize', {
        title: Drupal.t('Resize'),
        permission: 'resize_images',
        content: imce.createResizeForm(),
        shortcut: 'Ctrl+Alt+R',
        icon: 'image'
      });
    }
  });

  /**
   * Creates resize form.
   */
  imce.createResizeForm = function () {
    var form = imce.resizeForm;
    if (!form) {
      form = imce.resizeForm = imce.createEl('<form class="imce-resize-form">' +
        '<div class="imce-form-item imce-resize-dimensions">' +
          '<input type="number" name="width" class="imce-resize-width-input" min="1" step="1" />' +
          '<span class="imce-resize-separator">x</span>' +
          '<input type="number" name="height" class="imce-resize-height-input" min="1" step="1" />' +
        '</div>' +
        '<div class="imce-form-item imce-resize-copy">' +
          '<label><input type="checkbox" name="copy" class="imce-resize-copy-input" />' + Drupal.t('Create a copy') + '</label>' +
        '</div>' +
        '<div class="imce-form-actions">' +
          '<button type="submit" name="op" class="imce-resize-button">' + Drupal.t('Resize') + '</button>' +
        '</div>' +
      '</form>');
      form.onsubmit = imce.eResizeSubmit;
      // Set max values
      var els = form.elements;
      els.width.max = imce.getConf('maxwidth') || 10000;
      els.height.max = imce.getConf('maxheight') || 10000;
      // Set placeholders
      els.width.placeholder = Drupal.t('Width');
      els.height.placeholder = Drupal.t('Height');
      // Set focus event
      els.width.onfocus = els.height.onfocus = imce.eResizeInputFocus;
    }
    return form;
  };

  /**
   * Submit event for resize form.
   */
  imce.eResizeSubmit = function () {
    var data;
    var els = this.elements;
    var width = parseInt(els.width.value * 1);
    var height = parseInt(els.height.value * 1);
    var copy = els.copy.checked ? 1 : 0;
    var items = imce.getSelection();
    if (imce.validateResize(items, width, height, copy)) {
      data = {width: width, height: height, copy: copy};
      imce.ajaxItems('resize', items, {data: data});
      imce.getTbb('resize').closePopup();
    }
    return false;
  };

  /**
   * Validates item resizing.
   */
  imce.validateResize = function (items, width, height, copy) {
    return imce.activeFolder.isReady() && imce.validateCount(items) && imce.validateImageTypes(items) && imce.validateDimensions(items, width, height) && imce.validatePermissions(items, 'resize_images');
  };

  /**
   * Focus event for resize width/height input.
   */
  imce.eResizeInputFocus = function () {
    var el = this;
    var val = el.value;
    // Apply aspect ratio of the selected image after min delay.
    setTimeout(function () {
      if (el === document.activeElement && val === el.value) {
        var ratio;
        var els = el.form.elements;
        var isWidth = els.width === el;
        var value = els[isWidth ? 'height' : 'width'].value * 1;
        var Item = imce.previewingItem;
        if (Item && Item.width && value) {
          ratio = Item.width / Item.height;
          el.value = Math.round(isWidth ? value * ratio : value / ratio);
        }
        el = null;
      }
    });
  };

})(jQuery, Drupal, imce);
