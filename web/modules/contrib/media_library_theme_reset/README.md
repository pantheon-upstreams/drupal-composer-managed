Media Library Theme Reset
-------------------------

### Summary
This module fixes theme related problems related to using Layout Builder in conjunction with the core Media Library.

### About this module
Layout Builder greatly improves the content editing experience by moving content creation and layout into the active theme layer.

However, because editing takes place with the default theme, rather than the admin theme, the core Media Library, when used in the context of Layout Builder, will also use the default theme. Since most front-end themes are not designed for Drupal form editing, they end up looking insufficient.

The ideal solution would be to use a Drupal admin theme in Layout Builder. However, this is currently not possible (see [#3042907] and [#3050508]).

Rather than requiring themes to add additional logic to display Drupal forms sufficiently, this module takes on the responsibility of replicating Seven's CSS and attaching it, as a library, in the context of the Media Library, when accessed via Layout Builder.

<img src="https://www.drupal.org/files/media_library_theme_reset.png">