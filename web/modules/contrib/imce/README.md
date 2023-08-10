# IMCE FILE MANAGER

Imce is an ajax based file manager that supports personal folders.

For a full description of the module, visit the
[project page](https://www.drupal.org/project/imce).

Submit bug reports and feature suggestions, or track changes in the
[issue queue](https://www.drupal.org/project/issues/imce).


## CONTENTS OF THIS FILE

- Requirements
- Recommended modules
- Installation
- Menu integration
- CKeditor integration
- BUeditor integration
- File/Image field integration
- Maintainers


## Requirements

This module requires no modules outside of Drupal core.


## Recommended modules

-[BUEditor](https://www.drupal.org/project/bueditor)


## Installation

Install the IMCE module as you would normally install a contributed
Drupal module. Visit https://www.drupal.org/node/1897420 for further
information.


## Menu integration

1. Create a custom menu item with /imce path


## CKeditor integration

1. Go to Administration > Configuration >
  Content Authoring > Text formats and editors >
  and edit a text format that uses CKEditor.
2. Enable CKEditor image button without image uploads.

Image uploads must be disabled in order for IMCE link appear in the image
dialog. There is also an image button provided by Imce but it can't be used for
editing existing images.


## BUeditor integration

1. Edit your editor at /admin/config/content/bueditor.
2. Select Imce File Manager as the File browser under Settings.


## File/Image field integration

1. Go to form settings of your content type.
   Ex: /admin/structure/types/manage/article/form-display
2. Edit widget settings of a file/image field.
3. Check the box saying "Allow users to select files from Imce File Manager
   for this field." Save.
4. You should now see the "Open File Browser" link above the upload widget
   in the content form.


## Tests

Before of run tests you needs create a shortcut for
core/phpunit.xml.dist in your root project.


### Executing UnitTest

```
vendor/bin/phpunit modules/imce
```


## Executing KernelTest with Lando

lando php core/scripts/run-tests.sh --php /usr/local/bin/php
--url http://example.lndo.site --dburl mysql://drupal8:drupal8@database/drupal8
--sqlite simpletest.sqlite --module imce --verbose --color


## Maintainers

- ufku - [ufku](https://www.drupal.org/u/ufku)
- Thalles Ferreira - [thalles](https://www.drupal.org/u/thalles)
