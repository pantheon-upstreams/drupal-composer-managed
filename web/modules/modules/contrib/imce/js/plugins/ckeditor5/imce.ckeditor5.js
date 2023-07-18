(function (Drupal, CKEditor5) {
  /**
   * @file
   * Defines Imce plugins for CKEditor5.
   */

  /**
   * Defines imce.ImceSelector plugin.
   *
   * Integrates Imce button into image/link url fields.
   */
  class ImceSelector extends CKEditor5.core.Plugin {
    init() {
      this.editor.ui.on('ready', function () {
        const plugins = this.editor.plugins;
        // Image.
        if (plugins.has('ImageInsertUI')) {
          const view = plugins.get('ImageInsertUI').dropdownView;
          if (view) {
            view.once('change:isOpen', function () {
              const el =
                view.element.getElementsByClassName('ck-input-text')[0];
              imceInput.processCKEditor5Input(el, 'image');
            });
          }
        }
        // Link.
        if (plugins.has('LinkUI')) {
          const view = plugins.get('LinkUI').formView;
          if (view) {
            const el = view.urlInputView.fieldView.element;
            imceInput.processCKEditor5Input(el, 'link');
          }
        }
      });
    }
  }

  /**
   * Defines imce.ImceImage plugin.
   *
   * Provides a button that inserts multiple images from Imce.
   */
  class ImceImage extends CKEditor5.core.Plugin {
    init() {
      const label = Drupal.t('Insert images using Imce File Manager');
      imceInput.ckeditor5PluginInit(this.editor, 'image', label);
    }
  }

  /**
   * Defines imce.ImceLink plugin.
   *
   * Provides a button that inserts multiple file links from Imce.
   */
  class ImceLink extends CKEditor5.core.Plugin {
    init() {
      const label = Drupal.t('Insert file links using Imce File Manager');
      imceInput.ckeditor5PluginInit(this.editor, 'link', label);
    }
  }

  /**
   * Add imce namespace.
   */
  CKEditor5.imce = CKEditor5.imce || {
    ImceSelector,
    ImceImage,
    ImceLink,
  };

  /**
   * Extend window.imceInput.
   */
  const imceInput = window.imceInput || {};

  /**
   * Init ckeditor5 image/link plugin.
   */
  imceInput.ckeditor5PluginInit = function (editor, type, label) {
    editor.ui.componentFactory.add('imce_' + type, function () {
      const button = new CKEditor5.ui.ButtonView();
      button.set({
        label,
        class: 'ck-imce-button ck-imce-' + type + '-button',
        tooltip: true,
      });
      button.on('execute', function () {
        const id = editor.sourceElement.getAttribute('data-ckeditor5-id');
        return imceInput.openImce('imceInput.sendtoCKEditor5', type, 'ckid=' + id);
      });
      return button;
    });
  };

  /**
   * Integrates Imce into a CKEditor5 url input.
   */
  imceInput.processCKEditor5Input = function (el, type) {
    if (!el) {
      return;
    }
    // Create a custom sento handler.
    const name = 'sendtoCKEditor5' + (Math.random() + '').substring(2);
    imceInput[name] = function (File, win) {
      el.value = File.getUrl();
      win.close();
      el.focus();
      el.dispatchEvent(new CustomEvent('input'));
      // Auto submit.
      if (el.form) {
        const button = el.form.getElementsByClassName('ck-button-save')[0];
        if (button) {
          button.click();
        }
      }
    };
    const button = imceInput.createUrlButton(el.id, type);
    button.className += ' imce-selector-button';
    button.onclick = function () {
      imceInput.openImce('imceInput.' + name, type);
      return false;
    };
    el.insertAdjacentElement('afterend', button);
    return button;
  };

  /**
   * Imce sendto handler for inserting files/images into CKEditor5.
   */
  imceInput.sendtoCKEditor5 = function (File, win) {
    const imce = win.imce;
    const editor = Drupal.CKEditor5Instances.get(imce.getQuery('ckid'));
    if (!editor) {
      win.close();
      return;
    }
    const type = imce.getQuery('type');
    const isImg = type === 'image';
    const selected = imce.getSelection();
    const finish = function () {
      const inner = isImg ? '' : imceInput.ckeditor5GetSelection(editor);
      const html = imce.itemsHtml(selected, type, inner);
      imceInput.ckeditor5SetSelection(editor, html);
      win.close();
    };
    if (isImg) {
      imce.loadItemUuids(selected, finish);
    }
    else {
      finish();
    }
  };

  /**
   * Returns selected html from CKEditor5.
   */
  imceInput.ckeditor5GetSelection = function (editor) {
    let html = '';
    try {
      const model = editor.model;
      const content = model.getSelectedContent(model.document.selection);
      html = editor.data.stringify(content);
    } catch (err) {
      console.error(err);
    }
    return html;
  };

  /**
   * Inserts html into CKEditor5 selection.
   */
  imceInput.ckeditor5SetSelection = function (editor, html, skipFocus) {
    try {
      const viewFragment = editor.data.processor.toView(html);
      const modelFragment = editor.data.toModel(viewFragment);
      editor.model.insertContent(modelFragment);
      if (!skipFocus) {
        editor.editing.view.focus();
      }
    } catch (err) {
      console.error(err);
    }
  };
})(Drupal, CKEditor5);
