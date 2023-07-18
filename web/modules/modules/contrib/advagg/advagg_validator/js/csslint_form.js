/**
 * @file
 * Run CSSLint in the browser against the servers CSS.
 */

/* global jQuery:false */
/* global Drupal:false */
/* global CSSLint:false */

/**
 * Have clicks to advagg_validator_css classes run CSSLint clientside.
 */
(function ($) {
  'use strict';
  Drupal.behaviors.advagg_validator_css_simple = {
    attach: function (context, settings) {
      $('.advagg_validator_css', context).click(function (context) {
        // Get Results Div.
        var results = $(this).siblings('.results');
        // Clear out the results.
        $(results).html('');
        // Loop over each filename.
        $.each($(this).siblings('.filenames'), function () {
          var filename = $(this).val();
          if (filename) {
            advagg_validator_css($, results, filename, settings);
          }
        });

        return false;
      });
    }
  };
}(jQuery));

/**
 * Have clicks to advagg_validator_recursive_css classes run CSSLint clientside.
 */
(function ($) {
  'use strict';
  Drupal.behaviors.advagg_validator_css_recursive = {
    attach: function (context, settings) {
      $('.advagg_validator_recursive_css', context).click(function (context) {
        // Get Results Div.
        var results = $(this).siblings('.results');
        // Clear out the results.
        $(results).html('');
        // Loop over each filename.
        $.each($(this).parent().find('.filenames'), function () {
          var filename = $(this).val();
          if (filename) {
            advagg_validator_css($, results, filename, settings);
          }
        });
        return false;
      });
    }
  };
}(jQuery));

/**
 * Given the filename, run contents through CSSLint.
 *
 * @param object $
 *   jQuery object.
 * @param object results
 *   jQuery object from the results class in the dom.
 * @param string filename
 *   Name of the file, also includes the relative path.
 * @param object settings
 *   The drupal settings object.
 */
function advagg_validator_css($, results, filename, settings) {
  'use strict';
  try {
    // Use the current time to bust the browser cache.
    var t = new Date().getTime();
    jQuery.ajax({
      url: settings.path.baseUrl + filename + '?t=' + t,
      dataType: 'text',
      async: false,
      success: function (data) {
        // File was downloaded; run through CSSLint.
        var y = CSSLint.verify(data, settings.csslint.rules);
        var z = y.messages;
        $(results).append('<p><h4>' + filename + '</h4><ul>');
        if (!z.length) {
          $(results).append('<li>No errors</li>');
        }
        for (var i = 0, len = z.length; i < len; i++) {
          // Output lint errors.
          var w = z[i].message + ' (line ' + z[i].line + ', col ' + z[i].col + ', rule ' + z[i].rule.id + ')';
          $(results).append('<li class="' + z[i].type + '">' + w.replace(/ /g, '&nbsp;') + '</li>');
        }
        $(results).append('</ul></p>');
      },
      error: function (data, textStatus, errorThrown) {
        // File could not be downloaded; display error.
        $(results).append('<p><h4>' + filename + '</h4><ul>');
        $(results).append('<li class="error">' + Drupal.t('Browser unable to read file. @error', {'@error': errorThrown}) + '</li>');
        $(results).append('</ul></p>');
      }
    });
  }
  catch (err) {
    $(results).append(err);
  }
}
