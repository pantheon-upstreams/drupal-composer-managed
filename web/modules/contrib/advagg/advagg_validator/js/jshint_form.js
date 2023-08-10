/**
 * @file
 * Run JSHINT in the browser against the servers JS.
 */

/* global jQuery:false */
/* global Drupal:false */
/* global JSHINT:false */

/**
 * Have clicks to advagg_validator_js classes run JSHINT clientside.
 */
(function ($) {
  'use strict';
  Drupal.behaviors.advagg_validator_js_simple = {
    attach: function (context, settings) {
      $('.advagg_validator_js', context).click(function (context) {
        // Get Results Div.
        var results = $(this).siblings('.results');
        // Clear out the results.
        $(results).html('');
        // Loop over each filename.
        $.each($(this).siblings('.filenames'), function () {
          var filename = $(this).val();
          if (filename) {
            try {
              var t = new Date().getTime();
              var x = jQuery.ajax({
                url: settings.path.baseUrl + filename + '?t=' + t,
                dataType: 'text',
                async: false
              });
              if (!JSHINT(x.responseText, drupalSettings.jshint, drupalSettings.jshint.predef)) {
                $(results).append('<p><h4>' + filename + '</h4><ul>');
                for (var i = 0; i < JSHINT.errors.length; i++) {
                  var ignore = (drupalSettings.jshint && drupalSettings.jshint.ignore) ? drupalSettings.jshint.ignore.split(',') : [];
                  if (ignore.indexOf(JSHINT.errors[i].code) === -1) {
                    var w = JSHINT.errors[i].reason + ' (line ' + JSHINT.errors[i].line + ', col ' + JSHINT.errors[i].character + ', rule ' + JSHINT.errors[i].code + ')';
                    $(results).append('<li class="' + JSHINT.errors[i].id.replace(/[()]/g, '') + '">' + w.replace(/ /g, '&nbsp;') + '</li>');
                  }
                }
                $(results).append('</ul></p>');
              }
            }
            catch (err) {
              $(results).append(err);
            }
          }
        });

        return false;
      });
    }
  };
}(jQuery));

/**
 * Have clicks to advagg_validator_recursive_js classes run JSHINT clientside.
 */
(function ($) {
  'use strict';
  Drupal.behaviors.advagg_validator_js_recursive = {
    attach: function (context, settings) {
      $('.advagg_validator_recursive_js', context).click(function (context) {
        // Get Results Div.
        var results = $(this).siblings('.results');
        // Clear out the results.
        $(results).html('');
        // Loop over each filename.
        $.each($(this).parent().find('.filenames'), function () {
          var filename = $(this).val();
          if (filename) {
            try {
              var t = new Date().getTime();
              var x = jQuery.ajax({
                url: settings.path.baseUrl + filename + '?t=' + t,
                dataType: 'text',
                async: false
              });
              if (!JSHINT(x.responseText, drupalSettings.jshint, drupalSettings.jshint.predef)) {
                $(results).append('<p><h4>' + filename + '</h4><ul>');
                for (var i = 0; i < JSHINT.errors.length; i++) {
                  var ignore = (drupalSettings.jshint && drupalSettings.jshint.ignore) ? drupalSettings.jshint.ignore.split(',') : [];
                  if (ignore.indexOf(JSHINT.errors[i].code) === -1) {
                    var w = JSHINT.errors[i].reason + ' (line ' + JSHINT.errors[i].line + ', col ' + JSHINT.errors[i].character + ', rule ' + JSHINT.errors[i].code + ')';
                    $(results).append('<li class="' + JSHINT.errors[i].id.replace(/[()]/g, '') + '">' + w.replace(/ /g, '&nbsp;') + '</li>');
                  }
                }
                $(results).append('</ul></p>');
              }
            }
            catch (err) {
              $(results).append(err);
            }
          }
        });

        return false;
      });
    }
  };
}(jQuery));
