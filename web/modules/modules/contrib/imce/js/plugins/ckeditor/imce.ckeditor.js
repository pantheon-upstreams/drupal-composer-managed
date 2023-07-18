(function ($, Drupal, CKEDITOR) {
  'use strict';

  /**
   * @file
   * Defines Imce plugin for CKEditor.
   */
  if (!CKEDITOR.imce) {
    CKEDITOR.plugins.add('imce', {
      // Define commands and buttons
      init: function (editor, context) {
        // Image
        editor.addCommand('imceimage', {
          exec: CKEDITOR.imce.imageDialog
        });
        editor.ui.addButton('ImceImage', {
          label: CKEDITOR.imce.imageLabel(),
          command: 'imceimage',
          icon: editor.config.ImceImageIcon
        });
        // Link
        editor.addCommand('imcelink', {
          exec: CKEDITOR.imce.linkDialog
        });
        editor.ui.addButton('ImceLink', {
          label: CKEDITOR.imce.linkLabel(),
          command: 'imcelink',
          icon: editor.config.ImceLinkIcon
        });
      }
    });
  }

  /**
   * Global container for helper methods.
   */
  CKEDITOR.imce = CKEDITOR.imce || {

    /**
     * Returns image button label.
     */
    imageLabel: function () {
      return Drupal.t('Insert images using Imce File Manager');
    },

    /**
     * Opens Imce for inserting images into CKEditor.
     */
    imageDialog: function (editor) {
      return CKEDITOR.imce.openDialog(editor, 'image');
    },

    /**
     * Returns link button label.
     */
    linkLabel: function () {
      return Drupal.t('Insert file links using Imce File Manager');
    },

    /**
     * Opens Imce for inserting links into CKEditor.
     */
    linkDialog: function (editor) {
      return CKEDITOR.imce.openDialog(editor, 'link');
    },

    /**
     * Opens Imce for inserting images or links into CKEditor.
     */
    openDialog: function (editor, type) {
      var width = Math.min(1000, parseInt(screen.availWidth * 0.8));
      var height = Math.min(800, parseInt(screen.availHeight * 0.8));
      var url = CKEDITOR.imce.url('sendto=CKEDITOR.imce.sendto&type=' + type + '&ck_id=' + encodeURIComponent(editor.name));
      editor.popup(url, width, height);
    },

    /**
     * Imce sendto handler for image/link dialog.
     */
    sendto: function (File, win) {
      var imce = win.imce;
      var editor = CKEDITOR.instances[imce.getQuery('ck_id')];
      if (!editor) {
        win.close();
        return;
      }
      var selection = imce.getSelection();
      var type = imce.getQuery('type');
      var is_img = type === 'image';
      var process = function() {
        var inner = is_img ? '' : CKEDITOR.imce.getSelectedHtml(editor);
        var html = imce.itemsHtml(selection, type, inner);
        editor.insertHtml(html);
        win.close();
      };
      // Process after loading the uuids.
      if (is_img) {
        imce.loadItemUuids(selection, process);
      }
      else {
        process();
      }
    },

    /**
     * Returns Imce url.
     */
    url: function (query) {
      var url = Drupal.url('imce');
      if (query) {
        url += (url.indexOf('?') === -1 ? '?' : '&') + query;
      }
      return url;
    },

    /**
     * Returns the selection source from the editor.
     */
    getSelectedHtml: function (editor) {
      var html = '';
      try {
        var range = editor.getSelection().getRanges()[0];
        var div = editor.document.createElement('div');
        div.append(range.cloneContents());
        html = div.getHtml();
      } catch (err) { }
      return html;
    }

  };

})(jQuery, Drupal, CKEDITOR);
