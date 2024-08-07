CONTENTS OF THIS FILE
---------------------
   
 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Drush

INTRODUCTION
------------

Minify Source HTML was developed to replace the implementation of the Minify
module (https://www.drupal.org/project/minify) which would only minify the html
in the content area of the page, not the html of the entire page. This module
hooks in at the very end of the page render process and minifies everything.

REQUIREMENTS
------------

None.

INSTALLATION
------------

Install as you would normally install a contributed Drupal module. Visit:
https://www.drupal.org/documentation/install/modules-themes/modules-8 for
further information.

CONFIGURATION
-------------

* Configure user permissions in Administration » People » Permissions:

  - Administer Minify HTML Module

    This permission controls who can enable/disable the minification of the
    source HTML and who can modify the settings of the module.

* Customize the module settings in Administration » Configuration » Development
    » Performance » Minify Source HTML settings.

* Enable the minification of the source HTML in Administration » Configuration
    » Development » Performance.  See the Bandwidth optimization section.

DRUSH
-----

* Enable the minification of html:

  drush cset minifyhtml.config minify true

* Disable the minification of html:

  drush cset minifyhtml.config minify false

* Enable the strip comments option:

  drush cset minifyhtml.config strip_comments true

* Disable the strip comments option:

  drush cset minifyhtml.config strip_comments false
